<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('body');

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamp('published_at')->nullable();

            $table->boolean('is_pinned')->default(false);
            $table->timestamp('pinned_at')->nullable();
            $table->foreignId('pinned_by')->nullable()->constrained('users')->nullOnDelete();

            $table->boolean('allow_comments')->default(true);
            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('view_count')->default(0);

            $table->timestamps();

            $table->index(['status', 'is_active', 'published_at']);
            $table->index(['category_id', 'status', 'is_active']);
            $table->index(['is_pinned', 'pinned_at', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};