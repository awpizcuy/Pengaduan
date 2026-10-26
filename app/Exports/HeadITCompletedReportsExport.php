<?php

namespace App\Exports;

use App\Models\Report;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\{
    FromQuery, WithHeadings, WithMapping, ShouldAutoSize
};

class HeadITCompletedReportsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        protected ?string $dateFrom = null,
        protected ?string $dateTo = null,
        protected ?string $search = null,
        protected ?string $filterCategory = null,
        protected ?string $filterStatus = null,
    ) {}

    public function query(): Builder
    {
        $q = Report::query()
            ->with(['reporter', 'technician'])
            // default export laporan selesai
            ->whereIn('status', ['completed', 'rated']);

        // kalau mau bisa export sesuai filter status juga (optional)
        if ($this->filterStatus) {
            $q->where('status', $this->filterStatus);
        }

        if ($this->dateFrom) {
            $q->whereDate('tanggal_pengajuan', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $q->whereDate('tanggal_pengajuan', '<=', $this->dateTo);
        }
        if ($this->filterCategory) {
            $q->where('kategori', $this->filterCategory);
        }
        if ($this->search) {
            $s = $this->search;
            $q->where(function ($sub) use ($s) {
                $sub->where('nama_pelapor', 'like', "%{$s}%")
                    ->orWhere('kategori', 'like', "%{$s}%")
                    ->orWhere('deskripsi_pengajuan', 'like', "%{$s}%")
                    ->orWhereHas('reporter', fn($rq) => $rq->where('email', 'like', "%{$s}%"));
            });
        }

        return $q->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tanggal Pengajuan',
            'Pelapor',
            'Email Pelapor',
            'Kategori',
            'Deskripsi',
            'Status',
            'Teknisi',
            'Komplain Ke-',
            'Durasi (menit)',
            'Rating',
            'Feedback Rating',
        ];
    }

    public function map($report): array
    {
        return [
            $report->id,
            $report->tanggal_pengajuan ? \Carbon\Carbon::parse($report->tanggal_pengajuan)->format('Y-m-d') : '',
            $report->nama_pelapor,
            $report->reporter?->email,
            $report->kategori,
            $report->deskripsi_pengajuan,
            $report->status,
            $report->technician?->name,
            $report->complaint_count ?? 0,
            $report->duration_minutes,
            $report->rating,
            $report->rating_feedback,
        ];
    }
}
