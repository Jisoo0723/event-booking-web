<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bookings')) {
            return;
        }

        // SQLite 인덱스 목록 확인
        $indexes = collect(DB::select("PRAGMA index_list('bookings')"))
            ->pluck('name')
            ->toArray();

        // 이미 인덱스가 있으면 스킵
        if (in_array('bookings_user_id_event_id_unique', $indexes)) {
            return;
        }

        Schema::table('bookings', function (Blueprint $t) {
            try {
                $t->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            } catch (\Throwable $e) {}
            try {
                $t->foreign('event_id')->references('id')->on('events')->cascadeOnDelete();
            } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('bookings')) {
            return;
        }

        Schema::table('bookings', function (Blueprint $t) {
            try {
                $t->dropForeign(['user_id']);
            } catch (\Throwable $e) {}
            try {
                $t->dropForeign(['event_id']);
            } catch (\Throwable $e) {}
        });
    }
};
