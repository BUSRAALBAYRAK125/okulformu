<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_notification_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->unique();

            $table->boolean('connection_request_enabled')->default(true);
            $table->boolean('comment_enabled')->default(true);
            $table->boolean('event_enabled')->default(true);
            $table->boolean('announcement_enabled')->default(true);

            $table->boolean('email_connection_request_enabled')->default(false);
            $table->boolean('email_comment_enabled')->default(false);
            $table->boolean('email_event_enabled')->default(false);
            $table->boolean('email_announcement_enabled')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notification_settings');
    }
};