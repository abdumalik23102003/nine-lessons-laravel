<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('advert_dialogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advert_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('user_new_messages')->default(0);
            $table->unsignedInteger('client_new_messages')->default(0);
            $table->timestamps();

            $table->unique(['advert_id', 'client_id']);
        });

        Schema::create('advert_dialog_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dialog_id')->constrained('advert_dialogs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advert_dialog_messages');
        Schema::dropIfExists('advert_dialogs');
    }
};
