<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function dashboard()
{
    // Check if user is authenticated
    if (!auth()->check()) {
        return redirect()->route('login')->with('error', 'Please log in to access the dashboard');
    }

    $user = auth()->user();

    // Ambil proyek dari peserta
    $projectsFromParticipants = Project::whereHas('participants', function ($query) use ($user) {
        $query->where('user_id', $user->id);
    })->with(['projectCreators', 'participants'])->get();

    // Ambil proyek dari creator
    $projectsFromCreator = Project::whereHas('projectCreators', function ($query) use ($user) {
        $query->where('creator_project', $user->id);
    })->with(['projectCreators'])->get();

    // Gabungkan proyek dari peserta dan creator
    $projects = $projectsFromParticipants->merge($projectsFromCreator);

    // Ambil gambar creator dari proyek yang dibuat oleh pengguna
     $creatorProjects = $projectsFromCreator->flatMap(function ($project) {
         return $project->projectCreators->map(function ($creator) {
             return $creator->pivot->creator_project;
         });
     });
     $projectIds = $projects->pluck('id');

     $creatorProjectPict = ProjectUser::whereIn('project_id', $projectIds)->pluck('creator_project');


    // Ambil gambar creator dari user yang terlibat dalam proyek
    $creatorPict = User::whereIn('id', $creatorProjectPict)->pluck('profile_pict')->first();


    // Ambil gambar peserta untuk setiap proyek
    $projectParticipants = $projects->flatMap(function ($project) {
        return $project->participants->map(function ($participant) use ($project) {
            return [
                'project_id' => $project->id,
                'project_name' => $project->name ?? 'Unknown Project',
                'participants' => $project->participants->map(function ($participant) {
                    return [
                        'id' => $participant->id ?? null,
                        'name' => $participant->name ?? 'Unknown',
                        'profile_image' => $participant->profile_pict
                            ? asset('storage/' . $participant->profile_pict)
                            : asset('images/default-profile.png'),
                    ];
                })->toArray(),
            ];
        });
    })->unique('project_id')->values();

    // Bagikan data creatorPict ke view
    view()->share('creatorPict', $creatorPict);

    // Kirim data ke view
    return view('layouts.dashboard', [
        'projectParticipants' => $projectParticipants,
        'projects' => $projects,
        'creatorProjects' => $creatorProjects
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

            $user = auth()->user();

        // Mendapatkan proyek-proyek yang dibuat oleh pengguna
        $projectsFromCreator = Project::whereHas('projectCreators', function ($query) use ($user) {
            $query->where('creator_project', $user->id);
        })->with(['projectCreators'])->get();

        // Menyusun daftar ID pengguna yang membuat proyek
        $creatorProjects = $projectsFromCreator->flatMap(function ($project) {
            return $project->projectCreators->map(function ($creator) {
                return $creator->pivot->creator_project;
            });
        });

        // Mendapatkan gambar profil dari pengguna yang membuat proyek
        $userNames = User::whereIn('id', $creatorProjects)->pluck('profile_pict');
        $creatorPict = $userNames->first();

        // if ($creatorPict) {
        //     $creatorPict = asset('storage/' . $creatorPict);
        // } else {
        //     $creatorPict = asset('images/default-profile.png');
        // }

        // Pass the $creatorPict to the view
        return view('layouts.dashboard', compact('projects', 'creatorPict'));
    }




    public function assignProject(Request $request)
    {

        Log::info("message", ['data' => $request->all()]);
        // Mengubah peserta menjadi array jika datang sebagai string
        $participants = json_decode($request->input('participants'));

        // Validasi apakah peserta valid
        if (!is_array($participants)) {
            return redirect()->back()->withErrors(['participants' => 'Invalid participants format']);
        }

        // Melakukan validasi
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Simpan proyek
        $project = new Project();
        $project->name = $validated['name'];
        $project->save();

        // Ambil ID user yang sedang login
        $creatorId = Auth::id();

        // Siapkan data untuk tabel projects_users
        $data = [];
        foreach ($participants as $participant) {
            $data[] = [
                'user_id' => $participant,
                'project_id' => $project->id,
                'creator_project' => $creatorId, // Isi dengan ID user yang login
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Log::info("message", ['data' => $data]);

        // Simpan data ke tabel projects_users
        \DB::table('projects_users')->insert($data);

        // Redirect ke halaman proyek yang baru saja dibuat
        return redirect()->route('project.show', ['id' => $project->id]);
    }


    public function showParticipants($id)
    {
        $projects = Project::find($id);

        if (!$projects) {
            return redirect()->route('some.fallback.route')->with('error', 'Project not found.');
        }


        $participants = $projects->participants()->select('users.id', 'users.name', 'users.profile_pict')->get();


        return response()->json([
            'participants' => $participants
        ]);
    }

    public function storeParticipants(Request $request, $id)
    {

    }



}
