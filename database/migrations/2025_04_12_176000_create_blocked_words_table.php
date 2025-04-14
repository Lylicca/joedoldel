<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('blocked_words', function (Blueprint $table) {
      $table->id();
      $table->string('word')->unique();
      $table->float('weight')->default(0.5);
      $table->string('category')->nullable();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('blocked_words');
  }
};
