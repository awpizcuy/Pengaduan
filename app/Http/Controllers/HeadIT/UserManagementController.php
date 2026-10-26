<?php

namespace App\Http\Controllers\HeadIT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'admin_gedung')->orderBy('name')->paginate(20);
        return view('kepala-it.admin-gedung.index', compact('users'));
    }

    public function create()
    {
        return view('kepala-it.admin-gedung.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'no_telp' => 'nullable|string|max:20',
        ]);

        $plain = Str::random(10);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($plain),   
            'no_telp' => $data['no_telp'] ?? null,
            'role' => 'admin_gedung',
        ]);

        try {
            Mail::to($user->email)->send(new \App\Mail\AdminGedungAccountCreated($user, $plain));
            return redirect()->route('kepala.users.index')->with('success', 'Akun admin gedung berhasil dibuat. Password sementara telah dikirim ke email pengguna.');
        } catch (\Exception $e) {
            report($e);
            session()->flash('created_user_password', $plain);
            session()->flash('created_user_email', $user->email);
            return redirect()->route('kepala.users.index')->with('error', 'Gagal mengirim email. Harap catat password berikut secara manual.');
        }
    }

    public function edit(User $user)
    {
        abort_unless($user->role === 'admin_gedung', 404);
        return view('kepala-it.admin-gedung.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->role === 'admin_gedung', 404);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'password' => 'nullable|string|min:6',
            'no_telp' => 'nullable|string|max:20',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->no_telp = $data['no_telp'] ?? null;

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('kepala.users.index')->with('success', 'Akun diperbarui.');
    }

    public function destroy(User $user)
    {
        abort_unless($user->role === 'admin_gedung', 404);
        $user->delete();
        return redirect()->route('kepala.users.index')->with('success', 'Akun dihapus.');
    }
}
