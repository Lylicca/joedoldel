<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
  protected $fillable = [
    'comment_id',
    'video_id',
    'text',
    'author_name',
    'author_channel_id',
    'like_count',
    'published_at',
    'removed_at',
    'spam_probability'
  ];

  protected $casts = [
    'published_at' => 'datetime',
    'removed_at' => 'datetime',
    'like_count' => 'integer',
    'spam_probability' => 'float',
  ];

  public function video(): BelongsTo
  {
    return $this->belongsTo(Video::class);
  }
}
