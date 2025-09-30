<?php

// This model class represents theme data within the application.
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'palette_json',
        'background_asset',
        'sound_pack_id',
    ];

    protected $casts = [
        'palette_json' => 'array',
    ];

    public function soundPack()
    {
        return $this->belongsTo(SoundPack::class);
    }
}
