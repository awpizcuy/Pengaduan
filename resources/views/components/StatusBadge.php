<?php

namespace App\View\Components;

use Illuminate\View\Component;

class StatusBadge extends Component
{
    public string $status;
    public string $text;

    public function __construct(string $status)
    {
        $this->status = $status;

        // Label yang ditampilkan (lebih konsisten untuk UI)
        // NOTE: ini hanya label tampilan, tidak mengubah nilai di DB.
        $this->text = match ($status) {
            'pending'    => 'Pending',
            'accepted'   => 'Diterima',
            'on_process' => 'Diproses',
            'hold'       => 'Ditunda',
            'rejected'   => 'Ditolak',
            'completed'  => 'Selesai',
            'rated'      => 'Dinilai',
            'complain'   => 'Komplain',
            default      => ucfirst(str_replace('_', ' ', (string) $status)),
        };
    }

    public function render()
    {
        return view('components.status-badge');
    }
}
