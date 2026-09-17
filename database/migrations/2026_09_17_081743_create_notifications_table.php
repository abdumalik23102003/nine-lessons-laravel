<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();        // ✅ UUID (Laravel default)
            $table->string('type');
            $table->morphs('notifiable');          // ✅ integer ID uchun to'g'ri
            $table->text('data');
            $table->timestamp('read_at')->nullable(); // ✅ timestamp (dateTime emas)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
