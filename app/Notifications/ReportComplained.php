<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\Report;
use App\Models\Complaint;

class ReportComplained extends Notification implements ShouldQueue
{
    use Queueable;

    protected Report $report;
    protected ?Complaint $complaint;

    /**
     * Create a new notification instance.
     * Complaint dibuat optional supaya aman jika suatu saat ada pemanggilan lama.
     */
    public function __construct(Report $report, ?Complaint $complaint = null)
    {
        $this->report = $report;
        $this->complaint = $complaint;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $reportId = $this->report->id;

        // Fallback nama complainer (hindari error kalau reporter belum di-load)
        $complainerName = optional($this->report->reporter)->name
            ?? $this->report->nama_pelapor
            ?? '-';

        // URL berdasarkan role penerima notifikasi
        $url = '#';
        if (($notifiable->role ?? null) === 'kepala_it') {
            $url = route('kepala.report.manage', $this->report);
        } elseif (($notifiable->role ?? null) === 'teknisi') {
            $url = route('teknisi.task.show', $this->report);
        }

        // Data komplain (optional)
        $complaintId = $this->complaint?->id;
        $complaintNo = $this->complaint?->complaint_no;
        $seq         = $this->complaint?->sequence;

        // Pesan lebih informatif jika ada nomor komplain
        if ($complaintNo && $seq) {
            $message = "Komplain {$complaintNo} (ke-{$seq}) untuk Laporan #{$reportId} ({$this->report->kategori}) oleh {$complainerName}. Mohon ditindaklanjuti.";
        } else {
            $message = "Laporan #{$reportId} ({$this->report->kategori}) dikomplain oleh {$complainerName}. Mohon ditindaklanjuti.";
        }

        return [
            'report_id'     => $reportId,

            // disimpan kalau tersedia (kalau null pun aman)
            'complaint_id'  => $complaintId,
            'complaint_no'  => $complaintNo,
            'sequence'      => $seq,

            'title'   => 'Laporan Dikomplain',
            'message' => $message,
            'url'     => $url,
        ];
    }
}
