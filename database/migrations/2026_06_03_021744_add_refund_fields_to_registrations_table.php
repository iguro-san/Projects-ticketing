<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->enum('refund_status', ['none', 'pending', 'processing', 'completed', 'failed'])->default('none')->after('payment_status');
            $table->text('refund_reason')->nullable()->after('refund_status');
            $table->timestamp('refund_requested_at')->nullable()->after('refund_reason');
            $table->timestamp('refund_processed_at')->nullable()->after('refund_requested_at');
            $table->decimal('refund_amount', 12, 2)->nullable()->after('refund_processed_at');
            $table->text('refund_notes')->nullable()->after('refund_amount');
        });
    }

    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['refund_status', 'refund_reason', 'refund_requested_at', 'refund_processed_at', 'refund_amount', 'refund_notes']);
        });
    }
};