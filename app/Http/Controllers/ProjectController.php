<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    public function show($id)
    {
        // Ambil project berdasarkan ID yang diterima
        $project = Project::findOrFail($id);
        $projectName = $project->name; // Mengambil nama proyek dari tabel projects

        // Misalnya Anda mengambil kolom 'cards' dan status proyek
        $columns = [
            ['title' => 'To Do', 'cards' => [['title' => 'Task 1', 'description' => 'Description for Task 1']]],
            ['title' => 'In Progress', 'cards' => [['title' => 'Task 2', 'description' => 'Description for Task 2']]],
            ['title' => 'Done', 'cards' => [['title' => 'Task 3', 'description' => 'Description for Task 3']]],
        ];

        // Kirim data ke view
        return view('layouts.project', compact('projectName', 'columns'));
    }
}
