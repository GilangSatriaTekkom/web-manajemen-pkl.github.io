<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;

class CardController extends Controller
{
    public function store(Request $request)
    {
        // Ambil data dari request, dengan default untuk description jika null
        $data = $request->only(['title', 'description']);
        $data['description'] = $data['description'] ?? '';

        // Simpan data ke database
        Card::create($data);

        // Redirect kembali ke halaman sebelumnya
        return back()->with('success', 'Card added successfully.');
    }
}
