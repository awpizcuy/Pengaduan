<?php

namespace App\Http\Controllers\AdminGedung;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Report;
use App\Models\User;
use App\Models\Complaint;

use App\Notifications\NewReportSubmitted;
use App\Notifications\ReportComplained;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $filterStatus = $request->query('filter_status');
        $filterCategory = $request->query('filter_category');
        $search = $request->query('search');

        $query = Report::with('latestComplaint')
            ->where('user_id', Auth::id())
            ->whereNotIn('status', ['completed', 'rated', 'rejected']);

        if ($dateFrom) {
            $query->where('tanggal_pengajuan', '>=', \Carbon\Carbon::parse($dateFrom)->startOfDay());
        }
        if ($dateTo) {
            $query->where('tanggal_pengajuan', '<=', \Carbon\Carbon::parse($dateTo)->endOfDay());
        }

        if ($filterStatus && in_array($filterStatus, ['pending', 'accepted', 'on_process', 'hold', 'complain'])) {
            $query->where('status', $filterStatus);
        }

        if ($filterCategory) {
            $query->where('kategori', $filterCategory);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('deskripsi_pengajuan', 'like', "%{$search}%")
                    ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        // ✅ Recommended: paginate biar aman kalau data banyak
        $laporan = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $totalReports = Report::where('user_id', Auth::id())->count();
        $pendingReports = Report::where('user_id', Auth::id())->where('status', 'pending')->count();
        $completedReports = Report::where('user_id', Auth::id())->whereIn('status', ['completed', 'rated'])->count();
        $rejectedReports = Report::where('user_id', Auth::id())->where('status', 'rejected')->count();

        return view('admin-gedung.dashboard', [
            'laporan' => $laporan,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'filterStatus' => $filterStatus,
            'filterCategory' => $filterCategory,
            'search' => $search,
            'totalReports' => $totalReports,
            'pendingReports' => $pendingReports,
            'completedReports' => $completedReports,
            'rejectedReports' => $rejectedReports,
        ]);
    }

    public function create()
    {
        return view('admin-gedung.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori' => 'required|string|in:peminjaman,instalasi,kerusakan',
            'nama_pelapor' => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'deskripsi_pengajuan' => 'required|string',
            'foto_awal' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $reportData = [
            'user_id' => Auth::id(),
            'kategori' => $validated['kategori'],
            'nama_pelapor' => $validated['nama_pelapor'],
            'no_telp' => $validated['no_telp'] ?? null,
            'tanggal_pengajuan' => now(),
            'deskripsi_pengajuan' => $validated['deskripsi_pengajuan'],
            'status' => 'pending'
        ];

        if ($request->hasFile('foto_awal') && in_array($validated['kategori'], ['peminjaman', 'instalasi', 'kerusakan'])) {
            $reportData['foto_awal'] = $request->file('foto_awal')->store('reports/initial', 'public');
        }

        $newReport = Report::create($reportData);

        $kepalaITUsers = User::where('role', 'kepala_it')->get();
        foreach ($kepalaITUsers as $user) {
            $user->notify(new NewReportSubmitted($newReport));
        }

        return redirect()->route('admin.dashboard')
            ->with('success', 'Laporan Anda telah berhasil dikirim!');
    }

    public function rateForm(Report $report)
    {
        if ($report->user_id != Auth::id()) {
            abort(403, 'ANDA TIDAK PUNYA AKSES KE LAPORAN INI.');
        }

        if ($report->status != 'completed') {
            return redirect()->route('admin.dashboard')->with('error', 'Laporan ini belum selesai dikerjakan.');
        }

        $report->load('resolution');

        return view('admin-gedung.rate', [
            'report' => $report
        ]);
    }

    public function storeRating(Request $request, Report $report)
    {
        if ($report->user_id != Auth::id() || $report->status != 'completed') {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'rating_feedback' => 'nullable|string',
        ]);

        $report->update([
            'rating' => $validated['rating'],
            'rating_feedback' => $validated['rating_feedback'],
            'status' => 'rated'
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Rating telah berhasil diberikan. Terima kasih!');
    }

    public function storeComplaint(Request $request, Report $report)
    {
        if ($report->user_id != Auth::id()) {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }

        if (!in_array($report->status, ['completed', 'rated'])) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Komplain hanya bisa diajukan ketika laporan sudah selesai (completed/rated).');
        }

        $validated = $request->validate([
            'complaint_note' => 'required|string|min:10',
        ]);

        try {
            $complaint = DB::transaction(function () use ($report, $validated) {

                $nextSeq = (int) Complaint::where('report_id', $report->id)
                    ->lockForUpdate()
                    ->max('sequence') + 1;

                $complaintNo = sprintf('CMP-%06d-%03d', $report->id, $nextSeq);

                $newComplaint = Complaint::create([
                    'report_id'    => $report->id,
                    'created_by'   => Auth::id(),
                    'sequence'     => $nextSeq,
                    'complaint_no' => $complaintNo,
                    'message'      => $validated['complaint_note'],
                    'status'       => 'submitted',
                ]);

                $report->update([
                    'status'            => 'complain',
                    'complaint_note'    => $validated['complaint_note'],
                    'rating'            => null,
                    'rating_feedback'   => null,
                    'complaint_count'   => $nextSeq,
                    'last_complaint_at' => now(),
                ]);

                return $newComplaint;
            });

            $kepalaITUsers = User::where('role', 'kepala_it')->get();
            $report->load('technician');
            $technician = $report->technician;

            foreach ($kepalaITUsers as $user) {
                $user->notify(new ReportComplained($report, $complaint));
            }

            if ($technician) {
                $technician->notify(new ReportComplained($report, $complaint));
            }

            return redirect()->route('admin.dashboard')
                ->with('success', "Komplain berhasil dikirim. Nomor Komplain: {$complaint->complaint_no}");

        } catch (\Exception $e) {
            Log::error('Gagal membuat komplain: ' . $e->getMessage());

            return redirect()->route('admin.dashboard')
                ->with('error', 'Terjadi kesalahan saat mengirim komplain. Silakan coba lagi.');
        }
    }

    public function show(Report $report)
    {
        if ($report->user_id != Auth::id()) {
            abort(403, 'ANDA TIDAK PUNYA AKSES KE LAPORAN INI.');
        }

        $report->load([
            'resolution',
            'technician',
            'latestComplaint',
        ]);

        $latestComplaint = $report->latestComplaint;

        return view('admin-gedung.show', [
            'report' => $report,
            'latestComplaint' => $latestComplaint,
        ]);
    }

    public function history(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $filterStatus = $request->query('filter_status');
        $filterCategory = $request->query('filter_category');
        $search = $request->query('search');

        $query = Report::with('latestComplaint')
            ->where('user_id', Auth::id())
            ->whereIn('status', ['completed', 'rated', 'rejected']);

        if ($dateFrom) {
            $query->where('tanggal_pengajuan', '>=', \Carbon\Carbon::parse($dateFrom)->startOfDay());
        }
        if ($dateTo) {
            $query->where('tanggal_pengajuan', '<=', \Carbon\Carbon::parse($dateTo)->endOfDay());
        }

        if ($filterStatus && in_array($filterStatus, ['completed', 'rated', 'rejected'])) {
            $query->where('status', $filterStatus);
        }

        if ($filterCategory) {
            $query->where('kategori', $filterCategory);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('deskripsi_pengajuan', 'like', "%{$search}%")
                    ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        $laporan = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin-gedung.history', [
            'laporan' => $laporan,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'filterStatus' => $filterStatus,
            'filterCategory' => $filterCategory,
            'search' => $search,
        ]);
    }
}
