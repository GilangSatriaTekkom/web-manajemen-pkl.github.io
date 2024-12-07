<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectUser extends Model
{
    protected $table = 'project_user';
    protected $fillable = ['user_id', 'project_id', 'creator_project', 'created_at', 'updated_at'];

    // Menambahkan relasi
    public function project()
    {
        return $this->belongsTo(Project::class, 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
