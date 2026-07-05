<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_privacy_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('profile_visibility', ['public', 'connections', 'private'])
                ->default('public');

            $table->enum('email_visibility', ['public', 'connections', 'private'])
                ->default('private');

            $table->enum('clubs_visibility', ['public', 'connections', 'private'])
                ->default('public');

            $table->enum('connections_visibility', ['public', 'connections', 'private'])
                ->default('private');

            $table->enum('social_links_visibility', ['public', 'connections', 'private'])
                ->default('public');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_privacy_settings');
    }
};