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
        Schema::create('adverts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('region_id')->nullable()->constrained();
            $table->string('title');
            $table->unsignedInteger('price');
            $table->text('address');
            $table->text('content');
            $table->string('status', 16)->default('draft');
            $table->text('reject_reason')->nullable();
            $table->timestamps();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            // CHANGE 15: Add indexes for performance
            // These indexes speed up filtering and sorting queries
            $table->index('user_id');           // For finding user's adverts
            $table->index('status');            // For filtering by status
            $table->index('category_id');       // For filtering by category
            $table->index('region_id');         // For filtering by region
            $table->index('published_at');      // For sorting by publish date
            $table->index('expires_at');        // For finding expired adverts
            $table->index(['status', 'created_at']); // Composite index for moderation list
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adverts');
    }
};
