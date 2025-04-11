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
    Schema::create('videos', function (Blueprint $table) {
      $table->id();
      $table->foreignId('channel_id')->constrained()->cascadeOnDelete();
      $table->string('video_id')->unique(); // YouTube video ID
      $table->string('title');
      $table->text('description');
      $table->string('thumbnail_url');
      $table->dateTime('published_at');
      $table->unsignedBigInteger('view_count')->default(0);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('videos');
  }
};
