<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'user_id',
        'store_item_id',
        'is_equipped',
        'equipped_at',
    ];

    protected $casts = [
        'is_equipped' => 'boolean',
        'equipped_at' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(StoreItem::class, 'store_item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
