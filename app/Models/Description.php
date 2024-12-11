<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Description extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'text', 'image', 'url'];

    public function task()
    {
        return $this->hasOne(Task::class, 'description_id');
    }
}
