<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// ✅ Tambahan import yang wajib
use App\Models\User;
use App\Models\Complaint;
use App\Models\Resolution;

class Report extends Model
{
    use HasFactory;

    /**
     * Daftar status yang dipakai di aplikasi.
     * (Opsional, tapi membantu konsistensi & mengurangi typo.)
     */
    public const STATUS_PENDING    = 'pending';
    public const STATUS_ACCEPTED   = 'accepted';
    public const STATUS_ON_PROCESS = 'on_process';
    public const STATUS_HOLD       = 'hold';
    public const STATUS_REJECTED   = 'rejected';
    public const STATUS_COMPLETED  = 'completed';
    public const STATUS_RATED      = 'rated';
    public const STATUS_COMPLAIN   = 'complain';

    protected $casts = [
        'tanggal_pengajuan' => 'datetime',
        'start_time'        => 'datetime',
        'end_time'          => 'datetime',
        'last_complaint_at' => 'datetime',
        'decided_at'        => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'kategori',
        'nama_pelapor',
        'no_telp',
        'tanggal_pengajuan',
        'deskripsi_pengajuan',
        'foto_awal',
        'status',
        'status_note',
        'assigned_technician_id',
        'start_time',
        'end_time',
        'duration_minutes',
        'rating',
        'rating_feedback',
        'complaint_note',
        'complaint_count',
        'last_complaint_at',
        'complaint_decision_note',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'assigned_technician_id');
    }

    public function resolution()
    {
        return $this->hasOne(Resolution::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    /**
     * Komplain terakhir berdasarkan kolom sequence.
     * Bisa dipakai langsung di blade + bisa di eager-load untuk menghindari N+1.
     */
    public function latestComplaint()
    {
        return $this->hasOne(Complaint::class)->latestOfMany('sequence');
    }

    /**
     * Catatan yang ditampilkan:
     * - Prioritas: complaint_decision_note (keputusan komplain)
     * - Fallback  : status_note (catatan hold/reject)
     */
    public function getDisplayNoteAttribute(): string
    {
        return trim((string) ($this->complaint_decision_note ?: ($this->status_note ?? '')));
    }

    /**
     * True jika catatan yang tampil berasal dari keputusan komplain.
     */
    public function getIsComplaintDecisionAttribute(): bool
    {
        return trim((string) ($this->complaint_decision_note ?? '')) !== '';
    }

    /**
     * Pecah display_note menjadi [title, reason, use_split] berdasarkan titik pertama.
     * Berguna untuk UI yang menampilkan konteks kecil + alasan utama.
     */
    public function getDisplayNotePartsAttribute(): array
    {
        $note = $this->display_note;

        if ($note === '') {
            return [
                'title'     => '',
                'reason'    => '',
                'use_split' => false,
            ];
        }

        $dotPos = mb_strpos($note, '.');

        if ($dotPos === false) {
            return [
                'title'     => '',
                'reason'    => $note,
                'use_split' => false,
            ];
        }

        $title  = trim(mb_substr($note, 0, $dotPos + 1));
        $reason = trim(mb_substr($note, $dotPos + 1));

        $useSplit = $reason !== '';

        return [
            'title'     => $title,
            'reason'    => $useSplit ? $reason : $note,
            'use_split' => $useSplit,
        ];
    }
}
