<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\RegistrationController as AdminRegistrationController;
use App\Http\Controllers\Admin\TicketTypeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

// Halaman Publik
Route::get('/', [EventController::class, 'index'])->name('home');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

// Login/Logout
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Halaman yang memerlukan login
Route::middleware(['auth'])->group(function () {
    Route::post('/events/{event}/register', [EventController::class, 'register'])->name('events.register');
    Route::get('/my-tickets', [EventController::class, 'myTickets'])->name('my.tickets');
    
    // Halaman Admin
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Manajemen Kategori
        Route::resource('categories', CategoryController::class)->except(['show', 'edit', 'create']);
        
        // Manajemen Event
        Route::resource('events', AdminEventController::class);
        
        // Manajemen Tiket per Event
        Route::resource('events.ticket-types', TicketTypeController::class)->shallow();
        
        // Manajemen Peserta per Event
        Route::get('/events/{event}/registrations', [AdminRegistrationController::class, 'index'])->name('events.registrations.index');
        Route::get('/events/{event}/registrations/export', [AdminRegistrationController::class, 'export'])->name('events.registrations.export');
        Route::put('/events/{event}/registrations/{registration}/payment', [AdminRegistrationController::class, 'updatePayment'])->name('events.registrations.update-payment');
    });
});