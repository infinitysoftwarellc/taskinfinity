<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XpEvent extends Model
{
    use HasFactory;

    protected $table = 'xp_events';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'source',
        'reference_id',
        'delta_xp',
        'note',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
