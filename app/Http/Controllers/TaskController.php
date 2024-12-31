<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Board;
use App\Models\Project;
use App\Models\Comment;
use App\Models\Description;
use App\Models\Report;
use App\Models\User;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;



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
            $status = $task->status;
            $workedby = $task->worked_by;

            // Mengambil data user berdasarkan worked_by
            $userPict = User::where('id', $workedby)->first();

            // Jika user ditemukan, ambil profile_picture dan username, jika tidak, set null
            $profilePicture = $userPict && $userPict->profile_pict
                ? asset('storage/' . $userPict->profile_pict) // Jika ada, gunakan gambar dari database
                : null; // Jika tidak ada, set null

            $userName = $userPict ? $userPict->name : null; // Jika ada user, ambil nama, jika tidak, set null

            // Mengambil komentar untuk task
            $comments = Comment::where('task_id', $taskId)
            ->with('user:id,name,profile_pict') // Memuat hanya kolom yang diperlukan
            ->get();

            $comments = $comments->map(function ($comment) {
                if ($comment->user && $comment->user->profile_pict) {
                    // Tambahkan properti image_url ke setiap elemen
                    $comment->image_url = asset('storage/' . $comment->user->profile_pict);
                }

                if ($comment->created_at) {
                    // Ubah format created_at menjadi waktu relatif
                    $comment->time_ago = Carbon::parse($comment->created_at)->diffForHumans();
                }
                return $comment; // Penting: kembalikan elemen yang telah dimodifikasi
            });





            // Mengembalikan data dalam format JSON
            return response()->json([
                'title' => $title,
                'tasksDescription' => json_decode($task->description->text),
                'status' => $status,
                'comments' => $comments,
                'worked_by' => $workedby,
                'profile_picture' => $profilePicture,
                'user_name' => $userName,
                'profileUrl' => url('profile/' . $workedby),
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

         // Memuat kembali komentar dengan relasi user
        $comment = Comment::with('user:id,name,profile_pict')
        ->find($comment->id);

        // Menambahkan properti tambahan
        if ($comment->user && $comment->user->profile_pict) {
            $comment->image_url = asset('storage/' . $comment->user->profile_pict);
        }

        if ($comment->created_at) {
            $comment->time_ago = $comment->created_at->diffForHumans();
        }


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

        $user = auth()->id();

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
            'created_by' => $user,
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


    public function updateBoard(Request $request)
{
    $user = auth()->id();
    // Validasi request untuk task_id dan board_id
    $validated = $request->validate([
        'task_id' => 'required|exists:tasks,id',
    ]);

    $task = Task::find($validated['task_id']);

    if ($task) {
        // Simpan nilai board_id sebelumnya untuk log dan respon
        $oldBoardId = $task->board_id;

        \Log::info('Before Update: ', ['task_id' => $task->id, 'board_id' => $oldBoardId]);

        // Logika perubahan board_id dan update status task
        switch ($oldBoardId) {
            case 1: // In Progress
                $task->board_id = 2;
                $task->status = 'in_progress'; // Mengupdate status ke 'in_progress'
                Task::where('id', $validated['task_id'])
                    ->update(['worked_by' => $user]);
                break;

            case 2: // Done
                $task->board_id = 3;
                $task->status = 'done'; // Mengupdate status ke 'done'

                $projectName = Project::where('id', $task->project_id)->first();
                $workedByUser = User::where('id', $task->worked_by)->first();

                // Buat entri baru di tabel Report
                $updateReport = Report::create([
                    'project_name' => $projectName->name,
                    'task_name' => $task->title, // Ambil title dari task
                    'status' => 'done', // Status baru
                    'time' => now(), // Waktu saat ini
                    'worked_by' => $workedByUser->name,
                ]);

                break;

            default:
                // Jika board_id tidak valid, kembalikan dengan error
                return response()->json([
                    'message' => 'Invalid board_id',
                    'success' => false,
                    'error' => 'The provided board_id is not valid.',
                ], 400);
        }

        // Simpan perubahan pada task
        $task->save();

        \Log::info('After Update: ', [
            'task_id' => $task->id,
            'old_board_id' => $oldBoardId,
            'new_board_id' => $task->board_id,
            'new_status' => $task->status,
        ]);

        // Respon sukses dengan informasi tambahan
        return response()->json([
            'message' => 'Task updated successfully',
            'success' => true,
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'old_board_id' => $oldBoardId,
                'new_board_id' => $task->board_id,
                'new_status' => $task->status,
            ],
        ]);
    }

    // Respon error jika task tidak ditemukan
    \Log::error('Task not found: ', ['task_id' => $validated['task_id']]);
    return response()->json([
        'message' => 'Task not found',
        'success' => false,
        'error' => [
            'task_id' => $validated['task_id'],
            'reason' => 'Task does not exist or already deleted.',
        ],
    ], 404);
}






}
