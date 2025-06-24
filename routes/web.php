<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\PaymentProcessingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalaryRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/salary-requests/create', [SalaryRequestController::class, 'create'])->name('salary-requests.create');
    Route::post('/salary-requests', [SalaryRequestController::class, 'store'])->name('salary-requests.store');
    Route::get('/salary-requests/{salaryRequest}', [SalaryRequestController::class, 'show'])->name('salary-requests.show');

    Route::get('/payments/process', [PaymentProcessingController::class, 'index'])->name('payments.process.index');
    Route::post('/payments/process/{salaryRequest}/complete', [PaymentProcessingController::class, 'complete'])->name('payments.process.complete');

    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::post('/approvals/{salaryRequest}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{salaryRequest}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');

    Route::get('/reports/paid-salaries', [ReportController::class, 'paidSalaries'])->name('reports.paid-salaries.index');

    Route::get('/notifications', function () {
        // Ambil semua notifikasi untuk user yang sedang login
        $notifications = auth()->user()->notifications()->latest()->paginate(10);
        // Tandai semua notifikasi yang saat ini ditampilkan sebagai "dibaca"
        auth()->user()->unreadNotifications->markAsRead();

        return view('notifications.index', compact('notifications'));
    })->name('notifications.index');
});

require __DIR__ . '/auth.php';
