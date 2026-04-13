<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->decimal('amount_paid', 10, 2)->nullable()->after('payment_method');
            $table->timestamp('paid_at')->nullable()->after('amount_paid');
            $table->text('admin_notes')->nullable()->after('paid_at');
        });
    }

    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'amount_paid', 'paid_at', 'admin_notes']);
        });
    }
};