<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;
use App\Models\User;

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

    public function profileUpload(Request $request) {

        $user = Auth::user();
         // Validasi file
         $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|nullable|min:8',
            'roles' => 'sometimes|in:admin,peserta,pembimbing',
            'asal_sekolah' => 'sometimes|string|max:255',
            'profile_pict' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048'
        ]);




            // Periksa apakah ada file gambar yang di-upload
            if ($request->hasFile('profile_pict')) {
                // Ambil file gambar
                $file = $request->file('profile_pict');

                // Periksa apakah file yang di-upload valid
                if ($file) {
                    // Ambil ekstensi file
                    $fileExtension = $file->getClientOriginalExtension();

                    // Nama file baru yang unik
                    $fileName = uniqid() . '.' . $fileExtension;

                    // Tentukan path penyimpanan file
                    $filePath = $file->storeAs('uploads/profile_pictures', $fileName, 'public');

                    // Periksa apakah gambar sebelumnya ada dan berbeda
                    if ($user->profile_pict && $user->profile_pict !== $fileName) {
                        // Hapus gambar lama jika ada
                        Storage::disk('public')->delete('uploads/profile_pictures/' . $user->profile_pict);
                    }

                    // Perbarui nama file gambar di database
                    $user->profile_pict = $fileName;
                }
            } else {
                // Jika input gambar kosong, jangan ubah nilai profile_picture
                // Pastikan kolom profile_picture tetap dengan nilai sebelumnya
                // Tidak ada perubahan pada kolom profile_picture
            }

            // Periksa setiap field dan hanya perbarui jika ada perubahan
        if ($request->has('name') && $request->name !== $user->name) {
            $user->name = $request->name;
        }

        if ($request->has('email') && $request->email !== $user->email) {
            $user->email = $request->email;
        }

        if ($request->has('password') && $request->password) {
            $user->password = bcrypt($request->password);
        }

        if ($request->has('roles') && $request->roles !== $user->roles) {
            $user->roles = $request->roles;
        }

        if ($request->has('asal_sekolah') && $request->asal_sekolah !== $user->asal_sekolah) {
            $user->asal_sekolah = $request->asal_sekolah;
        }

        if ($request->hasFile('profile_pict')) {
            // Store the file and get the file path
            $filePath = $request->file('profile_pict')->store('profile_pictures', 'public');
            $user->profile_pict = $filePath;
        } else {
            // If no file is uploaded, keep the old profile picture
            $filePath = $user->profile_pict;
        }

        $user->profile_pict = $filePath;
        $user->save();


        // Kirim respons ke frontend
       // Return a JSON response instead of using view()->response()
       return redirect()->route('profile', ['id' => $user->id])->with('success', 'Profile updated successfully!');
    }


}
