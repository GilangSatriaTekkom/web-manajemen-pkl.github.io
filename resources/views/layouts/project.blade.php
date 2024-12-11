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
        <div class="bg-gray-200 p-4 rounded shadow-md">
            <h3 class="font-semibold text-lg mb-3">{{ $column['title'] }}</h3>


            <!-- Cek apakah ada cards -->
            @if ($column['cards']->isNotEmpty())
                @foreach ($column['cards'] as $task)
                <div class="bg-white p-3 rounded shadow mb-3 cursor-pointer" onclick="openTaskPopup('{{ $task->id }}')">
                    <h4 class="font-semibold">{{ $task->title }}</h4>
                </div>
                @endforeach

                <!-- Popup Task -->
                <div id="taskPopup" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
                    <div class="bg-white w-2/3 h-2/3 rounded shadow-lg flex relative">
                        <!-- Tombol Exit di pojok kiri atas -->

                        <!-- Deskripsi Task -->
                        <div class="w-1/2 p-4">
                            <button id="exitButton" class="absolute top-2 left-2 text-2xl text-gray-700 hover:text-gray-900 focus:outline-none">
                                &times;
                            </button>
                            <h3 id="popupTitle" class="text-xl font-semibold"></h3>
                            <div id="taskDescription"></div>
                        </div>

                        <!-- Kolom Komentar -->
                        <div class="w-1/2 border-l p-4 flex flex-col">
                            <div class="flex-1 overflow-y-auto" id="comments">
                                <!-- Komentar akan diisi melalui JS -->
                            </div>
                            <div class="mt-4">
                                <input id="commentInput" type="text" class="w-full border rounded p-2" placeholder="Write a comment..." />
                                <button onclick="addComment()" class="bg-blue-600 text-white px-4 py-2 mt-2 rounded">Send</button>
                            </div>
                        </div>
                    </div>
                    <!-- Tombol Aksi -->
                    <div class="absolute bottom-4 right-4 space-x-2 flex">
                        <button id="startTaskButton" onclick="updateTaskStatus('in_progress')" class="bg-green-600 text-white px-4 py-2 rounded hidden">
                            Mulai Pekerjaan
                        </button>
                        <button id="stopTaskButton" onclick="updateTaskStatus('paused')" class="bg-red-600 text-white px-4 py-2 rounded hidden">
                            Hentikan Pekerjaan
                        </button>
                        <button id="resumeTaskButton" onclick="updateTaskStatus('in_progress')" class="bg-yellow-600 text-white px-4 py-2 rounded hidden">
                            Lanjutkan Pekerjaan
                        </button>
                        <button id="finishTaskButton" onclick="updateTaskStatus('done')" class="bg-blue-600 text-white px-4 py-2 rounded hidden">
                            Selesai
                        </button>
                    </div>
                </div>

            @else
                <p class="text-gray-500">Tasks tidak terdeteksi</p>
            @endif

            <!-- Tombol untuk menambahkan kartu -->
            <button class="text-blue-600 text-sm mt-2" onclick="newCard()">+ Add another card</button>

            <!-- Modal Form -->
            <div id="modal" class=" hidden fixed inset-0 bg-gray-800 bg-opacity-50 items-center justify-center">
                <div class="bg-white rounded-lg p-6 w-1/3">
                    <h2 class="text-xl font-semibold mb-4">Add Card</h2>
                    <form action="{{ route('tasks.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">Card Title</label>
                            <input type="text" id="title" name="title" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div class="mb-4 border">
                            <label for="description" class="block text-sm font-medium text-gray-700">Card Description</label>
                            <div id="description-container" style="height: 200px;" class="border border-gray-300 rounded-md"></div>
                            <input type="hidden" id="description" name="description">
                        </div>
                        <div class="flex justify-end">
                            <button type="button" onclick="newCard()" class="text-gray-600 hover:text-gray-900 mr-3">Cancel</button>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Card</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
