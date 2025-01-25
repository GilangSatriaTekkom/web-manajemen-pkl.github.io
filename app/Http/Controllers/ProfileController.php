<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;
use App\Models\User;

use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function index($id)
    {
        // Ambil data pengguna yang sedang login
        $user = User::find($id); // Mendapatkan data pengguna yang sedang login
         // Cek apakah kolom profile_pict kosong
        $profilePicture = $user->profile_pict
        ? asset('storage/' . $user->profile_pict) // Jika ada, gunakan gambar dari database
        : asset('images/default-profile.png');   // Jika kosong, gunakan gambar default

        // Kirim data user dan profile picture ke view
        return view('layouts.profile', compact('user', 'profilePicture'));
    }

    public function profileUpload(Request $request, $id) {
        Log::info('Request data: ', ['data' => $request->all()]);
        Log::info('User ID: ', ['user_id' => $id]);

        // Cek apakah ID yang diberikan adalah ID pengguna yang sedang login
        if (Auth::id() == $id) {
            // Jika ID yang diberikan adalah ID pengguna yang sedang login, ambil user yang sedang login
            $user = Auth::user();
        } else {
            // Jika ID yang diberikan bukan ID pengguna yang sedang login, cari pengguna dengan ID tersebut
            $user = User::find($id);

            // Jika pengguna tidak ditemukan, kirim error
            if (!$user) {
                return redirect()->back()->withErrors(['error' => 'Pengguna tidak ditemukan.']);
            }
        }

        // Validasi data
        // Cek perubahan pada gambar
        if ($request->hasFile('profile_pict')) {
            $file = $request->file('profile_pict');
            if ($file) {
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = uniqid() . '.' . $fileExtension;
                $filePath = $file->storeAs('uploads/profile_pictures', $fileName, 'public');

                // Hapus gambar lama jika ada perubahan
                if ($user->profile_pict && $user->profile_pict !== $fileName) {
                    Storage::disk('public')->delete('uploads/profile_pictures/' . $user->profile_pict);
                }

                // Update gambar di database
                $user->profile_pict = $fileName;
            }
        }

        // Periksa perubahan dan simpan jika ada perubahan
        $updated = false; // Variabel untuk melacak apakah ada perubahan

        // Update nama jika ada perubahan
        if ($request->has('name') && $request->name !== $user->name) {
            $user->name = $request->name;
            $updated = true;
        }

        // Update email jika ada perubahan
        if ($request->has('email') && $request->email !== $user->email) {
            // Pastikan email yang baru tidak digunakan oleh pengguna lain
            $existingEmail = User::where('email', $request->email)->first();
            if ($existingEmail) {
                return redirect()->back()->withErrors(['email' => 'Email sudah digunakan oleh pengguna lain.']);
            }

            $user->email = $request->email;
            $updated = true;
        }

        // Update password jika ada perubahan
        if ($request->has('password') && $request->password) {
            $user->password = bcrypt($request->password);
            $updated = true;
        }

        // Update roles jika ada perubahan
        if ($request->has('roles') && $request->roles !== $user->roles) {
            Log::info("Roles updated: ", ['roles' => $request->roles]);
            $user->roles = $request->roles;
            $updated = true;
        }

        // Update asal_sekolah jika ada perubahan
        if ($request->has('asal_sekolah') && $request->asal_sekolah !== $user->asal_sekolah) {
            $user->asal_sekolah = $request->asal_sekolah;
            $updated = true;
        }

        // Jika ada perubahan, simpan ke database
        if ($updated) {
            $user->save();
        }

        // Kirim respons ke frontend
        return redirect()->route('profile', ['id' => $user->id])->with('success', 'Profile updated successfully!');
    }



}
