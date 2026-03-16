<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ListBarangController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DaftarEventController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ============ ROUTES LOGIN ============
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'proses']);

// ============ ROUTES PRAKTIKUM 3 ============
Route::get('/welcome', function () {
    return 'Selamat datang di Laravel';
});

Route::get('/user/{id}', function ($id) {
    return 'User dengan ID: ' . $id;
});

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return 'Dashboard Admin';
    });
    Route::get('/users', function () {
        return 'Daftar Users Admin';
    });
});

// Route untuk list barang (praktikum)
Route::get('/listbarang/{id}/{nama}', function($id, $nama){
    return view('list_barang', compact('id', 'nama'));
});

// ============ ROUTES PBL (PROJECT ANDA) ============

// Halaman utama
Route::get('/', [HomeController::class, 'index']);

// Halaman kontak
Route::get('/contact', [HomeController::class, 'contact']);

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index']);

// Events (menggunakan EventController)
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'detail']);

// Daftar Event (menggunakan DaftarEventController - jika ada)
Route::get('/daftarevent', [DaftarEventController::class, 'index']);
Route::get('/daftarevent/{id}', [DaftarEventController::class, 'detail']);

// Tiket
Route::get('/tickets', [TicketController::class, 'index']);
Route::get('/tickets/buy/{id}', [TicketController::class, 'buy']);