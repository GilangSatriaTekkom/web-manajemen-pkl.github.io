<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task; // Ganti dengan model yang sesuai dengan data laporan
use App\Models\Report;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::query();

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;

            // Coba parsing input sebagai tanggal
            $parsedDate = null;
            try {
                $parsedDate = \Carbon\Carbon::createFromFormat('d F Y', $search)->format('Y-m-d');
            } catch (\Exception $e) {
                // Tidak melakukan apa-apa jika parsing gagal
            }

            $query->where(function ($q) use ($search, $parsedDate) {
                $q->where('project_name', 'like', '%' . $search . '%')
                  ->orWhere('task_name', 'like', '%' . $search . '%')
                  ->orWhere('status', 'like', '%' . $search . '%')
                  ->orWhere('worked_by', 'like', '%' . $search . '%');

                // Jika parsing berhasil, tambahkan pencarian berdasarkan tanggal
                if ($parsedDate) {
                    $q->orWhereDate('created_at', $parsedDate);
                }
            });
        }

        // Filter berdasarkan rentang waktu
        if ($request->filled('time_range')) {
            $timeRanges = [
                '1_day' => now()->subDay(),
                '1_week' => now()->subWeek(),
                '1_month' => now()->subMonth(),
                '3_months' => now()->subMonths(3),
                '6_months' => now()->subMonths(6),
                '1_year' => now()->subYear(),
            ];

            if (array_key_exists($request->time_range, $timeRanges)) {
                $query->where('created_at', '>=', $timeRanges[$request->time_range]);
            }
        }

        // Pagination limit
        $limit = $request->get('limit', 10);
        $reports = $query->paginate($limit);

         // Query untuk tabel tasks (tanpa filter pencarian atau waktu)
        $taskQuery = Task::query();
        $taskLimit = $request->get('task_limit', 10);
        $tasks = $taskQuery->paginate($taskLimit);

        return view('layouts.report', compact('reports', 'tasks'));
    }
}
