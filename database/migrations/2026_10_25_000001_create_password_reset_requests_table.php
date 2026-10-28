<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_reset_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('otp_code', 6)->nullable();
            $table->string('temp_password')->nullable();
            $table->timestamp('temp_password_sent_at')->nullable();
            $table->timestamp('temp_password_expires_at')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'auto_reset'])->default('pending');
            $table->unsignedTinyInteger('request_count')->default(0);
            $table->timestamp('blocked_until')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->timestamp('request_expires_at')->nullable(); // 24 jam dari waktu request
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_requests');
    }
};
