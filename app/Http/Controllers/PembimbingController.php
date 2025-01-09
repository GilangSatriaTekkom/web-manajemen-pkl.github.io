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
}
