<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Board;
use App\Models\Task;
use App\Models\Description;
use App\Models\User;
use App\Models\Comment;

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
        $tasks = Task::where('project_id', $project->id)->with('workedBy')->get();

        // Ambil semua boards yang terkait dengan project ini
        $boards = Board::select('id')->get();

        // Mengelompokkan tasks berdasarkan status
        $columns = [
            ['id' => $boards[0]->id, 'title' => 'To Do', 'cards' => $tasks->where('status', 'to_do')->values()],
            ['id' => $boards[1]->id, 'title' => 'In Progress', 'cards' => $tasks->where('status', 'in_progress')->values()],
            ['id' => $boards[2]->id, 'title' => 'Done', 'cards' => $tasks->where('status', 'done')->values()],
        ];

        foreach ($tasks as $task) {
            // Menghitung jumlah komentar untuk setiap task
            $task->comment_count = Comment::where('task_id', $task->id)->count();

            // Mengambil deskripsi yang terkait dengan task
            $description = $task->description; // Menggunakan relasi yang sudah ada

            if ($description) {
                // Menghitung jumlah file (gambar) dalam deskripsi task
                $description_text = $description->text; // Sesuaikan dengan kolom yang menyimpan teks deskripsi

                // Hitung jumlah gambar menggunakan regex
                $task->file_count = preg_match_all('/"image":"data:image\/[a-zA-Z]*;base64,[^"]+"/', $description_text);
            } else {
                $task->file_count = 0; // Jika tidak ada deskripsi, set file_count ke 0
            }
        }



        // Kirim data ke view
        return view('layouts.project', [
            'projectName' => $project->name,
            'projectId' => $project->id,
            'columns' => $columns,
            'project' => $project,
            'tasks' => $tasks,
            'roles' => $roles

        ]);
    }
}
