<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects'; // Pastikan nama tabelnya benar
    // Tentukan kolom yang boleh diisi
    protected $fillable = ['name'];

    public function participants()
    {
        return $this->belongsToMany(User::class, 'projects_users', 'project_id', 'user_id')
            ->withPivot('creator_project') // Kolom tambahan di tabel pivot
            ->withTimestamps(); // Untuk mencatat created_at dan updated_at
    }

    public function projectUser()
    {
        // Assuming 'user_id' is the foreign key in the 'projects' table
        return $this->belongsTo(User::class, 'creator_project', 'id');
    }
}
