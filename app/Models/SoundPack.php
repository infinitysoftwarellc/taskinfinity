<?php

// This model class represents sound pack data within the application.
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoundPack extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'files_json',
    ];

    protected $casts = [
        'files_json' => 'array',
    ];

    public function themes()
    {
        return $this->hasMany(Theme::class);
    }
}
