<?php

namespace App\Console\Commands;

use App\Models\Event;       // <-- Tambahkan ini
use Illuminate\Console\Command;

class UpdateEventStatus extends Command
{
    protected $signature = 'app:update-event-status';
    protected $description = 'Update status event aktif menjadi completed jika tanggal sudah lewat';

    public function handle()
    {
        $count = Event::where('status', 'active')
            ->where('event_date', '<', now())
            ->update(['status' => 'completed']);

        $this->info("Updated {$count} event(s) to completed.");
        return Command::SUCCESS;
    }
}