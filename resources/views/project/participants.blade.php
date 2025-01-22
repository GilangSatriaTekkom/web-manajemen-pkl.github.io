@extends('layouts.app')

@section('content')
@if (session('success'))
    <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
@endif

@if (session('info'))
    <div class="mb-6 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded-lg">
        {{ session('info') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="container mx-auto p-6 bg-white shadow-md rounded-lg">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Participants for Project: <span class="text-blue-600">{{ $project->name }}</span></h1>

    <!-- Current Participants -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Current Participants</h2>
        <ul class="divide-y divide-gray-200">
            @forelse ($participants as $participant)
                <li class="flex items-center justify-between py-4">
                    <div class="flex items-center">
                        <img src="{{ $participant->profile_pict ? asset('storage/' . $participant->profile_pict) : asset('images/default-profile.png') }}" 
                             alt="{{ $participant->name }}" 
                             class="w-10 h-10 rounded-full mr-4">
                        <span class="text-gray-800">{{ $participant->name }}</span>
                    </div>
                    <form action="{{ route('project.removeParticipant', ['id' => $project->id, 'userId' => $participant->id]) }}" 
                        method="POST" 
                        onsubmit="return confirm('Are you sure you want to remove this participant?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="text-red-500 hover:text-red-700 font-medium">Remove</button>
                  </form>
                  
                </li>
            @empty
                <li class="py-4 text-gray-500">No participants yet.</li>
            @endforelse
        </ul>
    </div>

    <!-- Add Participant -->
    <div>
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Add Participant</h2>
        <form action="{{ route('project.addParticipant', ['id' => $project->id]) }}" method="POST" class="flex items-center space-x-4">
            @csrf
            <select name="user_id" required class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">-- Select User --</option>
                @foreach ($availableUsers as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md shadow hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Add</button>
        </form>
    </div>

    <!-- Back Link -->
    <div class="mt-6">
        <a href="/dashboard" class="text-blue-600 hover:underline font-medium">&larr; Back to Dashboard</a>
    </div>
</div>
@endsection
