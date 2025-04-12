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
    Schema::create('comments', function (Blueprint $table) {
      $table->id();
      $table->string('comment_id')->unique(); // YouTube comment ID
      $table->foreignId('video_id')->constrained('videos')->onDelete('cascade');
      $table->text('text');
      $table->string('author_name');
      $table->string('author_channel_id');
      $table->integer('like_count')->default(0);
      $table->timestamp('published_at');
      $table->timestamps();

      $table->index('comment_id');
      $table->index('video_id');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('comments');
  }
};
