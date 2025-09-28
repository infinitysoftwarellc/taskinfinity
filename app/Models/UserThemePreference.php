<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserThemePreference extends Model
{
    use HasFactory;

    protected $table = 'user_theme_prefs';

    protected $fillable = [
        'user_id',
        'theme_code',
        'settings_json',
    ];

    protected $casts = [
        'settings_json' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
