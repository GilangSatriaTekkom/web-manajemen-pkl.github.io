<!-- resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-4">
    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>

    <div class="grid grid-cols-3 gap-4">
        @if ($projects instanceof \Illuminate\Support\Collection && $projects->isEmpty())
            <p class="text-center text-gray-600 col-span-3">Tidak ada project hari ini</p>
        @else
            @foreach ($projects as $project)
                <div class="bg-white p-4 rounded shadow">
                    <h2 class="text-xl font-semibold">
                        {{ is_array($project) ? $project['name'] : $project->name }}
                    </h2>
                    <p class="text-sm text-gray-600">{{ is_array($project) ? $project['description'] : $project->description }}</p>
                    <p class="text-sm text-gray-800">
                        Created by:
                        <span class="font-semibold">
                            {{
                                is_array($project) && isset($project['creator'])
                                ? $project['creator']['name']
                                : (is_object($project) && $project->creator ? $project->creator->name : 'Unknown')
                            }}
                        </span>
                    </p>
                    <a href="{{ route('project.show', $project['id'] ?? $project->id) }}" class="text-blue-600 hover:underline">View Project</a>
                </div>
            @endforeach
        @endif
    </div>


</div>
@endsection
