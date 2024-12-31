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

        $projectsFromParticipants = Project::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['projectCreators', 'participants'])->get();

        $projectsFromCreator = Project::whereHas('projectCreators', function ($query) use ($user) {
            $query->where('creator_project', $user->id);
        })->with(['projectCreators'])->get();

        $projects = $projectsFromParticipants->merge($projectsFromCreator);



        $creatorProjects = $projectsFromCreator->flatMap(function ($project) {
            return $project->projectCreators->map(function ($creator) {
                return $creator->pivot->creator_project;
            });
        });

        $userNames = User::whereIn('id', $creatorProjects)->pluck('name');
        $userName = $userNames->first();

         // Ambil gambar peserta untuk setiap proyek
        $projectParticipants = $projects->flatMap(function ($project) {
            return $project->participants->map(function ($participant) use ($project) {
                // dd($participant->name);
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
        // dd($projectParticipants);


        return view('layouts.dashboard',['projectParticipants' => $projectParticipants], compact('projects', 'userName', 'creatorProjects'));
    }



    public function assignProject(Request $request)
    {
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
