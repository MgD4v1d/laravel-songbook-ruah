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
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('artist');
            $table->longText('lyrics');
            $table->string('key', 150)->nullable();
            $table->string('rhythm', 150)->nullable();
            $table->string('tempo', 150)->nullable();
            $table->string('video_url', 500)->nullable();
            $table->timestamps();

            $table->index(['title', 'artist']);
            $table->fullText(['title', 'artist', 'lyrics']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
