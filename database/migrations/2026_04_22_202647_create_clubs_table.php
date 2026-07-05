<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('slug')->unique();

            $table->string('short_description', 255)->nullable();
            $table->text('description')->nullable();

            $table->string('logo', 255)->nullable();
            $table->string('cover_image', 255)->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamp('published_at')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['status', 'is_active', 'published_at']);
            $table->index(['created_by', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};