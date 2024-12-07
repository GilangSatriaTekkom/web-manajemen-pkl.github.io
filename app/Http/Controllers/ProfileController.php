<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        $users = User::all(); // Fetch all users from the database
        // Ambil data pengguna yang sedang login
        $user = Auth::user(); // Mendapatkan data pengguna yang sedang login
        return view('layouts.profile', compact('user'));
    }
}
