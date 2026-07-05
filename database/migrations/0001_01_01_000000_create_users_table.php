<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
                    Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('surname', 100);
                $table->string('email', 191)->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');

                $table->enum('user_type', ['student', 'graduate', 'academic', 'admin'])->default('student');

                $table->string('student_no', 30)->nullable();
                $table->year('graduation_year')->nullable();

                $table->string('department', 100)->default('Bilgisayar Öğretmenliği');

                $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->text('reject_reason')->nullable();

                $table->rememberToken();
                $table->timestamp('last_login_at')->nullable();
                $table->timestamps();
            });
    }
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
