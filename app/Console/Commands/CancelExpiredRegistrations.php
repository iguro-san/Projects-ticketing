<?php

namespace App\Console\Commands;

use App\Models\Registration;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CancelExpiredRegistrations extends Command
{
    protected $signature = 'registrations:cancel-expired';
    protected $description = 'Batalkan pendaftaran yang melewati batas waktu pembayaran';

    public function handle()
    {
        $this->info('Mengecek pendaftaran kadaluarsa...');

        $expired = Registration::where('payment_status', 'pending')
            ->where('payment_deadline', '<', Carbon::now())
            ->get();

        if ($expired->isEmpty()) {
            $this->info('✅ Tidak ada pendaftaran kadaluarsa.');
            return Command::SUCCESS;
        }

        $this->info("Ditemukan {$expired->count()} pendaftaran kadaluarsa.");

        $count = 0;
        $bar = $this->output->createProgressBar(count($expired));
        $bar->start();

        foreach ($expired as $registration) {
            $registration->cancel('Batas waktu pembayaran 24 jam telah habis (otomatis)');
            $count++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ {$count} pendaftaran berhasil dibatalkan.");

        return Command::SUCCESS;
    }
}