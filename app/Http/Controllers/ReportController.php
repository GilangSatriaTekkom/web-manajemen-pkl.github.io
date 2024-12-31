<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task; // Ganti dengan model yang sesuai dengan data laporan
use App\Models\Report;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data dengan paginasi (default 10 per halaman)
        $reports = Report::paginate(10); // Ganti angka 10 dengan jumlah per halaman yang diinginkan

        // Preserve the query parameters in pagination links
        $reports->appends($request->all());

        //dd($reports);

        // Kirim data ke view
        return view('layouts.report', compact('reports'));
    }

    public function filter(Request $request)
    {
        // Default query
        $query = Report::query();

        // Filter pencarian (jika ada)
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter rentang waktu (default: hari ini)
        $timeRanges = [
            '1_day' => now()->startOfDay(),
            '1_week' => now()->subWeek(),
            '1_month' => now()->subMonth(),
            '3_months' => now()->subMonths(3),
            '6_months' => now()->subMonths(6),
            '1_year' => now()->subYear(),
        ];

        $timeRangeKey = $request->has('time_range') && array_key_exists($request->time_range, $timeRanges)
            ? $request->time_range
            : '1_day'; // Default: hari ini

        $query->where('created_at', '>=', $timeRanges[$timeRangeKey]);

        // Limit data per page (default: 10)
        $limit = $request->has('limit') && in_array($request->limit, [10, 50, 100])
            ? $request->limit
            : 10;

        // Dapatkan data dengan paginasi
        $reports = $query->paginate($limit);

        return view('layouts.report', compact('reports'));
    }
}
