<?php

// This model class represents economy wallet data within the application.
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EconomyWallet extends Model
{
    use HasFactory;

    protected $table = 'economy_wallet';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'coins_balance',
        'updated_at',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
