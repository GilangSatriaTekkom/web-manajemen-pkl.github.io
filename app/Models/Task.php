<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'board_id',
        'status',
        'project_id',
        'comment_id',
        'description_id',
        'created_by',
        'worked_by',
    ];

    // Relasi ke Board
    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    // Relasi ke Project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Relasi ke Comment
    public function comments()
    {
        return $this->hasMany(Comment::class, 'task_id');
    }

    // Relasi ke Description
    public function description()
    {
        return $this->belongsTo(Description::class);
    }

    public function workedBy()
    {
        return $this->belongsTo(User::class, 'worked_by');
    }
}
