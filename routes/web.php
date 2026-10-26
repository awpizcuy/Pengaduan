<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminGedung\ReportController;
use App\Http\Controllers\HeadIT\ReportController as HeadITReportController;
use App\Http\Controllers\Teknisi\TaskController;
use App\Http\Controllers\HeadIT\TechnicianController;
use App\Http\Controllers\HeadIT\UserManagementController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Rute Autentikasi
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';


/*
|--------------------------------------------------------------------------
| Rute Publik
|--------------------------------------------------------------------------
*/
Route::get('/', [App\Http\Controllers\LandingController::class, 'index'])->name('landing');


/*
|--------------------------------------------------------------------------
| Rute yang Dilindungi (Wajib Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    | Rute Dashboard Universal
    */
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;

        if ($role == 'admin_gedung') {
            return redirect()->route('admin.dashboard');
        } elseif ($role == 'kepala_it') {
            return redirect()->route('kepala.dashboard');
        } elseif ($role == 'teknisi') {
            return redirect()->route('teknisi.dashboard');
        } else {
            abort(404, 'Dashboard tidak ditemukan');
        }
    })->name('dashboard');

    /*
    | Rute Profil
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    | Rute Notifikasi
    */
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');


    // ==================================================================
    // GRUP RUTE BERDASARKAN ROLE (Peran)
    // ==================================================================

    /*
    | RUTE KHUSUS ADMIN GEDUNG
    */
    Route::middleware(['role:admin_gedung'])->prefix('admin-gedung')->name('admin.')->group(function () {
        Route::get('/dashboard', [ReportController::class, 'index'])->name('dashboard');
        Route::get('/report/create', [ReportController::class, 'create'])->name('report.create');
        Route::post('/report', [ReportController::class, 'store'])->name('report.store');
        Route::get('/report/{report}/rate', [ReportController::class, 'rateForm'])->name('report.rateForm');
        Route::post('/report/{report}/rate', [ReportController::class, 'storeRating'])->name('report.storeRating');
        Route::post('/report/{report}/complain', [ReportController::class, 'storeComplaint'])->name('report.storeComplaint');
        Route::get('/report/{report}/show', [ReportController::class, 'show'])->name('report.show');
        Route::get('/report/history', [ReportController::class, 'history'])->name('report.history');
    });

    /*
    | RUTE KHUSUS KEPALA IT
    */
    Route::middleware(['role:kepala_it'])->prefix('kepala-it')->name('kepala.')->group(function () {
        Route::get('/dashboard', [HeadITReportController::class, 'index'])->name('dashboard');
        Route::get('/report/{report}/manage', [HeadITReportController::class, 'manage'])->name('report.manage');
        Route::post('/report/{report}/update', [HeadITReportController::class, 'update'])->name('report.update');
        Route::get('/report/{report}/print', [HeadITReportController::class, 'printView'])->name('report.print');
        Route::get('/reports/print-all-completed', [HeadITReportController::class, 'printAllCompleted'])->name('reports.printAllCompleted');
        Route::resource('technicians', TechnicianController::class);
        Route::resource('users', UserManagementController::class)->except(['show']);
        Route::get('/reports/export/completed', [HeadITReportController::class, 'exportAllCompletedExcel'])
        ->name('reports.exportAllCompletedExcel');
        Route::patch('/technicians/{technician}/toggle-active', [TechnicianController::class, 'toggleActive'])
        ->name('technicians.toggleActive');
        Route::post('/report/{report}/complaints/{complaint}/approve', [HeadITReportController::class, 'approveComplaint'])
        ->name('report.complaints.approve');
        Route::post('/report/{report}/complaints/{complaint}/reject', [HeadITReportController::class, 'rejectComplaint'])
        ->name('report.complaints.reject');
    });

    /*
    | RUTE KHUSUS TEKNISI
    */
    Route::middleware(['role:teknisi'])->prefix('teknisi')->name('teknisi.')->group(function () {
        Route::get('/dashboard', [TaskController::class, 'index'])->name('dashboard');
        Route::post('/task/{report}/start', [TaskController::class, 'start'])->name('task.start');
        Route::get('/task/{report}/complete', [TaskController::class, 'completeForm'])->name('task.completeForm');
        Route::post('/task/{report}/complete', [TaskController::class, 'storeCompletion'])->name('task.storeCompletion');
        Route::post('/task/{report}/claim', [TaskController::class, 'claim'])->name('task.claim');
        Route::get('/task/{report}/show', [TaskController::class, 'show'])->name('task.show');
        Route::get('/task/history', [TaskController::class, 'history'])->name('task.history');
        Route::patch('/technicians/{id}/activate', [TechnicianController::class, 'activate'])
        ->name('technicians.activate');
        Route::get('/task/{report}/detail', [\App\Http\Controllers\Teknisi\TaskController::class, 'detail'])
        ->name('task.detail');
    });

});
