<?php

namespace App\Http\Controllers\HeadIT;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use App\Models\Report;

class TechnicianController extends Controller
{
    /**
     * Menampilkan daftar semua teknisi.
     */
    public function index(Request $request)
    {
        $searchTerm = $request->query('search');

        $query = User::where('role', 'teknisi');

        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        $technicians = $query->withCount([
                                'tasks as completed_tasks_count' => function ($subQuery) {
                                    $subQuery->whereIn('status', ['completed', 'rated']);
                                },
                                'tasks as active_tasks_count' => function ($subQuery) {
                                    $subQuery->whereIn('status', ['accepted', 'on_process', 'hold', 'complain']);
                                }
                            ])
                            ->paginate(10)
                            ->withQueryString();

        return view('kepala-it.technicians.index', [
            'technicians' => $technicians,
            'searchTerm' => $searchTerm
        ]);
    }

    /**
     * Menampilkan form untuk menambah teknisi baru.
     */
    public function create()
    {
        return view('kepala-it.technicians.create');
    }

    /**
     * Menyimpan teknisi baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'no_telp' => ['nullable', 'string', 'max:20'],
            ],
            [
                // ✅ custom message agar tidak muncul "invalid unique"
                'email.unique' => 'Email tersebut sudah terdaftar. Silakan gunakan email lain.',
                // optional biar lebih rapi juga
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'name.required' => 'Nama wajib diisi.',
            ]
        );

        $plainPassword = Str::random(10);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'no_telp' => $request->no_telp,
            'password' => Hash::make($plainPassword),
            'role' => 'teknisi'
        ]);

        try {
            Mail::to($user->email)->send(new \App\Mail\TechnicianAccountCreated($user, $plainPassword));
            return redirect()->route('kepala.technicians.index')
                         ->with('success', 'Teknisi baru berhasil dibuat. Password sementara telah dikirim ke email pengguna.');
        } catch (\Exception $e) {
            report($e);
            // Jika email gagal, tetap buat user tapi tampilkan password di session untuk dicatat manual
            session()->flash('created_user_password', $plainPassword);
            session()->flash('created_user_email', $user->email);
            return redirect()->route('kepala.technicians.index')
                         ->with('error', 'Akun berhasil dibuat, tapi gagal mengirim email. Harap catat password berikut secara manual.');
        }
    }

    /**
     * Menampilkan form untuk mengedit teknisi.
     */
    public function edit(User $technician)
    {
        if ($technician->role != 'teknisi') {
            abort(404);
        }

        return view('kepala-it.technicians.edit', [
            'technician' => $technician
        ]);
    }

    /**
     * Memperbarui data teknisi di database.
     */
    public function update(Request $request, User $technician)
    {
        $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($technician->id)],
                'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
                'no_telp' => ['nullable', 'string', 'max:20']
            ],
            [
                // ✅ custom message agar tidak muncul "invalid unique"
                'email.unique' => 'Email tersebut sudah digunakan oleh akun lain. Silakan gunakan email berbeda.',
                // optional biar lebih rapi juga
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'name.required' => 'Nama wajib diisi.',
                'password.confirmed' => 'Konfirmasi password tidak sama.',
            ]
        );

        $technician->update([
            'name' => $request->name,
            'email' => $request->email,
            'no_telp' => $request->no_telp,
        ]);

        if ($request->filled('password')) {
            $technician->update([
                'password' => Hash::make($request->password)
            ]);
        }

        return redirect()->route('kepala.technicians.index')
                     ->with('success', 'Data teknisi telah berhasil diperbarui!');
    }

    /**
     * Menghapus teknisi dari database.
     */
    public function destroy(User $technician)
    {
        $technician->is_active = false;
        $technician->save();

        return redirect()->route('kepala.technicians.index')
        ->with('success', 'Teknisi berhasil dinonaktifkan. Data historis tetap tersimpan.');
    }

    public function activate($id)
    {
        $technician = User::where('role', 'teknisi')->findOrFail($id);
        $technician->is_active = true;
        $technician->save();

        return redirect()->route('kepala.technicians.index')
            ->with('success', 'Teknisi berhasil diaktifkan kembali.');
    }

    public function toggleActive(User $technician)
    {
        if ($technician->role !== 'teknisi') {
            abort(404);
        }

        // Jika mau menonaktifkan, blokir kalau masih ada tugas aktif
        if ($technician->is_active) {
            $activeTasks = $technician->tasks()
                ->whereNotIn('status', ['completed', 'rated', 'rejected'])
                ->count();

            if ($activeTasks > 0) {
                return back()->with('error', "Tidak bisa menonaktifkan teknisi karena masih memiliki {$activeTasks} tugas aktif.");
            }

            $technician->is_active = false;
            $technician->save();

            return back()->with('success', 'Teknisi berhasil dinonaktifkan.');
        }

        // Aktifkan kembali
        $technician->is_active = true;
        $technician->save();

        return back()->with('success', 'Teknisi berhasil diaktifkan.');
    }
}
