@extends('layouts.app')

@section('title', 'Report')

@section('content')
<div class="container">
    <h1 class="text-center my-4">Report Page</h1>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('report.filter') }}" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by task name" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="time_range" class="form-control">
                    <option value="">Select Time Range</option>
                    <option value="1_day" {{ request('time_range') == '1_day' ? 'selected' : '' }}>Last 1 Day</option>
                    <option value="1_week" {{ request('time_range') == '1_week' ? 'selected' : '' }}>Last 1 Week</option>
                    <option value="1_month" {{ request('time_range') == '1_month' ? 'selected' : '' }}>Last 1 Month</option>
                    <option value="3_months" {{ request('time_range') == '3_months' ? 'selected' : '' }}>Last 3 Months</option>
                    <option value="6_months" {{ request('time_range') == '6_months' ? 'selected' : '' }}>Last 6 Months</option>
                    <option value="1_year" {{ request('time_range') == '1_year' ? 'selected' : '' }}>Last 1 Year</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="limit" class="form-control">
                    <option value="10" {{ request('limit') == 10 ? 'selected' : '' }}>10 per page</option>
                    <option value="50" {{ request('limit') == 50 ? 'selected' : '' }}>50 per page</option>
                    <option value="100" {{ request('limit') == 100 ? 'selected' : '' }}>100 per page</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('report.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <!-- Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Project</th>
                <th>Task</th>
                <th>Status</th>
                <th>Time</th>
                <th>Worked By</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reports as $report)
                <tr>
                    <td>{{ $loop->iteration }}</td>


                    <td>{{$report->project_name}}</td>
                    <td>{{ $report->task_name}}</td>
                    <td>{{ $report->status}}</td>
                    <td>{{ \Carbon\Carbon::parse($report->time ?? now())->translatedFormat('d F Y') }}</td>
                    <td>{{ $report->worked_by}}</td>

                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No data available</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    {{ $reports->links() }}

</div>
@endsection
