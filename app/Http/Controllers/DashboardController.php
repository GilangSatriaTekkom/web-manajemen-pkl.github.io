<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            // Redirect to login page if not authenticated
            return redirect()->route('login')->with('error', 'Please log in to access the dashboard');
        }

        $user = auth()->user();

        $projects = Project::whereHas('participants', function ($query) use ($user) {
            // Kondisi pertama: memeriksa user_id di tabel participants
            $query->where('user_id', $user->id);
        })->orWhereHas('participants', function ($query) use ($user) {
            // Memeriksa creator_project di tabel pivot
            $query->where('creator_project', $user->id);
        })
        ->get(); // Mengambil Collection dari proyek

        $projects = collect($projects);
        // Kirim data proyek ke tampilan
        return view('layouts.dashboard', compact('projects'));
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


}
