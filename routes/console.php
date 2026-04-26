<?php
// routes/console.php

use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\CancelExpiredRegistrations;

// Cek pendaftaran kadaluarsa setiap 5 menit
Schedule::command(CancelExpiredRegistrations::class)->everyFiveMinutes();
