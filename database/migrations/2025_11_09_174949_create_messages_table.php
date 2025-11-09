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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
             $table->foreignId('recipient_id')
                ->constrained('contacts')
                ->onDelete('cascade');
            $table->foreignId('sender_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('channel_id')
                ->constrained('channels');
            $table->text('content');
            $table->enum('status', ['sending', 'sent', 'failed'])->default('sending');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
