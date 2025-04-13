<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CleanupStatistics extends Model
{
  protected $fillable = [
    'cleanup_date',
    'comments_processed',
    'spam_removed',
    'comments_held_for_review',
    'videos_processed',
  ];

  protected $casts = [
    'cleanup_date' => 'date',
  ];
}
