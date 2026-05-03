<?php

use App\Http\Controllers\{
    AuthController,
    HomeController,
    RegistrationController,
    PaymentController
};
use App\Http\Controllers\Admin\{
    CategoryController as AdminCategoryController,
    DashboardController as AdminDashboardController,
    EventController as AdminEventController,
    RegistrationController as AdminRegistrationController,
    TicketTypeController as AdminTicketTypeController,
    PanitiaController as AdminPanitiaController
};
use App\Http\Controllers\Panitia\{
    DashboardController as PanitiaDashboardController,
    EventController as PanitiaEventController,
    TicketTypeController as PanitiaTicketTypeController
};
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/events/{event}', [HomeController::class, 'show'])->name('events.show');
Route::get('/events', [HomeController::class, 'index'])->name('events.index');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// User & Panitia Routes
Route::middleware('auth')->group(function () {
    Route::middleware('role:user,panitia')->group(function () {
        Route::get('/my-tickets', [RegistrationController::class, 'myTickets'])->name('my.tickets');
        Route::post('/events/{event}/register', [RegistrationController::class, 'register'])->name('events.register');
        Route::get('/payment/{registration}', [PaymentController::class, 'show'])->name('payment.show');
        Route::post('/payment/{registration}/upload', [PaymentController::class, 'uploadProof'])->name('payment.upload');
    });
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Categories
    Route::resource('/categories', AdminCategoryController::class)->except(['show']);
    
    // Events
    Route::get('/events', [AdminEventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [AdminEventController::class, 'create'])->name('events.create');
    Route::post('/events', [AdminEventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}', [AdminEventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/edit', [AdminEventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [AdminEventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [AdminEventController::class, 'destroy'])->name('events.destroy');
    
    // APPROVE & REJECT
    Route::post('/events/{event}/approve', [AdminEventController::class, 'approve'])->name('events.approve');
    Route::post('/events/{event}/reject', [AdminEventController::class, 'reject'])->name('events.reject');
    
    // Ticket Types
    Route::get('/events/{event}/ticket-types', [AdminTicketTypeController::class, 'index'])->name('events.ticket-types.index');
    Route::post('/events/{event}/ticket-types', [AdminTicketTypeController::class, 'store'])->name('events.ticket-types.store');
    Route::put('/events/{event}/ticket-types/{ticketType}', [AdminTicketTypeController::class, 'update'])->name('events.ticket-types.update');
    Route::delete('/events/{event}/ticket-types/{ticketType}', [AdminTicketTypeController::class, 'destroy'])->name('events.ticket-types.destroy');
    
    // Registrations
    Route::get('/registrations', [AdminRegistrationController::class, 'index'])->name('registrations.index');
    Route::get('/registrations/{registration}', [AdminRegistrationController::class, 'show'])->name('registrations.show');
    Route::post('/registrations/{registration}/verify', [AdminRegistrationController::class, 'verifyPayment'])->name('registrations.verify');
    Route::get('/registrations/export', [AdminRegistrationController::class, 'export'])->name('registrations.export');
    
    // Event Registrations
    Route::get('/events/{event}/registrations', [AdminRegistrationController::class, 'index'])->name('events.registrations.index');
    Route::get('/events/{event}/registrations/export', [AdminRegistrationController::class, 'export'])->name('events.registrations.export');
    
    // Panitia Management
    Route::resource('/panitia', AdminPanitiaController::class);
});

// Panitia Routes
Route::middleware(['auth', 'role:panitia'])->prefix('panitia')->name('panitia.')->group(function () {
    
    Route::get('/dashboard', [PanitiaDashboardController::class, 'index'])->name('dashboard');
    
    // Events
    Route::get('/events', [PanitiaEventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [PanitiaEventController::class, 'create'])->name('events.create');
    Route::post('/events', [PanitiaEventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}', [PanitiaEventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/edit', [PanitiaEventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [PanitiaEventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [PanitiaEventController::class, 'destroy'])->name('events.destroy');
    
    // Registrations
    Route::get('/events/{event}/registrations', [PanitiaEventController::class, 'registrations'])->name('events.registrations');
    Route::post('/events/{event}/registrations/{registration}/confirm', [PanitiaEventController::class, 'confirmPayment'])->name('events.registrations.confirm');
    Route::get('/events/{event}/registrations/export', [PanitiaEventController::class, 'exportRegistrations'])->name('events.registrations.export');
    
    // Ticket Types
    Route::get('/events/{event}/tickets', [PanitiaTicketTypeController::class, 'index'])->name('events.tickets.index');
    Route::post('/events/{event}/tickets', [PanitiaTicketTypeController::class, 'store'])->name('events.tickets.store');
    Route::put('/events/{event}/tickets/{ticketType}', [PanitiaTicketTypeController::class, 'update'])->name('events.tickets.update');
    Route::delete('/events/{event}/tickets/{ticketType}', [PanitiaTicketTypeController::class, 'destroy'])->name('events.tickets.destroy');
});