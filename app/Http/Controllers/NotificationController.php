<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
    $notifications = Auth::user()
                         ->notifications()
                         ->latest()
                         ->paginate(15);

    return view('notifications.index', [
        'notifications' => $notifications
    ]);
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            return redirect($notification->data['url'] ?? route('dashboard'));
        }

        return redirect()->route('dashboard')->with('error', 'Notifikasi tidak ditemukan.');
    }

    public function markAllAsRead()
    {
    Auth::user()->unreadNotifications->markAsRead();

    return redirect()->back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
