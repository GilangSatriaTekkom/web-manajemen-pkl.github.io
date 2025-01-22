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

    public function showParticipants($id)
    {
        $project = Project::findOrFail($id);

        // Specify the table name for ambiguous columns
        $participants = $project->participants()
            ->select('users.id', 'users.name', 'users.profile_pict')
            ->get();

        // Get available users
        $availableUsers = User::whereNotIn('id', $participants->pluck('id'))
            ->whereNotIn('roles', ['admin', 'pembimbing'])
            ->select('users.id', 'users.name')
            ->get();

            return view('project.participants', [
                'project' => $project,
                'participants' => $participants,
                'availableUsers' => $availableUsers,
            ]);
    }


    public function addParticipant(Request $request, $projectId)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $project = Project::findOrFail($projectId);

        if (!$project->participants()->where('users.id', $request->user_id)->exists()) {

            $creatorProject = DB::table('projects_users')
            ->where('project_id', $projectId)
            ->value('creator_project'); // Ambil satu nilai

            $project->participants()->attach($request->user_id, [
                'creator_project' => $creatorProject,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Tambahkan pesan flash
            session()->flash('success', 'Participant added successfully');
        } else {
            session()->flash('info', 'Participant is already part of the project');
        }

        return redirect()->route('project.participants', ['id' => $projectId]);
    }


    public function removeParticipant(Request $request, $projectId, $userId)
    {
        $project = Project::findOrFail($projectId);

        // Periksa apakah user ada di project
        if ($project->participants()->where('user_id', $userId)->exists()) {
            $project->participants()->detach($userId);

            // Tambahkan pesan flash
            session()->flash('success', 'Participant removed successfully');
        } else {
            session()->flash('info', 'Participant not found in the project');
        }

        // Redirect kembali ke halaman peserta
        return redirect()->route('project.participants', ['id' => $projectId]);
    }



    public function getParticipants($id)
    {
        $project = Project::findOrFail($id);

        $participants = $project->participants()
            ->select('users.id', 'users.name', 'users.profile_pict')
            ->get()
            ->map(function ($participant) {
                return [
                    'id' => $participant->id,
                    'name' => $participant->name,
                    'profile_image' => $participant->profile_pict
                        ? asset('storage/' . $participant->profile_pict)
                        : asset('images/default-profile.png')
                ];
            });

        return response()->json([
            'participants' => $participants
        ]);
    }



    public function searchProjects(Request $request)
    {
        $query = $request->get('query', '');

        // Cari proyek berdasarkan ID atau nama
        $projects = Project::query()
            ->where('id', $query) // Pencarian berdasarkan ID
            ->orWhere('name', 'like', "%{$query}%") // Pencarian berdasarkan nama proyek
            ->with(['participants', 'projectCreators']) // Relasi jika diperlukan
            ->get();

        return view('layouts.dashboard', compact('projects'));
    }



    public function profile($id)
    {
        $user = User::findOrFail($id);
        return view('layouts.profile', compact('user'));
    }

    public function destroy($id)
    {
        // Cari project berdasarkan ID
        $project = Project::findOrFail($id);

        // Hapus project
        $project->delete();

        // Kembalikan respons sukses
        return response()->json(['message' => 'Project deleted successfully']);
    }


}
