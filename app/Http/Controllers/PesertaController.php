<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class PesertaController extends Controller
{

    public function get(Request $request)
    {
        $query = User::where('roles', 'peserta');

        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('asal_sekolah', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->get();
        return view('layouts.peserta', compact('users'));
    }
}
