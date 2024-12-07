<!-- resources/views/project.blade.php -->
@extends('layouts.app')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@section('content')
<div class="container mx-auto mt-4">
    <h1 class="text-2xl font-bold mb-4">{{ $projectName }}</h1>

    <div class="grid grid-cols-3 gap-4">
        @foreach ($columns as $column)
            @include('components.board-column', ['title' => $column['title'], 'cards' => $column['cards']])
        @endforeach
    </div>
</div>

<!-- Popup -->
<div id="taskPopup" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white w-2/3 h-2/3 rounded shadow-lg flex">
        <!-- Deskripsi Task -->
        <div class="w-1/2 p-4">
            <h3 id="popupTitle" class="text-xl font-semibold"></h3>
            <p id="popupDescription" class="text-gray-700 mt-4"></p>
        </div>

        <!-- Kolom Komentar -->
        <div class="w-1/2 border-l p-4 flex flex-col">
            <div class="flex-1 overflow-y-auto" id="comments">
                <!-- Komentar akan muncul di sini -->
            </div>
            <div class="mt-4">
                <input id="commentInput" type="text" class="w-full border rounded p-2" placeholder="Write a comment..." />
                <button onclick="addComment()" class="bg-blue-600 text-white px-4 py-2 mt-2 rounded">Send</button>
            </div>
        </div>
    </div>
</div>
@endsection

<script src="{{ asset('/resource/js/app.js') }} "></script>
