<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
  protected $fillable = [
    'channel_id',
    'video_id',
    'title',
    'description',
    'thumbnail_url',
    'published_at',
    'view_count',
    'comment_count',
    'visibility',
    'livestream_status',
  ];

  protected $casts = [
    'published_at' => 'datetime',
    'view_count' => 'integer',
    'comment_count' => 'integer',
    'visibility' => 'string',
    'livestream_status' => 'string',
  ];

  public function channel(): BelongsTo
  {
    return $this->belongsTo(Channel::class);
  }

  public function comments()
  {
    return $this->hasMany(Comment::class);
  }
}
