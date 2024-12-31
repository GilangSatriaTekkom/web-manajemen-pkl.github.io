<!-- resources/views/project.blade.php -->
@extends('layouts.app')

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@section('content')
    <div class="container mx-auto mt-4">
        <h1 class="text-2xl font-bold mb-4">{{ $projectName }}</h1>

        <div class="grid grid-cols-3 gap-4">
            @foreach ($columns as $column)
                <div id="board-column-{{ $column['id'] }}" class="bg-gray-200 p-4 rounded shadow-md">
                    <h3 class="font-semibold text-lg mb-3">{{ $column['title'] }}</h3>

                    <!-- Cek apakah ada cards -->
                    @if ($column['cards']->isNotEmpty())
                        @foreach ($column['cards'] as $task)
                            <div id="task-{{ $task->id }}" class="bg-white p-3 rounded shadow mb-3 cursor-pointer"
                                onclick="openTaskPopup('{{ $task->id }}')">
                                <h4 class="font-semibold">{{ $task->title }}</h4>
                            </div>
                        @endforeach

                        <!-- Popup Task -->
                        <div id="taskPopup"
                            class=" fixed inset-0 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center hidden ">
                            <div class="bg-white rounded shadow-lg flex flex-col w-[65%] absolute top-[100px]">
                                <div class="relative ">
                                    <div class=" p-4">
                                        <div class="flex justify-between items-center">

                                            <h3 id="popupTitle" class="text-xl font-semibold"></h3>
                                            <button id="exitButton"
                                                class="justify-end text-2xl text-gray-700 hover:text-gray-900 focus:outline-none">
                                                &times;
                                            </button>
                                        </div>

                                        <div class="text-sm text-gray-600" id="assignedTo">
                                            <p><strong>Assigned to:</strong></p>
                                        </div>
                                        <h4 class="mt-4">Description</h4>
                                        <div id="taskDescription"></div>
                                    </div>

                                    <!-- Kolom Komentar -->

                                    <div class=" border-l p-4 flex flex-col">
                                        <h4>Comment</h4>
                                        <div class="flex-1" id="comments">
                                            <!-- Komentar akan diisi melalui JS -->
                                        </div>
                                        <div class="mt-4">
                                            <input id="commentInput" type="text" class="w-full border rounded p-2"
                                                placeholder="Write a comment..." />
                                            <button onclick="addComment()"
                                                class="bg-blue-600 text-white px-4 py-2 mt-2 rounded">Send</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="absolute bottom-4 right-4 space-x-2 flex">
                                    <button id="startTaskButton" onclick="updateTaskStatus('in_progress', {{ $task->board_id }})"
                                        data-task-id="{{ $task->id }}"
                                        class="bg-green-600 text-white px-4 py-2 rounded hidden">
                                        Mulai Pekerjaan
                                    </button>
                                    {{-- <button id="stopTaskButton" onclick="updateTaskStatus('paused')"
                                        class="bg-red-600 text-white px-4 py-2 rounded hidden">
                                        Hentikan Pekerjaan
                                    </button> --}}
                                    <button id="resumeTaskButton" onclick="updateTaskStatus('in_progress')"
                                        class="bg-yellow-600 text-white px-4 py-2 rounded hidden">
                                        Lanjutkan Pekerjaan
                                    </button>
                                    <button id="finishTaskButton" onclick="updateTaskStatus('done', {{ $task->board_id }})"
                                        data-task-id="{{ $task->id }}"
                                        class="bg-blue-600 text-white px-4 py-2 rounded hidden">
                                        Selesai
                                    </button>
                                </div>
                            </div>
                            <!-- Tombol Aksi -->

                        </div>
                    @else
                        <p class="text-gray-500">Tasks tidak terdeteksi</p>
                    @endif

                    <!-- Tombol untuk menambahkan kartu -->
                    @if ($roles)
                        <button id="addTaskButton" class="text-blue-600 text-sm mt-2 {{ $column['id'] == 1 ? '' : 'hidden' }}"
                            onclick="newCard()">+ Tambah Tugas</button>
                    @endif

                    <!-- Modal Form -->
                    <div id="modal" class=" hidden fixed inset-0 bg-gray-800 bg-opacity-50 items-center justify-center">
                        <div class="bg-white rounded-lg p-6 w-1/3">
                            <h2 class="text-xl font-semibold mb-4">Add Card</h2>
                            <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="project_id" value="{{ $project->id }}">

                                <div class="mb-4">
                                    <label for="title" class="block text-sm font-medium text-gray-700">Card Title</label>
                                    <input type="text" id="title" name="title" required
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>

                                <div class="mb-4 border">
                                    <label for="description" class="block text-sm font-medium text-gray-700">Card Description</label>
                                    <div id="description-container" style="height: 200px;"
                                        class="border border-gray-300 rounded-md"></div>
                                    <input type="hidden" id="description" name="description">
                                </div>

                                <div class="mb-4">
                                    <label for="links" class="block text-sm font-medium text-gray-700">Links</label>
                                    <div id="links-container">
                                        <div class="flex items-center mb-2">
                                            <input type="url" name="links[]" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Enter link">
                                            <button type="button" onclick="addLink()" class="ml-2 text-blue-600">Add Link</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="image" class="block text-sm font-medium text-gray-700">Card Image</label>
                                    <input type="file" id="image" name="image" accept="image/*"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
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


<script>
    function addLink() {
        const linksContainer = document.getElementById('links-container');
        const newLinkDiv = document.createElement('div');
        newLinkDiv.classList.add('flex', 'items-center', 'mb-2');
        newLinkDiv.innerHTML = `
            <input type="url" name="links[]" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Enter link">
            <button type="button" onclick="removeLink(this)" class="ml-2 text-red-600">Remove</button>
        `;
        linksContainer.appendChild(newLinkDiv);
    }

    function removeLink(button) {
        button.parentElement.remove();
    }
</script>


