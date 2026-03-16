<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ListBarangController;
// Tambahkan ini
use App\Http\Controllers\LoginController;

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

// Route untuk list barang
Route::get('/listbarang/{id}/{nama}', function($id, $nama){
    return view('list_barang', compact('id', 'nama'));
});

// ============ ROUTES PBL ANDA ============
Route::get('/', [HomeController::class, 'index']);
Route::get('/contact', [HomeController::class, 'contact']);
Route::get('/dashboard', [DashboardController::class, 'index']);
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'detail']);
Route::get('/tickets', [TicketController::class, 'index']);
Route::get('/tickets/buy/{id}', [TicketController::class, 'buy']);