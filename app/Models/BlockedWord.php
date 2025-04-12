<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedWord extends Model
{
  protected $fillable = [
    'word',
    'weight',
    'category'
  ];

  protected $casts = [
    'weight' => 'float'
  ];
}
