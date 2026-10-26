<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Report;

class ReportStatusUpdated extends Notification
{
    use Queueable;

    protected $report;

    /**
     * Create a new notification instance.
     */
    public function __construct(Report $report)
    {
        $this->report = $report;
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
        $statusText = [
            'accepted' => 'diterima',
            'rejected' => 'ditolak',
            'hold' => 'ditunda',
        ];
        $currentStatus = $statusText[$this->report->status] ?? $this->report->status;

        return [
            'report_id' => $this->report->id,
            'title' => 'Status Laporan Diperbarui',
            'message' => "Laporan Anda #{$this->report->id} ({$this->report->kategori}) telah diubah statusnya menjadi '{$currentStatus}'.",
            'url' => route('admin.report.show', $this->report->id),
        ];
    }
}