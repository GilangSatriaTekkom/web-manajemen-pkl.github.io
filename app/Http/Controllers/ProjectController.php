<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Board;
use App\Models\Task;
use App\Models\Description;
use App\Models\User;

class ProjectController extends Controller
{
    public function show($id)
    {
        // Ambil project berdasarkan ID yang diterima
        $project = Project::findOrFail($id);

        $user = auth()->user();
        $roles = User::where('id', $user->id)
                    ->whereIn('roles', ['admin', 'pembimbing'])
                    ->first();

        // Ambil semua tasks yang terkait dengan proyek ini
        $tasks = Task::where('project_id', $project->id)->get();

        // Ambil semua boards yang terkait dengan project ini
        $boards = Board::select('id')->get();

        // Mengelompokkan tasks berdasarkan status
        $columns = [
            ['id' => $boards[0]->id, 'title' => 'To Do', 'cards' => $tasks->where('status', 'to_do')->values()],
            ['id' => $boards[1]->id, 'title' => 'In Progress', 'cards' => $tasks->where('status', 'in_progress')->values()],
            ['id' => $boards[2]->id, 'title' => 'Done', 'cards' => $tasks->where('status', 'done')->values()],
        ];

        // Kirim data ke view
        return view('layouts.project', [
            'projectName' => $project->name,
            'columns' => $columns,
            'project' => $project,
            'tasks' => $tasks,
            'roles' => $roles

        ]);
    }
}
