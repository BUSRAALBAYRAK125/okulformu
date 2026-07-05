<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('club_members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('club_id')
                ->constrained('clubs')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('role', ['founder', 'president', 'manager', 'member'])->default('member');
            $table->enum('status', ['pending', 'approved', 'rejected', 'left'])->default('approved');

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('joined_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            $table->unique(['club_id', 'user_id']);
            $table->index(['club_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_members');
    }
};