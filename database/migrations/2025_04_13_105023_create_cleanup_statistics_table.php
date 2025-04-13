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
    Schema::create('cleanup_statistics', function (Blueprint $table) {
      $table->id();
      $table->date('cleanup_date');
      $table->integer('comments_processed')->default(0);
      $table->integer('spam_removed')->default(0);
      $table->integer('comments_held_for_review')->default(0);
      $table->integer('videos_processed')->default(0);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('cleanup_statistics');
  }
};
