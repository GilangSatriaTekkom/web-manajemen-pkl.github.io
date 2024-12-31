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

    public function projectCreators()
    {
        return $this->belongsToMany(User::class, 'projects_users')
                    ->withPivot('creator_project')
                    ->wherePivot('creator_project', '!=', null); // Hanya untuk creator_project
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('creator_project');
    }

}
