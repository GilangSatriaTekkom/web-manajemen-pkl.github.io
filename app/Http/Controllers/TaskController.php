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
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;



class TaskController extends Controller
{

    public function getTaskData(Request $request, $projectId, $taskId)
    {


        // Cek jika permintaan bukan AJAX
        if (!$request->ajax()) {
            return redirect("/project/{$projectId}/");
        }

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

            $tasksDescription = $task->description->text ?? null;
            $tasksDescription = $tasksDescription ? json_decode($tasksDescription) : null;

            if (empty($tasksDescription)) {
                $tasksDescription = 'Deskripsi tugas tidak tersedia'; // Atur nilai default
            }


            // Mengembalikan data dalam format JSON
            return response()->json([
                'title' => $title,
                'tasksDescription' => $tasksDescription,
                'status' => $status,
                'comments' => $comments,
                'worked_by' => $workedby,
                'profile_picture' => $profilePicture,
                'user_name' => $userName,
                'profileUrl' => url('profile/' . $workedby),
                'loggedInUserId' => auth()->id(),
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
        Log::info('Request received:', $request->all());

        $data = $request->all();
        Log::info('Data extracted:', $data);

        // Menyimpan gambar jika ada
        $imageUrls = [];
        if (isset($data['images']) && $data['images']) {
            // Mendapatkan gambar URL yang dikirimkan dalam data
            $imageUrls = $data['images']; // Asumsi image sudah berupa URL
            Log::info('Images received:', $imageUrls);
        }

        // Mendapatkan data lainnya
        $projectId = $request->input('project_id');
        Log::info('Project ID:', ['project_id' => $projectId]);

        $board = Board::where('name', 'To Do')->firstOrFail();
        Log::info('Board found:', ['board' => $board]);

        $project = Project::findOrFail($projectId);
        Log::info('Project found:', ['project' => $project]);

        $user = auth()->id();
        Log::info('Authenticated user:', ['user_id' => $user]);

        // Membuat deskripsi dengan URL gambar
        $descriptionText = $request->input('description');
        Log::info('Description text:', ['description' => $descriptionText]);

        // Mengonversi deskripsi menjadi format Quill JSON
        $quillDescription = json_decode($descriptionText, true);
        Log::info('Quill description:', ['quill_description' => $quillDescription]);

        // Memproses gambar jika ada dalam 'images'
        if (isset($data['images']) && is_array($data['images']) && !empty($data['images'])) {
            foreach ($data['images'] as $image) {
                if ($image && (filter_var($image, FILTER_VALIDATE_URL) || str_starts_with($image, '/storage/'))) {
                    // Menambahkan gambar ke dalam konten Quill jika gambar valid (berupa URL)
                    $quillDescription['ops'][] = [
                        'insert' => [
                            'image' => $image, // Gambar dalam format Quill
                        ]
                    ];
                    Log::info('Valid image added to Quill:', ['image' => $image]);
                } else {
                    Log::info('Invalid image skipped:', ['image' => $image]);
                }
            }
        }

        // Memproses link jika ada dalam 'links'
        if (isset($data['links']) && !empty($data['links'])) {
            $links = is_array($data['links']) ? $data['links'] : [$data['links']]; // Pastikan links adalah array
            foreach ($links as $link) {
                if ($link) {
                    $quillDescription['ops'][] = [
                        'insert' => $link,
                        'attributes' => ['link' => $link] // Menambahkan link dengan atribut 'link'
                    ];
                    Log::info('Link added to Quill:', ['link' => $link]);
                }
            }
        }

        // Menghapus semua objek dengan nilai {"insert":{"image":true}} dari $descriptionData['ops']
        $quillDescription['ops'] = array_filter($quillDescription['ops'], function ($item) {
            // Memastikan item bukan gambar dengan value true
            return !(isset($item['insert']['image']) && $item['insert']['image'] === true);
        });

        // Re-index array setelah filter
        $quillDescription['ops'] = array_values($quillDescription['ops']);

        // Simpan deskripsi ke database sebagai string biasa
        $description = Description::create([
            'text' => json_encode($quillDescription), // Simpan sebagai string biasa
        ]);

        Log::info('Description saved:', [$description]);

        Log::info('Description saved:', ['description_id' => $description->id]);

        // Simpan task ke tabel tasks
        $task = Task::create([
            'title' => $request->input('title'),
            'board_id' => $board->id,
            'status' => 'to_do',
            'project_id' => $project->id,
            'description_id' => $description->id,
            'created_by' => $user,
        ]);
        Log::info('Task created:', ['task_id' => $task->id]);

        return response()->json([
            'success' => true,
            'message' => 'Task berhasil dibuat!',
            'task_id' => $task->id,
            'project_id' => $project->id,
            'description_id' => $description->id,
        ]);

    }





    public function uploadImage(Request $request)
{

    Log::info("message: ", $request->all());

    // Check if 'images' are uploaded
    if ($request->hasFile('images')) {
        $imageUrls = [];

        foreach ($request->file('images') as $image) {
            // Log the file name and real path of the uploaded image

            // Store the image and get its URL
            $path = $image->store('images', 'public');
            $imageUrls[] = Storage::url($path);
        }

        return response()->json(['imageUrls' => $imageUrls]);
    }

    // Check if 'imagesEdit' are uploaded
    if ($request->hasFile('imagesEdit')) {
        $imageUrls = [];

        foreach ($request->file('imagesEdit') as $image) {
            // Log the file name and real path of the uploaded image
            // Store the image and get its URL
            $path = $image->store('imagesEdit', 'public');
            $imageUrls[] = Storage::url($path);
        }

        return response()->json(['imageUrls' => $imageUrls]);
    }

    // Return error if no images were uploaded
    return response()->json(['error' => 'No image uploaded'], 400);
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

    public function editData($projectId, $taskId)
    {
        // Ambil task berdasarkan taskId dan projectId
        $task = Task::where('project_id', $projectId)
                    ->where('id', $taskId)
                    ->first();

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        $description_id = $task->description_id;

        $getdescription = Description::where('id', $description_id)->first();

        // Kembalikan data task dalam format JSON
        return response()->json([
            'task' => $task,
            'description' => $getdescription
        ]);
    }


    public function updateTask(Request $request, $taskId)
    {
        $response = [];

        Log::info('Request data:', $request->all()); // Log data request untuk debugging

        // Mengakses data request
        $data = $request->all();

        // Memastikan taskId valid
        if (is_null($data['taskId'])) {
            return response()->json([
                'success' => false,
                'message' => 'Task ID tidak ditemukan.',
            ]);
        }

        // Cari task yang ingin diedit
        $task = Task::findOrFail($taskId);

        // Mengambil description_id yang ada di tabel Task
        $descriptionId = $task->description_id;

        // Cari deskripsi yang ada menggunakan description_id
        $description = Description::findOrFail($descriptionId);

        // Menyimpan gambar jika ada
        $imageUrl = null;
        if (isset($data['image']) && $data['image']) {
            // Mendapatkan gambar URL yang dikirimkan dalam data
            $imageUrl = $data['image']; // Asumsi image sudah berupa URL
        }

        // Mengakses dan memproses deskripsi yang berformat JSON (dari Quill)
        $descriptionText = $data['description'] ?? 'Tidak ada deskripsi saat ini';
        $descriptionData = json_decode($descriptionText, true); // Mengubah JSON string menjadi array

        // Memproses gambar jika ada dalam 'imagesEdit'
        if (isset($data['imagesEdit']) && is_array($data['imagesEdit']) && !empty($data['imagesEdit'])) {
            foreach ($data['imagesEdit'] as $image) {
                if ($image) {
                    // Menambahkan gambar ke dalam konten Quill
                    $descriptionData['ops'][] = [
                        'insert' => [
                            'image' => $image, // Gambar dalam format Quill
                        ]
                    ];
                }
            }
        }

        // Memproses link jika ada dalam 'links'
        if (isset($data['links']) && !empty($data['links'])) {
            $links = is_array($data['links']) ? $data['links'] : [$data['links']]; // Pastikan links adalah array
            foreach ($links as $link) {
                if ($link) {
                    $descriptionData['ops'][] = [
                        'insert' => $link,
                        'attributes' => ['link' => $link] // Menambahkan link dengan atribut 'link'
                    ];
                }
            }
        }

        // Mengonversi kembali deskripsi ke format JSON Quill
        $updatedDescriptionText = json_encode($descriptionData);

        // Hapus deskripsi lama dan simpan yang baru
        $description->text = $updatedDescriptionText; // Mengupdate teks deskripsi dengan format Quill
        $description->save(); // Simpan perubahan

        // Update task dengan data baru
        $task->title = $data['titleTaskEdit'];
        $task->project_id = $data['project_id'];
        $task->description_id = $description->id; // Pastikan description_id tetap sama
        $task->save();

        // Mengembalikan respons JSON
        return response()->json([
            'success' => true,
            'message' => 'Task berhasil diperbarui!',
            'task' => $task,
        ]);
    }









}
