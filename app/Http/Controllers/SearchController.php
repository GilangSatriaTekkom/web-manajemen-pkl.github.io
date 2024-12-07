<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class SearchController extends Controller
{
    public function searchParticipants(Request $request)
    {
        $query = $request->input('query');
        $users = User::where('roles', 'peserta')
                    ->where('name', 'LIKE', "%{$query}%")
                    ->get(['id', 'name', 'asal_sekolah']);
        return response()->json($users);
    }
}
