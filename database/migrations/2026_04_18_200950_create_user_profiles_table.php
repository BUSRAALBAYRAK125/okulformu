<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('headline', 160)->nullable();
            $table->text('bio')->nullable();

            $table->string('photo', 255)->nullable();
            $table->string('cover_photo', 255)->nullable();

            $table->string('city', 100)->nullable();

            $table->string('linkedin_url', 255)->nullable();
            $table->string('github_url', 255)->nullable();
            $table->string('website_url', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};