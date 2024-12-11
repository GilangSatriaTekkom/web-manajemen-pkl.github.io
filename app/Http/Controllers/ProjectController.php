<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Board;
use App\Models\Task;
use App\Models\Description;

class ProjectController extends Controller
{
    public function show($id)
    {
        // Ambil project berdasarkan ID yang diterima
        $project = Project::findOrFail($id);

        // Ambil semua tasks yang terkait dengan proyek ini
        $tasks = Task::where('project_id', $project->id)->get();

        // Mengelompokkan tasks berdasarkan status
        $columns = [
            ['title' => 'To Do', 'cards' => $tasks->where('status', 'to_do')->values()],
            ['title' => 'In Progress', 'cards' => $tasks->where('status', 'in_progress')->values()],
            ['title' => 'Done', 'cards' => $tasks->where('status', 'done')->values()],
        ];

        // Kirim data ke view
        return view('layouts.project', [
            'projectName' => $project->name,
            'columns' => $columns,
            'project' => $project,
            'tasks' => $tasks,

        ]);
    }
}
