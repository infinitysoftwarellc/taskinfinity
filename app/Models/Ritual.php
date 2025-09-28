<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ritual extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'frequency',
    ];

    public function entries()
    {
        return $this->hasMany(RitualEntry::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
