<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
  use HasFactory;

  protected $fillable = [
    'user_id',
    'type',
    'name',
    'value',
    'is_global',
  ];

  public function scopeActive($query, $value = true)
  {
    return $query->where('status', $value);
  }

  public function scopeSetting($query, string $name, bool $global = false)
  {
    $q = $query->where('name', $name);
    if ($global) {
      return $q->where('is_global', $global);
    } else {
      if (Auth::check()) {
        return $q->where('user_id', Auth::id());
      } else {
        return $q;
      }
    }
  }
}
