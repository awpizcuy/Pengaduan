<?php

namespace App\Http\Controllers\HeadIT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Models\Report;
use App\Models\User;
use App\Models\Complaint;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\CarbonInterval;

use App\Notifications\ReportAssignedToYou;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HeadITCompletedReportsExport;

class ReportController extends Controller
{
    /**
     * Menampilkan dashboard Kepala IT dengan semua laporan, grafik, dan fitur pencarian
     */
    public function index(Request $request)
    {
        $searchTerm     = $request->query('search');
        $filterStatus   = $request->query('filter_status');
        $filterCategory = $request->query('filter_category');
        $dateFrom       = $request->query('date_from');

        $range         = $request->query('range', '12_months');
        $year          = $request->query('year');
        $selectedMonth = $request->query('month'); // format: Y-m (mis: 2025-11)

        $query = Report::with('reporter');

        if ($filterStatus) {
            $query->where('status', $filterStatus);
        }

        if ($filterCategory) {
            $query->where('kategori', $filterCategory);
        }

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_pelapor', 'like', '%' . $searchTerm . '%')
                    ->orWhere('kategori', 'like', '%' . $searchTerm . '%')
                    ->orWhere('deskripsi_pengajuan', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('reporter', function ($reporterQuery) use ($searchTerm) {
                        $reporterQuery->where('email', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        if ($dateFrom) {
            $query->whereDate('tanggal_pengajuan', '>=', $dateFrom);
        }

        $reports = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // === PENGUMPULAN DATA KARTU (STATISTIK) ===
        $totalReports   = Report::count();
        $pendingReports = Report::where('status', 'pending')->count();

        $averageMinutes = Report::whereIn('status', ['completed', 'rated'])
            ->whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, start_time, end_time)) as avg_duration'))
            ->value('avg_duration');

        $averageCompletionTime = "N/A";

        if ($averageMinutes && $averageMinutes > 0) {
            $totalMinutes = round($averageMinutes);
            $averageCompletionTime = CarbonInterval::minutes($totalMinutes)
                ->cascade()
                ->forHumans(['short' => true]);
        }

        // === DATA UNTUK CHART ===

        $statusCounts = Report::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // 2) Query dasar untuk laporan selesai per HARI
        $completedDailyQuery = Report::select(
                DB::raw('DATE(tanggal_pengajuan) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->whereIn('status', ['completed', 'rated'])
            ->whereNotNull('tanggal_pengajuan');

        if ($range === '12_months') {
            $completedDailyQuery->whereDate(
                'tanggal_pengajuan',
                '>=',
                now()->subMonths(11)->startOfMonth()
            );
        } elseif ($range === 'year' && $year) {
            $completedDailyQuery->whereYear('tanggal_pengajuan', $year);
        }

        if ($selectedMonth) {
            $completedDailyQuery->whereRaw(
                'DATE_FORMAT(tanggal_pengajuan, "%Y-%m") = ?',
                [$selectedMonth]
            );
        }

        $completedPerDay = $completedDailyQuery
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dailyLabels = $completedPerDay->pluck('date');
        $dailyTotals = $completedPerDay->pluck('total');

        // 3) Ringkasan per BULAN
        $completedMonthlyQuery = Report::select(
                DB::raw('DATE_FORMAT(tanggal_pengajuan, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereIn('status', ['completed', 'rated'])
            ->whereNotNull('tanggal_pengajuan');

        if ($range === '12_months') {
            $completedMonthlyQuery->whereDate(
                'tanggal_pengajuan',
                '>=',
                now()->subMonths(11)->startOfMonth()
            );
        } elseif ($range === 'year' && $year) {
            $completedMonthlyQuery->whereYear('tanggal_pengajuan', $year);
        }

        $completedPerMonth = $completedMonthlyQuery
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlySummary = $completedPerMonth->pluck('total', 'month');

        if ($selectedMonth && $completedPerDay->count()) {
            $lineMode   = 'daily';
            $lineLabels = $dailyLabels->values();
            $lineData   = $dailyTotals->values();
        } else {
            $lineMode   = 'monthly';
            $lineLabels = $monthlySummary->keys()->values();
            $lineData   = $monthlySummary->values();
        }

        $lineMax = $lineData->max() ?? 0;

        $categoryCounts = Report::select('kategori', DB::raw('COUNT(*) as total'))
            ->groupBy('kategori')
            ->pluck('total', 'kategori');

        return view('kepala-it.dashboard', [
            'reports'               => $reports,
            'totalReports'          => $totalReports,
            'pendingReports'        => $pendingReports,
            'averageCompletionTime' => $averageCompletionTime,

            'searchTerm'     => $searchTerm,
            'filterStatus'   => $filterStatus,
            'filterCategory' => $filterCategory,
            'dateFrom'       => $dateFrom,

            'range'          => $range,
            'year'           => $year,
            'selectedMonth'  => $selectedMonth,

            'statusCounts'   => $statusCounts,
            'monthlySummary' => $monthlySummary,
            'lineLabels'     => $lineLabels,
            'lineData'       => $lineData,
            'lineMax'        => $lineMax,
            'lineMode'       => $lineMode,
            'categoryCounts' => $categoryCounts,
        ]);
    }

    /**
     * Menampilkan halaman untuk me-manage laporan
     */
    public function manage(Report $report)
    {
        $report->load(['reporter', 'technician', 'resolution']);

        // ambil komplain aktif (yang belum diputuskan) untuk report ini
        $activeComplaint = \App\Models\Complaint::where('report_id', $report->id)
            ->where('status', 'submitted')
            ->latest('sequence')
            ->first();

        // daftar teknisi (hanya aktif, tapi tetap tampilkan teknisi yg sedang assigned)
        $technicians = User::where('role', 'teknisi')
            ->where(function ($q) use ($report) {
                $q->where('is_active', true)
                  ->orWhere('id', $report->assigned_technician_id);
            })
            ->get();

        return view('kepala-it.manage', [
            'report'          => $report,
            'technicians'     => $technicians,
            'activeComplaint' => $activeComplaint,
        ]);
    }

    /**
     * Menyimpan perubahan dari halaman manage (untuk laporan normal / review awal)
     * ⚠️ Tidak dipakai untuk memutus komplain
     */
    public function update(Request $request, Report $report)
    {
        // ✅ blokir kalau report sedang status complain (kalau kamu pakai flow komplain)
        if ($report->status === 'complain') {
            return back()->with('error', 'Report ini sedang dalam status KOMPLAIN. Gunakan tombol Putus Komplain (Setujui/Tolak).');
        }

        $validated = $request->validate([
            'status'      => ['required', Rule::in(['accepted', 'hold', 'rejected'])],
            'status_note' => ['nullable', 'string', 'required_if:status,hold', 'required_if:status,rejected'],

            'assigned_technician_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($q) {
                    $q->where('role', 'teknisi')
                      ->where('is_active', true);
                }),
            ],

            // ✅ tambahan untuk WhatsApp (tidak mengganggu kalau tidak dipakai)
            'send_whatsapp'    => ['nullable', 'boolean'],
            'whatsapp_message' => ['nullable', 'string', 'max:2000'],
        ]);

        $updateData = [
            'status'                 => $validated['status'],
            'status_note'            => $validated['status_note'] ?? null,
            'assigned_technician_id' => $validated['assigned_technician_id'] ?? null,
        ];

        $report->update($updateData);

        // ✅ Jika status accepted + ada penugasan -> notif + WA link
        if ($validated['status'] === 'accepted' && !empty($validated['assigned_technician_id'])) {
            $technician = User::find($validated['assigned_technician_id']);

            if ($technician) {
                $report->refresh();
                $technician->notify(new ReportAssignedToYou($report));

                // ✅ nomor telp teknisi dari kolom no_telp
                $waPhone = $this->normalizePhoneToWa($technician->no_telp);

                // ✅ pesan WhatsApp: ambil dari form kalau ada (hidden input), kalau tidak ada pakai default
                $msg = $request->input('whatsapp_message');

                if (!$msg) {
                    $msg =
                        "🛠️ Penugasan Tugas\n" .
                        "Laporan #{$report->id} ({$report->kategori})\n" .
                        "Pelapor: " . ($report->nama_pelapor ?? '-') . "\n" .
                        "Masalah: " . \Illuminate\Support\Str::limit($report->deskripsi_pengajuan, 160);
                }

                $host = $request->getHost();
                $isLocalHost = in_array($host, ['127.0.0.1', 'localhost']);
                if (!$isLocalHost) {
                    $msg .= "\nLink detail: " . route('teknisi.task.show', $report);
                } else {
                    $msg .= "\n\nCatatan: Sistem masih lokal. Silakan cek detail tugas via Dashboard Teknisi setelah login.";
                }

                $waUrl = $waPhone
                    ? "https://wa.me/{$waPhone}?text=" . urlencode($msg)
                    : "https://wa.me/?text=" . urlencode($msg);

                return redirect()
                    ->route('kepala.report.manage', $report)
                    ->with('success', 'Status laporan berhasil diperbarui & teknisi sudah dinotifikasi.')
                    ->with('wa_url', $waUrl)
                    ->with('auto_open_wa', $request->boolean('send_whatsapp')); // ✅ flag
            }
        }

        return redirect()->route('kepala.report.manage', $report)
            ->with('success', 'Status laporan telah berhasil diperbarui!');
    }

    /**
     * ✅ Kepala IT menyetujui komplain
     * Komplain -> approved
     * Report -> accepted (dibuka ulang untuk dikerjakan teknisi)
     */
    public function approveComplaint(Request $request, Report $report, Complaint $complaint)
    {
        if ($complaint->report_id !== $report->id) {
            abort(404);
        }

        if ($complaint->status !== 'submitted') {
            return back()->with('error', 'Komplain ini sudah pernah diputuskan.');
        }

        $data = $request->validate([
            'decision_note' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($report, $complaint, $data) {
            // ✅ 1) Update data komplain (tetap sama)
            $complaint->update([
                'status'        => 'approved',
                'decision_note' => $data['decision_note'] ?? null,
                'decided_by'    => Auth::id(),
                'decided_at'    => now(),
            ]);

            // ✅ 2) Update report (logika sama: status jadi accepted)
            // Perubahan: keputusan komplain masuk ke complaint_decision_note (bukan status_note)
            $report->update([
                'status' => 'accepted',

                // field baru Opsi B:
                'complaint_decision_note' => trim(
                    "Komplain {$complaint->complaint_no} disetujui. " . ($data['decision_note'] ?? '')
                ),

                // status_note tidak disentuh agar khusus untuk hold/reject normal
                // 'status_note' => ... (DIHILANGKAN)
            ]);
        });

        return redirect()
            ->route('kepala.report.manage', $report)
            ->with('success', "Komplain {$complaint->complaint_no} disetujui. Laporan dibuka kembali untuk dikerjakan ulang.");
    }

    /**
     * ✅ Kepala IT menolak komplain
     * Komplain -> rejected
     * Report -> completed (tetap dianggap selesai, bukan rejected)
     */
    public function rejectComplaint(Request $request, Report $report, Complaint $complaint)
    {
        if ($complaint->report_id !== $report->id) {
            abort(404);
        }

        if ($complaint->status !== 'submitted') {
            return back()->with('error', 'Komplain ini sudah pernah diputuskan.');
        }

        $data = $request->validate([
            'decision_note' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($report, $complaint, $data) {
            // ✅ 1) Update data komplain (tetap sama)
            $complaint->update([
                'status'        => 'rejected',
                'decision_note' => $data['decision_note'] ?? null,
                'decided_by'    => Auth::id(),
                'decided_at'    => now(),
            ]);

            // ✅ 2) Update report (logika sama: status jadi completed)
            // Perubahan: keputusan komplain masuk ke complaint_decision_note (bukan status_note)
            $report->update([
                'status' => 'completed',

                // field baru Opsi B:
                'complaint_decision_note' => trim(
                    "Komplain {$complaint->complaint_no} ditolak. " . ($data['decision_note'] ?? '')
                ),

                // status_note tidak disentuh agar khusus untuk hold/reject normal
                // 'status_note' => ... (DIHILANGKAN)
            ]);
        });

        return redirect()
            ->route('kepala.report.manage', $report)
            ->with('success', "Komplain {$complaint->complaint_no} ditolak. Status laporan dikembalikan ke selesai.");
    }

    public function printView(Report $report)
    {
        if (!in_array($report->status, ['completed', 'rated'])) {
            return redirect()->route('kepala.report.manage', $report)
                ->with('error', 'Hanya laporan yang telah selesai yang dapat dicetak.');
        }

        $report->load(['reporter', 'technician', 'resolution']);

        return view('kepala-it.report-print', [
            'report' => $report,
        ]);
    }

    public function printAllCompleted()
    {
        $reports = Report::with(['reporter', 'technician', 'resolution'])
            ->whereIn('status', ['completed', 'rated'])
            ->whereHas('resolution', function ($query) {
                $query->whereNotNull('rating');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        if ($reports->isEmpty()) {
            return redirect()->route('kepala.dashboard')
                ->with('error', 'Tidak ada laporan selesai yang memiliki rating untuk dicetak.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('kepala-it.reports_all_pdf', [
            'reports' => $reports,
        ]);

        return $pdf->stream('semua-laporan-selesai.pdf');
    }

    public function exportAllCompletedExcel(Request $request)
    {
        return Excel::download(
            new HeadITCompletedReportsExport(
                $request->query('date_from'),
                $request->query('date_to'),
                $request->query('search'),
                $request->query('filter_category'),
                $request->query('filter_status'),
            ),
            'laporan-selesai-kepala-it.xlsx'
        );
    }

    /**
     * Helper utama yang sudah kamu buat (tetap dipertahankan)
     */
    private function toWhatsappNumber(?string $phone): ?string
    {
        if (!$phone) return null;

        // ambil angka saja
        $phone = preg_replace('/\D+/', '', $phone);

        // contoh: 08xxxx -> 628xxxx
        if (str_starts_with($phone, '08')) {
            $phone = '62' . substr($phone, 1);
        }

        // kalau sudah 62xxxx biarkan
        if (str_starts_with($phone, '62')) {
            return $phone;
        }

        return null; // format tidak dikenali
    }

    /**
     */
    private function normalizePhoneToWa(?string $phone): ?string
    {
        return $this->toWhatsappNumber($phone);
    }
}
