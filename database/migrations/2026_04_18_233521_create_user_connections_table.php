<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_connections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sender_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('receiver_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('status', ['pending', 'accepted', 'rejected', 'blocked'])
                ->default('pending');

            $table->timestamp('requested_at')->nullable();
            $table->timestamp('responded_at')->nullable();

            $table->timestamps();

            $table->unique(['sender_user_id', 'receiver_user_id'], 'user_connections_sender_receiver_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_connections');
    }
};