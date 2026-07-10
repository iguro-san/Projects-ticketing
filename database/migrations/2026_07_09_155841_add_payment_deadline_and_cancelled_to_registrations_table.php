<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->timestamp('payment_deadline')->nullable()->after('payment_status');
            $table->timestamp('cancelled_at')->nullable()->after('paid_at');
            
            // Index untuk deadline
            $table->index('payment_deadline');
        });
    }

    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropIndex(['payment_deadline']);
            $table->dropColumn(['payment_deadline', 'cancelled_at']);
        });
    }
};