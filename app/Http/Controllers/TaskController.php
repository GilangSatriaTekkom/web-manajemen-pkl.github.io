<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Board;
use App\Models\Project;
use App\Models\Comment;
use App\Models\Description;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    public function getTaskData($projectId, $taskId)
    {

        // Memastikan taskId adalah numerik
        if (!is_numeric($taskId)) {
            return response()->json(['error' => 'Invalid Task ID'], 400);
        }

        $task = Task::find($taskId);
        // Memeriksa apakah task ditemukan
        if ($task) {
            // Mengambil data title, description, dan comments
            $title = $task->title;

            $comments = Comment::where('task_id', $taskId)->get();

            // Mengembalikan data dalam format JSON
            return response()->json([
                'title' => $title,
                'tasksDescription' => json_decode($task->description->text),
                'comments' => $comments
            ]);
        }

        // Jika task tidak ditemukan
        return response()->json(['error' => 'Task not found or does not belong to the project'], 404);
    }



    public function addComment(Request $request, $currentTaskId)
    {
        // Mencari task berdasarkan currentTaskId
        $task = Task::findOrFail($currentTaskId);

        // Membuat instance baru untuk Comment
        $comment = new Comment();

        // Mengisi kolom currentTaskId, text, dan user_id
        $comment->task_id = $task->id;
        $comment->comment = $request->input('text');
        $comment->user_id = auth()->id();  // Mendapatkan user_id dari pengguna yang sedang login

        // Menyimpan komentar ke database
        $comment->save();

        // Mengembalikan response berupa JSON dengan data komentar yang baru dibuat
        return response()->json($comment);
    }

    public function create()
    {
        // Menampilkan form untuk menambahkan task (jika ada tampilan terkait)
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'project_id' => 'required|exists:projects,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $projectId = $request->input('project_id');
        $board = Board::where('name', 'To Do')->firstOrFail();
        $project = Project::findOrFail($projectId);

        $description = Description::create([
            'text' => $request->input('description'),
        ]);

        // Simpan task ke tabel tasks
        $task = Task::create([
            'title' => $request->input('title'),
            'board_id' => $board->id,
            'status' => 'to_do',
            'project_id' => $project->id,
            'description_id' => $description->id,
        ]);

        // Redirect ke halaman detail proyek
        return redirect()
            ->route('project.show', ['id' => $project->id])
            ->with('success', 'Task berhasil dibuat!');
    }

    // Fungsi untuk menangani upload gambar lokal
    protected function handleUploadedImage($base64Image)
    {
        // Decode base64 image
        $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64Image));

        // Tentukan nama file
        $imageName = 'image_' . uniqid() . '.png';

        // Tentukan folder tujuan penyimpanan
        $imagePath = public_path('uploads/images/' . $imageName);

        // Simpan gambar ke folder
        file_put_contents($imagePath, $imageData);

        // Kembalikan URL gambar yang bisa diakses
        return url('uploads/images/' . $imageName);
    }



}
