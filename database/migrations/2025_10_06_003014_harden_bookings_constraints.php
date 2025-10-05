<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('bookings', function (Blueprint $t) {
            $t->unique(['user_id','event_id']);                 // 중복 예약 금지
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('event_id')->constrained()->cascadeOnDelete();
        });
    }
    public function down(): void {
        Schema::table('bookings', function (Blueprint $t) {
            $t->dropUnique(['user_id','event_id']);
            $t->dropConstrainedForeignId('user_id');
            $t->dropConstrainedForeignId('event_id');
        });
    }
};
