<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('target', ['all', 'panitia', 'user'])->default('all');
            $table->foreignId('created_by')->constrained('users');
            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('announcements');
    }
};