<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminEventController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\DaftarEventController;

// Route publik
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{id}', [EventController::class, 'detail'])->name('events.detail');
Route::get('/daftar-event', [DaftarEventController::class, 'index'])->name('daftar.event');
Route::get('/daftar-event/{id}', [DaftarEventController::class, 'detail'])->name('daftar.event.detail');

// Auth routes
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'proses'])->name('login.proses');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Tiket routes
Route::get('/tickets', [TicketController::class, 'index'])->name('tickets');
Route::get('/tickets/buy/{id}', [TicketController::class, 'buy'])->name('tickets.buy');

// Registrasi (perlu login)
Route::middleware(['auth.session'])->group(function () {
    Route::post('/events/{eventId}/register', [RegistrationController::class, 'store'])->name('register.event');
    Route::get('/my-tickets', [RegistrationController::class, 'myTickets'])->name('my.tickets');
});

// Admin routes (perlu role admin)
Route::prefix('admin')->middleware(['auth.session'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Manajemen Event
    Route::get('/events', [AdminEventController::class, 'index'])->name('admin.events.index');
    Route::get('/events/create', [AdminEventController::class, 'create'])->name('admin.events.create');
    Route::post('/events', [AdminEventController::class, 'store'])->name('admin.events.store');
    Route::get('/events/{id}/edit', [AdminEventController::class, 'edit'])->name('admin.events.edit');
    Route::put('/events/{id}', [AdminEventController::class, 'update'])->name('admin.events.update');
    Route::delete('/events/{id}', [AdminEventController::class, 'destroy'])->name('admin.events.destroy');
    
    // Manajemen Kategori
    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    
    // Lihat Peserta per Event
    Route::get('/events/{eventId}/participants', [RegistrationController::class, 'participants'])->name('admin.participants');
});