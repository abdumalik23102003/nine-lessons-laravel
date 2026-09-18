<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('networks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // google, facebook, github
            $table->string('network_id'); // provider user ID
            $table->json('data')->nullable(); // extra data from provider
            $table->timestamps();

            $table->unique(['user_id', 'name', 'network_id']);
            $table->index(['name', 'network_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('networks');
    }
};
