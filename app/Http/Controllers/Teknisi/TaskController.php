<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Report;
use App\Models\Resolution;
use Illuminate\Support\Facades\Storage;
use App\Notifications\ReportCompletedByTechnician;
use App\Models\Complaint;

class TaskController extends Controller
{
    /**
     * Menampilkan dashboard Teknisi dengan daftar tugas
     */
    public function index(Request $request)
    {
        $teknisiId = Auth::id();
        $dateFrom  = $request->query('date_from');
        $dateTo    = $request->query('date_to');
        $kategori  = $request->query('kategori');
        $search    = $request->query('search');

        /**
         * ✅ 1) ACTIVE TASKS (yang bisa dikerjakan)
         * - accepted: siap dikerjakan (termasuk rework setelah komplain approved)
         * - on_process: sedang dikerjakan
         * (opsional) hold: kalau mau tampil juga di teknisi
         *
         * ❗ complain dipisah karena itu "menunggu keputusan Kepala IT"
         */
        $tasksActiveQuery = Report::where('assigned_technician_id', $teknisiId)
            ->whereIn('status', ['accepted', 'on_process'])
            ->with('reporter')
            ->orderBy('status', 'asc');

        /**
         * ✅ 2) TASKS MENUNGGU KEPUTUSAN KOMPLAIN (status complain)
         * Teknisi sebaiknya cuma lihat, bukan lanjut kerja.
         */
        $tasksWaitingComplaintQuery = Report::where('assigned_technician_id', $teknisiId)
            ->where('status', 'complain')
            ->with('reporter')
            ->orderBy('updated_at', 'desc');

        /**
         * ✅ 3) CLAIMABLE TASKS (status accepted dan belum ada teknisi)
         */
        $tasksClaimableQuery = Report::where('status', 'accepted')
            ->whereNull('assigned_technician_id')
            ->with('reporter')
            ->orderBy('created_at', 'asc');

        // Apply filters to all queries
        foreach ([$tasksActiveQuery, $tasksWaitingComplaintQuery, $tasksClaimableQuery] as $query) {
            if ($dateFrom) {
                $query->where('tanggal_pengajuan', '>=', \Carbon\Carbon::parse($dateFrom)->startOfDay());
            }
            if ($dateTo) {
                $query->where('tanggal_pengajuan', '<=', \Carbon\Carbon::parse($dateTo)->endOfDay());
            }
            if ($kategori) {
                $query->where('kategori', $kategori);
            }
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('deskripsi_pengajuan', 'like', "%{$search}%")
                        ->orWhere('kategori', 'like', "%{$search}%")
                        ->orWhere('nama_pelapor', 'like', "%{$search}%");
                });
            }
        }

        $tasksActive          = $tasksActiveQuery->get();
        $tasksWaitingComplaint = $tasksWaitingComplaintQuery->get();
        $tasksClaimable       = $tasksClaimableQuery->get();

        // Statistics
        $totalReports = Report::where('assigned_technician_id', $teknisiId)->count();
        $pendingReports = Report::where('assigned_technician_id', $teknisiId)->where('status', 'pending')->count();

        $avgDurationMinutes = Report::where('assigned_technician_id', $teknisiId)
            ->whereIn('status', ['completed', 'rated'])
            ->whereNotNull('duration_minutes')
            ->avg('duration_minutes');

        if ($avgDurationMinutes === null) {
            $avgDurationLabel = '—';
        } else {
            $avg = (int) round($avgDurationMinutes);
            if ($avg >= 60) {
                $hours = floor($avg / 60);
                $minutes = $avg % 60;
                $avgDurationLabel = $hours . 'h' . ($minutes > 0 ? ' ' . $minutes . 'm' : '');
            } else {
                $avgDurationLabel = $avg . 'm';
            }
        }

        $categories = Report::select('kategori')->distinct()->orderBy('kategori')->pluck('kategori');

        return view('teknisi.dashboard', [
            'tasksActive' => $tasksActive,
            'tasksWaitingComplaint' => $tasksWaitingComplaint, // ✅ tambahan (kalau belum dipakai view, aman)
            'tasksClaimable' => $tasksClaimable,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'kategori' => $kategori,
            'search' => $search,
            'categories' => $categories,
            'totalReports' => $totalReports,
            'pendingReports' => $pendingReports,
            'avgDurationLabel' => $avgDurationLabel,
        ]);
    }

    /**
     * Memulai pekerjaan (mengubah status ke 'on_process' dan set timer)
     */
    public function start(Report $report)
    {
        if ($report->assigned_technician_id != Auth::id()) {
            abort(403, 'ANDA TIDAK PUNYA AKSES KE TUGAS INI.');
        }

        // ✅ optional guard biar tidak start saat complain / sudah on_process
        if ($report->status !== 'accepted') {
            return redirect()->route('teknisi.dashboard')
                ->with('error', 'Tugas ini tidak bisa dimulai. Status harus "accepted".');
        }

        $report->update([
            'status' => 'on_process',
            'start_time' => now()
        ]);

        return redirect()->route('teknisi.dashboard')
            ->with('success', 'Pekerjaan telah dimulai!');
    }

    public function completeForm(Report $report)
    {
        if ($report->assigned_technician_id != Auth::id()) {
            abort(403, 'ANDA TIDAK PUNYA AKSES KE TUGAS INI.');
        }

        if ($report->status != 'on_process') {
            return redirect()->route('teknisi.dashboard')->with('error', 'Pekerjaan ini belum dimulai.');
        }

        return view('teknisi.complete', [
            'report' => $report,
        ]);
    }

    public function storeCompletion(Request $request, Report $report)
    {
        if ($report->assigned_technician_id != Auth::id() || $report->status != 'on_process') {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }

        $validated = $request->validate([
            'barang' => 'nullable|string|max:255',
            'qty' => 'nullable|integer|min:0',
            'deskripsi_pekerjaan' => 'required|string',
            'foto_before' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'foto_after' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $pathBefore = $request->file('foto_before')->store('resolutions', 'public');
        $pathAfter  = $request->file('foto_after')->store('resolutions', 'public');

        $endTime = now();
        $durationMinutes = $report->start_time?->diffInMinutes($endTime) ?? 0;

        $report->update([
            'status' => 'completed',
            'end_time' => $endTime,
            'duration_minutes' => $durationMinutes,
        ]);

        Resolution::updateOrCreate(
            ['report_id' => $report->id],
            [
                'barang' => $validated['barang'] ?? null,
                'qty' => $validated['qty'] ?? null,
                'deskripsi_pekerjaan' => $validated['deskripsi_pekerjaan'],
                'foto_before' => $pathBefore,
                'foto_after' => $pathAfter,
                'completion_date' => now(),
            ]
        );

        $report->refresh();
        if ($report->reporter) {
            $report->reporter->notify(new ReportCompletedByTechnician($report));
        }

        return redirect()->route('teknisi.dashboard')
            ->with('success', 'Pekerjaan telah selesai dan laporan terkirim!');
    }

    public function claim(Report $report)
    {
        if (!in_array($report->status, ['pending', 'accepted']) || $report->assigned_technician_id !== null) {
            return redirect()->route('teknisi.dashboard')->with('error', 'Tugas ini tidak tersedia untuk diklaim.');
        }

        $report->update([
            'assigned_technician_id' => Auth::id(),
            'status' => 'on_process',
            'start_time' => now()
        ]);

        return redirect()->route('teknisi.dashboard')->with('success', 'Tugas berhasil diklaim dan telah dimulai!');
    }

    public function show(Report $report)
    {
        if ($report->assigned_technician_id != Auth::id()) {
            abort(403, 'ANDA TIDAK PUNYA AKSES KE TUGAS INI.');
        }

        if (!in_array($report->status, ['completed', 'rated'])) {
            return redirect()->route('teknisi.dashboard')->with('error', 'Tugas ini belum selesai.');
        }

        $report->load('resolution', 'reporter');

        return view('teknisi.show', [
            'report' => $report
        ]);
    }

    public function detail(Report $report)
    {
        if ($report->assigned_technician_id != Auth::id()) {
            abort(403, 'ANDA TIDAK PUNYA AKSES KE TUGAS INI.');
        }

        $report->load(['resolution', 'reporter']);

        // Komplain aktif (belum diputus)
        $activeComplaint = Complaint::where('report_id', $report->id)
            ->where('status', 'submitted')
            ->latest('sequence')
            ->first();

        // Komplain terakhir yang SUDAH diputus
        $lastDecidedComplaint = Complaint::where('report_id', $report->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->latest('sequence')
            ->first();

        return view('teknisi.detail', [
            'report' => $report,
            'activeComplaint' => $activeComplaint,
            'lastDecidedComplaint' => $lastDecidedComplaint,
        ]);
    }

    public function history(Request $request)
    {
        $teknisiId = Auth::id();
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $kategori = $request->query('kategori');
        $filterStatus = $request->query('filter_status');

        $tasksHistoryQuery = Report::where('assigned_technician_id', $teknisiId)
            ->whereIn('status', ['completed', 'rated'])
            ->with('reporter')
            ->orderBy('end_time', 'desc');

        if ($dateFrom) {
            $tasksHistoryQuery->where('tanggal_pengajuan', '>=', \Carbon\Carbon::parse($dateFrom)->startOfDay());
        }
        if ($dateTo) {
            $tasksHistoryQuery->where('tanggal_pengajuan', '<=', \Carbon\Carbon::parse($dateTo)->endOfDay());
        }

        if (!empty($kategori)) {
            $tasksHistoryQuery->where('kategori', $kategori);
        }

        if (!empty($filterStatus)) {
            $tasksHistoryQuery->where('status', $filterStatus);
        }

        $tasksHistory = $tasksHistoryQuery->paginate(10);

        $categories = Report::select('kategori')->distinct()->orderBy('kategori')->pluck('kategori');

        return view('teknisi.history', [
            'tasksHistory' => $tasksHistory,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'kategori' => $kategori,
            'filterStatus' => $filterStatus,
            'categories' => $categories,
        ]);
    }
}
