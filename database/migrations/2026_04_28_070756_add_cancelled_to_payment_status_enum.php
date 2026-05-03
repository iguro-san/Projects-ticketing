<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Ubah enum untuk menambah nilai 'cancelled'
        DB::statement("ALTER TABLE registrations MODIFY COLUMN payment_status ENUM('pending', 'paid', 'failed', 'expired', 'cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        // Kembalikan ke enum semula (tanpa cancelled)
        DB::statement("ALTER TABLE registrations MODIFY COLUMN payment_status ENUM('pending', 'paid', 'failed', 'expired') NOT NULL DEFAULT 'pending'");
    }
};