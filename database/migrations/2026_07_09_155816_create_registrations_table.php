<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number', 20)->unique();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('ticket_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('user_name', 100);
            $table->string('user_email', 100);
            $table->string('user_phone', 20)->nullable();
            
            // Payment Status (base)
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'expired'])->default('pending');
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_proof', 500)->nullable();
            $table->decimal('amount_paid', 12, 2)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamps();
            
            // Index untuk performance
            $table->index('registration_number');
            $table->index('user_email');
            $table->index('payment_status');
            $table->index('event_id');
            $table->index('ticket_type_id');
            $table->index(['user_email', 'event_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('registrations');
    }
};