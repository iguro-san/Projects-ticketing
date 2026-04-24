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

/*
|--------------------------------------------------------------------------
| Public Routes (Bisa diakses tanpa login)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/events/{event}', [HomeController::class, 'show'])->name('events.show');
Route::get('/events', [HomeController::class, 'index'])->name('events.index');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Untuk yang BELUM login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // Forgot Password (Opsional)
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
        ->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
        ->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])
        ->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->name('password.update');
});

// Logout (Harus sudah login)
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| User & Panitia Routes (Harus LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    // Routes untuk User dan Panitia
    Route::middleware('role:user,panitia')->group(function () {
        // My Tickets
        Route::get('/my-tickets', [RegistrationController::class, 'myTickets'])
            ->name('my.tickets');
        
        // Register for event
        Route::post('/events/{event}/register', [RegistrationController::class, 'register'])
            ->name('events.register');
        
        // Payment
        Route::get('/payment/{registration}', [PaymentController::class, 'show'])
            ->name('payment.show');
        Route::post('/payment/{registration}/upload', [PaymentController::class, 'uploadProof'])
            ->name('payment.upload');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Harus LOGIN + Role ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');
        
        // Categories Management
        Route::get('/categories', [AdminCategoryController::class, 'index'])
            ->name('categories.index');
        Route::post('/categories', [AdminCategoryController::class, 'store'])
            ->name('categories.store');
        Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])
            ->name('categories.update');
        Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])
            ->name('categories.destroy');
        
        // Events Management
        Route::get('/events', [AdminEventController::class, 'index'])
            ->name('events.index');
        Route::get('/events/create', [AdminEventController::class, 'create'])
            ->name('events.create');
        Route::post('/events', [AdminEventController::class, 'store'])
            ->name('events.store');
        Route::get('/events/{event}', [AdminEventController::class, 'show'])
            ->name('events.show');
        Route::get('/events/{event}/edit', [AdminEventController::class, 'edit'])
            ->name('events.edit');
        Route::put('/events/{event}', [AdminEventController::class, 'update'])
            ->name('events.update');
        Route::delete('/events/{event}', [AdminEventController::class, 'destroy'])
            ->name('events.destroy');
        
        // Ticket Types Management
        Route::get('/events/{event}/ticket-types', [AdminTicketTypeController::class, 'index'])
            ->name('events.ticket-types.index');
        Route::post('/events/{event}/ticket-types', [AdminTicketTypeController::class, 'store'])
            ->name('events.ticket-types.store');
        Route::put('/events/{event}/ticket-types/{ticketType}', [AdminTicketTypeController::class, 'update'])
            ->name('events.ticket-types.update');
        Route::delete('/events/{event}/ticket-types/{ticketType}', [AdminTicketTypeController::class, 'destroy'])
            ->name('events.ticket-types.destroy');
        
        // All Registrations
        Route::get('/registrations', [AdminRegistrationController::class, 'index'])
            ->name('registrations.index');
        Route::get('/registrations/{registration}', [AdminRegistrationController::class, 'show'])
            ->name('registrations.show');
        Route::post('/registrations/{registration}/verify', [AdminRegistrationController::class, 'verifyPayment'])
            ->name('registrations.verify');
        Route::get('/registrations/export', [AdminRegistrationController::class, 'export'])
            ->name('registrations.export');
        
        // Event Registrations (Per Event)
        Route::get('/events/{event}/registrations', [AdminRegistrationController::class, 'index'])
            ->name('events.registrations.index');
        Route::get('/events/{event}/registrations/{registration}', [AdminRegistrationController::class, 'show'])
            ->name('events.registrations.show');
        Route::get('/events/{event}/registrations/{registration}/payment', [AdminRegistrationController::class, 'show'])
            ->name('events.registrations.payment');
        Route::put('/events/{event}/registrations/{registration}/update-payment', [AdminRegistrationController::class, 'verifyPayment'])
            ->name('events.registrations.update-payment');
        Route::get('/events/{event}/registrations/export', [AdminRegistrationController::class, 'export'])
            ->name('events.registrations.export');
        
        // Panitia Management (CRUD)
        Route::get('/panitia', [AdminPanitiaController::class, 'index'])
            ->name('panitia.index');
        Route::get('/panitia/create', [AdminPanitiaController::class, 'create'])
            ->name('panitia.create');
        Route::post('/panitia', [AdminPanitiaController::class, 'store'])
            ->name('panitia.store');
        Route::get('/panitia/{user}', [AdminPanitiaController::class, 'show'])
            ->name('panitia.show');
        Route::get('/panitia/{user}/edit', [AdminPanitiaController::class, 'edit'])
            ->name('panitia.edit');
        Route::put('/panitia/{user}', [AdminPanitiaController::class, 'update'])
            ->name('panitia.update');
        Route::delete('/panitia/{user}', [AdminPanitiaController::class, 'destroy'])
            ->name('panitia.destroy');
    });

/*
|--------------------------------------------------------------------------
| Panitia Routes (Harus LOGIN + Role PANITIA)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:panitia'])
    ->prefix('panitia')
    ->name('panitia.')
    ->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [PanitiaDashboardController::class, 'index'])
            ->name('dashboard');
        
        // Events Management (Hanya event miliknya)
        Route::get('/events', [PanitiaEventController::class, 'index'])
            ->name('events.index');
        Route::get('/events/create', [PanitiaEventController::class, 'create'])
            ->name('events.create');
        Route::post('/events', [PanitiaEventController::class, 'store'])
            ->name('events.store');
        Route::get('/events/{event}', [PanitiaEventController::class, 'show'])
            ->name('events.show');
        Route::get('/events/{event}/edit', [PanitiaEventController::class, 'edit'])
            ->name('events.edit');
        Route::put('/events/{event}', [PanitiaEventController::class, 'update'])
            ->name('events.update');
        Route::delete('/events/{event}', [PanitiaEventController::class, 'destroy'])
            ->name('events.destroy');
        
        // Event Registrations (Lihat & Konfirmasi Peserta)
        Route::get('/events/{event}/registrations', [PanitiaEventController::class, 'registrations'])
            ->name('events.registrations');
        Route::get('/events/{event}/registrations/{registration}', [PanitiaEventController::class, 'paymentDetail'])
            ->name('events.registrations.detail');
        Route::post('/events/{event}/registrations/{registration}/confirm', [PanitiaEventController::class, 'confirmPayment'])
            ->name('events.registrations.confirm');
        Route::get('/events/{event}/registrations/export', [PanitiaEventController::class, 'exportRegistrations'])
            ->name('events.registrations.export');
        
        // Ticket Types Management
        Route::get('/events/{event}/tickets', [PanitiaTicketTypeController::class, 'index'])
            ->name('events.tickets.index');
        Route::post('/events/{event}/tickets', [PanitiaTicketTypeController::class, 'store'])
            ->name('events.tickets.store');
        Route::put('/events/{event}/tickets/{ticketType}', [PanitiaTicketTypeController::class, 'update'])
            ->name('events.tickets.update');
        Route::delete('/events/{event}/tickets/{ticketType}', [PanitiaTicketTypeController::class, 'destroy'])
            ->name('events.tickets.destroy');
    });