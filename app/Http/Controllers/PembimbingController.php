<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class PembimbingController extends Controller
{
    public function get()
    {
        $users = User::where('roles', 'pembimbing')
                    ->orWhere('roles', 'admin')
                    ->get();
        return view('layouts.pembimbing', compact('users'));


    }

    public function liveSearch(Request $request)
    {
    $search = $request->input('search');
    $users = User::where(function ($query) use ($search) {
        $query->where('roles', 'pembimbing')
              ->orWhere('roles', 'admin');
    })
    ->where(function ($query) use ($search) {
        $query->where('name', 'like', '%' . $search . '%')
              ->orWhere('email', 'like', '%' . $search . '%');
    })
    ->get();

    return view('layouts.pembimbing', ['users' => $users]);
    }
}
