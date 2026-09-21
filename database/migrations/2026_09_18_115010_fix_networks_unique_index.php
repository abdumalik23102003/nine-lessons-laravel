<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('networks', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('networks', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'name', 'network_id']);
        });

        Schema::table('networks', function (Blueprint $table) {
            $table->unique(['name', 'network_id']);
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('networks', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('networks', function (Blueprint $table) {
            $table->dropUnique(['name', 'network_id']);
        });

        Schema::table('networks', function (Blueprint $table) {
            $table->unique(['user_id', 'name', 'network_id']);
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }
};
