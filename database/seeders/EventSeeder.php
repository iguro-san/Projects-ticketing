<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $event1 = Event::create([
            'category_id' => 1,
            'panitia_id' => 2,
            'title' => 'Seminar Teknologi 2026',
            'description' => 'Seminar tentang perkembangan teknologi terbaru di tahun 2026.',
            'event_date' => now()->addDays(30),
            'location' => 'Jakarta Convention Center',
            'status' => 'active',
        ]);

        TicketType::create([
            'event_id' => $event1->id,
            'name' => 'Regular',
            'price' => 150000,
            'quota' => 100,
            'description' => 'Tiket reguler',
        ]);

        TicketType::create([
            'event_id' => $event1->id,
            'name' => 'VIP',
            'price' => 350000,
            'quota' => 50,
            'description' => 'Tiket VIP dengan fasilitas khusus',
        ]);

        $event2 = Event::create([
            'category_id' => 2,
            'panitia_id' => 3,
            'title' => 'Konser Musik Indie',
            'description' => 'Konser musik indie dengan berbagai band lokal.',
            'event_date' => now()->addDays(45),
            'location' => 'Bandung Creative Hub',
            'status' => 'active',
        ]);

        TicketType::create([
            'event_id' => $event2->id,
            'name' => 'Early Bird',
            'price' => 75000,
            'quota' => 200,
            'description' => 'Tiket early bird',
        ]);

        TicketType::create([
            'event_id' => $event2->id,
            'name' => 'Normal',
            'price' => 100000,
            'quota' => 300,
            'description' => 'Tiket normal',
        ]);
    }
}