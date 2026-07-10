<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->string('phone', 20)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->enum('role', ['admin', 'panitia', 'user'])->default('user');
            $table->rememberToken();
            $table->timestamps();
            
            // Index untuk performance
            $table->index('email');
            $table->index('role');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};