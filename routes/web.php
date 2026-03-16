<?php

use Illuminate\Support\Facades\Route;
Use App\Http\Controllers\HomeController;
use App\Http\Controllers\DaftarEventController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [HomeController::class, 'index']);
Route::get('/contact', [HomeController::class, 'contact']);
Route::get('/daftarevent', [DaftarEventController::class, 'index']);
Route::get('/daftarevent/{id}', [DaftarEventController::class, 'detail']);
