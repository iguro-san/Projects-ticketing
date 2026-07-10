<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->foreignId('panitia_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title', 200);
            $table->text('description');
            $table->date('event_date');
            $table->string('location', 255);
            $table->string('poster', 500)->nullable();
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft');
            $table->enum('suspension_status', ['normal', 'pending', 'cancelled'])->default('normal');
            $table->text('suspension_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->foreignId('suspended_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Index untuk performance
            $table->index('event_date');
            $table->index('status');
            $table->index('suspension_status');
            $table->index(['event_date', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
};