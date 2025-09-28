<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanLimit extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_code',
        'limits_json',
    ];

    protected $casts = [
        'limits_json' => 'array',
    ];
}
