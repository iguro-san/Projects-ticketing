<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\PanitiaController;
use App\Http\Controllers\Admin\PaymentConfirmationController;
use App\Http\Controllers\Admin\RefundController;
use App\Http\Controllers\Admin\RegistrationController as AdminRegistrationController;
use App\Http\Controllers\Admin\SuspensionController;
use App\Http\Controllers\Admin\TicketTypeController as AdminTicketTypeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Panitia\DashboardController as PanitiaDashboardController;
use App\Http\Controllers\Panitia\EventController as PanitiaEventController;
use App\Http\Controllers\User\EventController as UserEventController;
use App\Http\Controllers\User\PaymentController as UserPaymentController;
use App\Http\Controllers\User\RefundRequestController;
use App\Http\Controllers\User\RegistrationController as UserRegistrationController;
use Illuminate\Support\Facades\Route;

// ==========================================
// HOME & PUBLIC ROUTES
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home');

// ==========================================
// PASSWORD RESET ROUTES (LUPA PASSWORD)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// ==========================================
// PUBLIC EVENT ROUTES (TANPA MIDDLEWARE)
// ==========================================
Route::get('/events', [UserEventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [UserEventController::class, 'show'])->name('events.show');

// ==========================================
// AUTH ROUTES (GUEST ONLY)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ==========================================
// USER ROUTES (ROLE USER)
// ==========================================
Route::middleware(['auth', 'check.role:user'])->group(function () {
    Route::post('/events/{event}/register', [UserRegistrationController::class, 'register'])->name('events.register');
    Route::get('/my-tickets', [UserRegistrationController::class, 'myTickets'])->name('my.tickets');
    Route::get('/payment/{registration}', [UserPaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{registration}/upload', [UserPaymentController::class, 'uploadProof'])->name('payment.upload');
    Route::post('/refund/request/{registration}', [RefundRequestController::class, 'request'])->name('refund.request');
});

// ==========================================
// PANITIA ROUTES (ROLE PANITIA)
// ==========================================
Route::middleware(['auth', 'check.role:panitia'])->prefix('panitia')->name('panitia.')->group(function () {
    Route::get('/dashboard', [PanitiaDashboardController::class, 'index'])->name('dashboard');
    Route::resource('events', PanitiaEventController::class)->except(['show']);
    Route::get('/events/{event}/registrations', [PanitiaEventController::class, 'registrations'])->name('events.registrations');
    Route::get('/events/{event}/registrations/export', [PanitiaEventController::class, 'exportRegistrations'])->name('events.registrations.export');
});

// ==========================================
// ADMIN ROUTES (ROLE ADMIN)
// ==========================================
Route::middleware(['auth', 'check.role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Category Management
    Route::resource('categories', CategoryController::class)->except(['show']);
    
    // Event Management
    Route::resource('events', AdminEventController::class);
    Route::post('/events/{event}/approve', [AdminEventController::class, 'approve'])->name('events.approve');
    Route::post('/events/{event}/reject', [AdminEventController::class, 'reject'])->name('events.reject');
    Route::post('/events/{event}/pending', [SuspensionController::class, 'pending'])->name('events.pending');
    Route::post('/events/{event}/resolve/{action}', [SuspensionController::class, 'resolve'])->name('events.resolve');
    
    // Ticket Types
    Route::resource('events.ticket-types', AdminTicketTypeController::class)->except(['show', 'edit']);
    
    // Registration Management
    Route::get('/registrations', [AdminRegistrationController::class, 'index'])->name('registrations.index');
    Route::get('/registrations/{registration}', [AdminRegistrationController::class, 'show'])->name('registrations.show');
    Route::post('/registrations/{registration}/verify-payment', [AdminRegistrationController::class, 'verifyPayment'])->name('registrations.verify-payment');
    Route::get('/registrations/export', [AdminRegistrationController::class, 'export'])->name('registrations.export');
    
    // Panitia Management
    Route::resource('panitia', PanitiaController::class)->except(['show', 'edit', 'update']);
    
    // Payment Confirmation (ADMIN ONLY)
    Route::get('/payments', [PaymentConfirmationController::class, 'index'])->name('payments.index');
    Route::post('/payments/{registration}/confirm', [PaymentConfirmationController::class, 'confirm'])->name('payments.confirm');
    
    // Refund Management
    Route::get('/refunds', [RefundController::class, 'index'])->name('refunds.index');
    Route::post('/refunds/{registration}/process', [RefundController::class, 'process'])->name('refunds.process');
    
    // Announcement Management
    Route::resource('announcements', AnnouncementController::class)->except(['show', 'edit', 'update']);
    Route::post('/announcements/{announcement}/toggle', [AnnouncementController::class, 'toggleStatus'])->name('announcements.toggle');
});

// ==========================================
// USER ROUTES (Regular User Only)
// ==========================================
Route::middleware(['auth'])->group(function () {
    // Account management (profil & ganti password) - Only for regular users
    Route::get('/account', [\App\Http\Controllers\AccountController::class, 'index'])->name('account.show');
    Route::post('/account', [\App\Http\Controllers\AccountController::class, 'update'])->name('account.update');
    Route::post('/account/password', [\App\Http\Controllers\AccountController::class, 'updatePassword'])->name('account.password');
});

// ==========================================
// NOTIFICATIONS ROUTES
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
});