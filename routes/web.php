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
use App\Http\Controllers\AccountController;
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
// AUTHENTICATED USER ROUTES (Semua user yang login)
// ==========================================
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
});

// ==========================================
// USER ROUTES (ROLE USER)
// ==========================================
Route::middleware(['auth', 'check.role:user'])->group(function () {
    Route::post('/events/{event}/register', [UserRegistrationController::class, 'register'])->name('events.register');
    Route::get('/my-tickets', [UserRegistrationController::class, 'myTickets'])->name('my.tickets');
    Route::get('/payment/{registration}', [UserPaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{registration}/upload', [UserPaymentController::class, 'uploadProof'])->name('payment.upload');
    Route::post('/refund/request/{registration}', [RefundRequestController::class, 'request'])->name('refund.request');
    Route::get('/account', [AccountController::class, 'index'])->name('account.show');
    Route::post('/account', [AccountController::class, 'update'])->name('account.update');
    Route::post('/account/password', [AccountController::class, 'updatePassword'])->name('account.password');
});

// ==========================================
// PANITIA ROUTES (ROLE PANITIA)
// ==========================================
Route::middleware(['auth', 'check.role:panitia'])->prefix('panitia')->name('panitia.')->group(function () {
    Route::get('/dashboard', [PanitiaDashboardController::class, 'index'])->name('dashboard');
    Route::get('/events', [PanitiaEventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [PanitiaEventController::class, 'create'])->name('events.create');
    Route::post('/events', [PanitiaEventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [PanitiaEventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [PanitiaEventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [PanitiaEventController::class, 'destroy'])->name('events.destroy');
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
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    
    // Event Management
    Route::get('/events', [AdminEventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [AdminEventController::class, 'create'])->name('events.create');
    Route::post('/events', [AdminEventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}', [AdminEventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/edit', [AdminEventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [AdminEventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [AdminEventController::class, 'destroy'])->name('events.destroy');
    
    // Event Approval & Suspension
    Route::post('/events/{event}/approve', [AdminEventController::class, 'approve'])->name('events.approve');
    Route::post('/events/{event}/reject', [AdminEventController::class, 'reject'])->name('events.reject');
    Route::post('/events/{event}/pending', [SuspensionController::class, 'pending'])->name('events.pending');
    Route::post('/events/{event}/resolve/{action}', [SuspensionController::class, 'resolve'])->name('events.resolve');
    Route::post('/events/{event}/cancel', [AdminEventController::class, 'cancelEvent'])->name('events.cancel');
    
    // Ticket Types
    Route::get('/events/{event}/ticket-types', [AdminTicketTypeController::class, 'index'])->name('events.ticket-types.index');
    Route::post('/events/{event}/ticket-types', [AdminTicketTypeController::class, 'store'])->name('events.ticket-types.store');
    Route::put('/events/{event}/ticket-types/{ticketType}', [AdminTicketTypeController::class, 'update'])->name('events.ticket-types.update');
    Route::delete('/events/{event}/ticket-types/{ticketType}', [AdminTicketTypeController::class, 'destroy'])->name('events.ticket-types.destroy');
    
    // Registration Management
    Route::get('/registrations', [AdminRegistrationController::class, 'index'])->name('registrations.index');
    Route::get('/registrations/{registration}', [AdminRegistrationController::class, 'show'])->name('registrations.show');
    Route::post('/registrations/{registration}/verify-payment', [AdminRegistrationController::class, 'verifyPayment'])->name('registrations.verify-payment');
    Route::get('/registrations/export', [AdminRegistrationController::class, 'export'])->name('registrations.export');
    Route::get('/events/{event}/registrations', [AdminRegistrationController::class, 'eventRegistrations'])->name('events.registrations');
    Route::get('/events/{event}/registrations/export', [AdminRegistrationController::class, 'exportEventRegistrations'])->name('events.registrations.export');
    
    // Panitia Management
    Route::get('/panitia', [PanitiaController::class, 'index'])->name('panitia.index');
    Route::get('/panitia/create', [PanitiaController::class, 'create'])->name('panitia.create');
    Route::post('/panitia', [PanitiaController::class, 'store'])->name('panitia.store');
    Route::delete('/panitia/{user}', [PanitiaController::class, 'destroy'])->name('panitia.destroy');
    
    // Payment Confirmation
    Route::get('/payments', [PaymentConfirmationController::class, 'index'])->name('payments.index');
    Route::post('/payments/{registration}/confirm', [PaymentConfirmationController::class, 'confirm'])->name('payments.confirm');
    
    // Refund Management
    Route::get('/refunds', [RefundController::class, 'index'])->name('refunds.index');
    Route::post('/refunds/{registration}/process', [RefundController::class, 'process'])->name('refunds.process');
    
    // Announcement Management
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    Route::post('/announcements/{announcement}/toggle', [AnnouncementController::class, 'toggleStatus'])->name('announcements.toggle');
});