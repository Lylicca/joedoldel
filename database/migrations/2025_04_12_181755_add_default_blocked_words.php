<?php

use App\Models\BlockedWord;
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
    $words = [
      'gacor',
      'casino',
      'poker',
      'bet',
      'judi',
      'togel',
      'slot',
      'baccarat',
      'sbobet',
      'qq',
      'maxwin',
      'jp',
      'alexis17',
      'slotter99'
    ];

    foreach ($words as $word) {
      BlockedWord::create([
        'word' => $word,
        'weight' => 1.0
      ]);
    }
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {}
};
