<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('immortal_application_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('discord_user_id');
            $table->string('ticket_id');
            $table->string('guild_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('immortal_application_sessions');
    }
};
