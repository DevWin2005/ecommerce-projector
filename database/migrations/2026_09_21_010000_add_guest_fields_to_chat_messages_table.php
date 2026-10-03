<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cho phép user_id nullable để khách vãng lai chưa đăng nhập vẫn chat được
        DB::statement('ALTER TABLE chat_messages MODIFY user_id BIGINT UNSIGNED NULL');

        Schema::table('chat_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('chat_messages', 'session_id')) {
                $table->string('session_id')->nullable()->after('user_id')->index();
            }
            if (!Schema::hasColumn('chat_messages', 'guest_name')) {
                $table->string('guest_name')->nullable()->after('session_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn(['session_id', 'guest_name']);
        });
    }
};
