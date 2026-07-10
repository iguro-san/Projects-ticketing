<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Refund Status
            $table->enum('refund_status', ['none', 'pending', 'processing', 'completed', 'rejected'])->default('none')->after('payment_status');
            $table->text('refund_reason')->nullable()->after('refund_status');
            $table->timestamp('refund_requested_at')->nullable()->after('refund_reason');
            $table->timestamp('refund_processed_at')->nullable()->after('refund_requested_at');
            $table->decimal('refund_amount', 12, 2)->nullable()->after('refund_processed_at');
            $table->text('refund_notes')->nullable()->after('refund_amount');
            
            // Bank Account for Refund
            $table->string('refund_bank', 50)->nullable()->after('refund_notes');
            $table->string('refund_account_name', 100)->nullable()->after('refund_bank');
            $table->string('refund_account_number', 50)->nullable()->after('refund_account_name');
            
            // Index untuk refund
            $table->index('refund_status');
            $table->index('refund_requested_at');
        });
    }

    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropIndex(['refund_status']);
            $table->dropIndex(['refund_requested_at']);
            
            $table->dropColumn([
                'refund_status',
                'refund_reason',
                'refund_requested_at',
                'refund_processed_at',
                'refund_amount',
                'refund_notes',
                'refund_bank',
                'refund_account_name',
                'refund_account_number'
            ]);
        });
    }
};