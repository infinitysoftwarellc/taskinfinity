<?php

// This model class represents task list data within the application.
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskList extends Model
{
    use HasFactory;

    protected $table = 'lists';

    protected $fillable = [
        'user_id',
        'name',
        'view_type',
        'color',
        'icon',
        'folder_id',
        'position',
        'is_pinned',
        'archived_at',
    ];

    protected $casts = [
        'archived_at' => 'datetime',
        'is_pinned' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function missions()
    {
        return $this->hasMany(Mission::class, 'list_id');
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }
}
