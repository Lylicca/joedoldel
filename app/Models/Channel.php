<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Channel extends Model
{
  protected $fillable = [
    'channel_id',
    'name',
    'image_url',
    'subscriber_count',
    'video_count',
    'user_id',
  ];

  protected $casts = [
    'subscriber_count' => 'integer',
    'video_count' => 'integer',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function videos(): HasMany
  {
    return $this->hasMany(Video::class);
  }
}
