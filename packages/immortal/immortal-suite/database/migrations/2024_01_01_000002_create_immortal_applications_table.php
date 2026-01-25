<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('immortal_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('status')->default('NEW');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->string('discord_user_id')->nullable();
            $table->string('ticket_id')->nullable();
            $table->string('guild_id')->nullable();
            $table->json('application_data')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('immortal_applications');
    }
};
