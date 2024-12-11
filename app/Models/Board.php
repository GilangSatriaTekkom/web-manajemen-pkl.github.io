<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;

    // Pastikan hanya 3 board yang ada dan tidak bisa diubah
    protected $fillable = ['name'];

    // Tentukan untuk mencegah perubahan board
    public static function boot()
    {
        parent::boot();

        static::updating(function ($board) {
            // Prevent updates to boards
            return false;
        });
    }

    // Menambahkan properti untuk memastikan board hanya 3
    public static function getPredefinedBoards()
    {
        return [
            'To Do',
            'In Progress',
            'Done'
        ];
    }
}
