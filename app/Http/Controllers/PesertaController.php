<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class PesertaController extends Controller
{

    public function get()
    {
        $users = User::where('roles', 'peserta')->get();
        return view('layouts.peserta', compact('users'));
    }
}
