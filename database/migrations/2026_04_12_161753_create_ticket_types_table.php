<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ticket_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->decimal('price', 12, 2)->default(0);
            $table->integer('quota')->unsigned();
            $table->integer('registered')->unsigned()->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index untuk performance
            $table->index('event_id');
            $table->index('is_active');
            
            // Validasi: registered tidak boleh melebihi quota
            // Ini akan dihandle di aplikasi, bukan di database
        });
    }

    public function down()
    {
        Schema::dropIfExists('ticket_types');
    }
};