<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->enum('suspension_status', ['normal', 'pending', 'cancelled'])->default('normal')->after('status');
            $table->text('suspension_reason')->nullable()->after('suspension_status');
            $table->timestamp('suspended_at')->nullable()->after('suspension_reason');
            $table->foreignId('suspended_by')->nullable()->after('suspended_at')->constrained('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['suspended_by']);
            $table->dropColumn(['suspension_status', 'suspension_reason', 'suspended_at', 'suspended_by']);
        });
    }
};