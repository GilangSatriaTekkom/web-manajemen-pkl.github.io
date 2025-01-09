<!-- resources/views/project.blade.php -->
@extends('layouts.app')

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@section('content')
    <div class="container mx-auto mt-4">
        <div class="flex flex-row w-full justify-between">
            <h1 class="text-2xl font-bold mb-4">{{ $projectName }}</h1>
            <a href="javascript:void(0)" onclick="openModalParticipant('{{ $projectId }}')" class="flex items-center space-x-[-10px]">
                @foreach ($project['participants'] as $index => $participant)

                    @if ($index < 5)
                        <!-- Gambar Profil -->
                        <div class="w-[32px] h-[32px] rounded-full border-2 border-white overflow-hidden">
                            <img src="{{ $participant['profile_image'] ? asset('storage/' . $participant->profile_pict) : asset('images/default-profile.png') }}" alt="Profile {{ $index + 1 }}" class="w-full h-full object-cover" />
                        </div>
                    @elseif ($index === 5)
                        <!-- Placeholder untuk peserta tambahan -->
                        <div class="w-[32px] h-[32px] rounded-full border-2 border-white bg-gray-200 flex items-center justify-center text-sm font-semibold">
                            +{{ count($project['participants']) - 5 }}
                        </div>
                    @endif
                @endforeach
            </a>
        </div>

        <div class="grid grid-cols-3 gap-4">
            @foreach ($columns as $column)
                <div id="board-column-{{ $column['id'] }}" class="py-4 rounded shadow-md">
                    <h3 class="font-semibold text-md mb-3 flex flex-row items-center">
                        <span class="w-2 h-2 rounded-full mr-2 block
                            @if ($loop->index % 3 == 0) bg-gray-500
                            @elseif ($loop->index % 3 == 1) bg-yellow-500
                            @elseif ($loop->index % 3 == 2) bg-green-500
                            @endif"></span>
                            {{ $column['title'] }}</h3>

                    <!-- Cek apakah ada cards -->
                    @if ($column['cards']->isNotEmpty())
                        @foreach ($column['cards'] as $task)
                            <div id="task-{{ $task->id }}" class="bg-white p-3 rounded shadow mb-3 cursor-pointer"
                                onclick="openTaskPopup('{{ $task->id }}')">
                                <h4 class="font-semibold text-lg">{{ $task->title }}</h4>
                                <hr class="my-2">
                                <div class="text-sm gap-2">
                                    {{-- <span class="text-[#6b6f99]">Deskripsi :</span> --}}
                                    @php
                                        $description = $task->description;
                                        $data = json_decode($description->text, true);
                                        // dd($data);
                                    @endphp
                                   <p class="text-[#6b6f99] flex flex-row truncate">
                                        @if (!empty($data['ops']))
                                            @foreach ($data['ops'] as $item)
                                                {{-- Jika ada teks biasa --}}
                                                @if (isset($item['insert']) && is_string($item['insert']))
                                                    {{-- Menghilangkan <br> dan membatasi teks --}}
                                                    @php
                                                        $text = str_replace('<br>', '', $item['insert']);
                                                    @endphp
                                                    <span class="line-clamp-3">{{ $text }}</span>
                                                @endif
                                            @endforeach
                                        @endif
                                    </p>
                                    <a class="flex flex-row gap-2 mt-4">
                                        @if (!empty($data['ops']))
                                            @foreach ($data['ops'] as $item)
                                                {{-- Jika ada gambar --}}
                                                @if (isset($item['insert']['image']))
                                                    <img class="w-[30px] h-[30px] rounded-sm" src="{{ $item['insert']['image'] }}" alt="Image">
                                                @endif
                                            @endforeach
                                        @endif
                                    </a>

                                </div>
                                <hr class="my-2">
                                <div class="flex flex-row gap-2 justify-between">
                                    <div class="flex flex-row gap-2">
                                        @if($task['comment_count'] > 0)
                                            <div class="comment_indicator flex flex-row gap-2 items-center">
                                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1.2915 3.725C1.2915 2.81491 1.2915 2.35987 1.46862 2.01227C1.62441 1.7065 1.87301 1.45791 2.17877 1.30211C2.52638 1.125 2.98142 1.125 3.8915 1.125H8.4415C9.35159 1.125 9.80663 1.125 10.1542 1.30211C10.46 1.45791 10.7086 1.7065 10.8644 2.01227C11.0415 2.35987 11.0415 2.81491 11.0415 3.725V6.65C11.0415 7.56009 11.0415 8.01513 10.8644 8.36274C10.7086 8.6685 10.46 8.91709 10.1542 9.07289C9.80663 9.25 9.35159 9.25 8.4415 9.25H7.07853C6.74051 9.25 6.57149 9.25 6.40983 9.28318C6.2664 9.31261 6.12761 9.3613 5.99723 9.42791C5.85026 9.50299 5.71828 9.60858 5.45433 9.81974L4.16221 10.8534C3.93683 11.0337 3.82414 11.1239 3.7293 11.124C3.64682 11.1241 3.56879 11.0866 3.51734 11.0221C3.45817 10.948 3.45817 10.8037 3.45817 10.5151V9.25C2.95444 9.25 2.70257 9.25 2.49592 9.19463C1.93515 9.04437 1.49713 8.60636 1.34687 8.04558C1.2915 7.83894 1.2915 7.58707 1.2915 7.08333V3.725Z" stroke="#141522" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                <span>{{ $task['comment_count'] }}</span>
                                            </div>
                                        @endif

                                        @if($task['file_count'] > 0)
                                            <div class="file_indicator flex flex-row gap-2 items-center">
                                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M11.1243 5.40409L6.24082 10.2876C5.13026 11.3981 3.3297 11.3981 2.21915 10.2876C1.10859 9.177 1.10859 7.37643 2.21915 6.26588L7.1026 1.38243C7.84297 0.642056 9.04335 0.642056 9.78372 1.38243C10.5241 2.12279 10.5241 3.32317 9.78372 4.06354L5.09177 8.75549C4.72158 9.12567 4.12139 9.12567 3.75121 8.75549C3.38103 8.3853 3.38103 7.78511 3.75121 7.41493L7.86863 3.29751" stroke="#141522" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                <span>{{ $task['file_count'] }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    @if ($task->board_id !== 1)
                                    <div class="worked-by__on-card flex flex-row gap-2">
                                        <span class="text-sm">Dikerjakan Oleh :</span>
                                        @if ($task->workedBy && $task->workedBy->profile_pict)
                                        <img class="w-[24px] h-[24px] rounded-full"
                                            src="{{ Storage::url($task->workedBy->profile_pict) }}"
                                            alt="Profile Picture">
                                        @else
                                            <!-- Tidak ada gambar profil -->
                                        @endif
                                    </div>
                                    @endif
                                </div>

                            </div>
                        @endforeach

                        <!-- Popup Task -->
                        <div id="taskPopup"
                            class=" fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden ">
                            <div class="bg-white rounded shadow-lg flex flex-col w-[65%] card-layout-scoll">
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

                                        <div id="attachments" class="attachment">

                                        </div>
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

                                <div class="relative mt-10 pl-4 pb-4 space-x-2 flex ">
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
                        <button id="addTaskButton" class="w-full bg-white font-bold text-black text-sm py-2 {{ $column['id'] == 1 ? '' : 'hidden' }}"
                            onclick="newCard()">+ Tambah Tugas
                        </button>
                    @endif

                    <!-- Modal Form -->
                    <div id="modal" class=" add-card hidden fixed inset-0 bg-gray-800 bg-opacity-50 items-center justify-center">
                        <div class=" bg-white rounded-lg p-6 w-1/3 card-layout-scoll">
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


        @include ('layouts.partials.participantModal')


        {{-- Modal Image Description Popup --}}
        <div id="imageModal" class="z-[101]" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.7); justify-content: center; align-items: center;">
            <div style="position: relative; padding: 20px;">
                <img id="modalImage" src="" alt="Full Image" class="max-h-[90vh]" />
                <button onclick="closeModalImageDescription()" style="position: absolute; top: 10px; right: 10px; background: red; color: white; border: none; padding: 10px; cursor: pointer;">X</button>
            </div>
        </div>

        <div id="editModal-{{ $project->id }}" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
            <div class="bg-white rounded-lg shadow-lg w-1/3 p-6">
                <h2 class="text-lg font-semibold mb-4">Tambah Peserta</h2>
                <form method="POST" action="{{ route('storeParticipants', ['project' => $project->id]) }}">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <div class="mb-4">
                        <label for="user_name" class="block text-sm font-medium text-gray-700">Nama User</label>
                        <input
                            type="text"
                            id="user_name"
                            name="user_name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required
                        >
                    </div>
                    <div class="flex justify-end">
                        <button
                            type="button"
                            onclick="closeEditModal('{{ $project->id }}')"
                            class="mr-2 px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                        >
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection


<script>

     function closeModal() {
        document.getElementById('participantModal').classList.add('hidden');
    }

    document.addEventListener("DOMContentLoaded", function () {
    const currentUrl = window.location.href;
    console.log("Script CHecking is running");


    // Cek apakah URL mengandung '/tasks/' dan '/data'
    if (currentUrl.includes("/tasks/") && currentUrl.includes("/data")) {
    // Ambil ID project dari URL
    const projectIdMatch = currentUrl.match(/\/project\/(\d+)\//);
    if (projectIdMatch) {
        const projectId = projectIdMatch[1];
        // Redirect ke URL awal
        window.location.href = `/project/${projectId}/`;
    }
    }
    });


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



    window.openModalParticipant = function (projectId) {
        // Kosongkan daftar peserta sebelumnya
        const participantList = document.getElementById('participantList'); // Ambil elemen ul
        participantList.innerHTML = ''; // Kosongkan isi daftar

        // Panggil API untuk mendapatkan data peserta
        axios.get('/dashboard/' + projectId + '/participants')
            .then((response) => {
                const participants = response.data.participants; // Ambil data peserta dari respons

                // Tambahkan peserta ke dalam daftar
                participants.forEach(participant => {
                    const listItem = document.createElement('li');
                    listItem.classList.add('flex', 'flex-row', 'gap-3', 'items-center');

                    listItem.innerHTML = `
                        <img class="size-9" src="${participant.profile_picture ? `/storage/${participant.profile_picture}` : '/images/default-profile.png'}" alt="${participant.name}">
                        <a href="/profile/${participant.id}" class="block py-2 text-blue-600 hover:underline">${participant.name}</a>
                        ${participant.canRemove ? `
                            <button class="text-red-600 hover:text-red-800" onclick="removeParticipant(${participant.id}, ${projectId})">
                                &times;
                            </button>
                        ` : ''}
                    `;
                    participantList.appendChild(listItem);
                });

                // Tampilkan tombol tambah peserta jika pengguna adalah admin atau pembimbing
                if (response.data.canAddParticipant) {
                    addParticipantButton.classList.remove('hidden');
                    addParticipantButton.querySelector('button').setAttribute('onclick', `window.location.href='/dashboard/${projectId}/addParticipant'`);
                }

                // Tampilkan modal
                document.getElementById('participantModal').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error fetching participants:', error);
            });
    };

     // Fungsi untuk membuka modal dan menampilkan gambar
     function openModalImageDescription(imageUrl) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');

        // Set gambar yang akan ditampilkan di modal
        modalImage.src = imageUrl;

        // Tampilkan modal
        modal.style.display = 'flex';
    }

    // Fungsi untuk menutup modal
    function closeModalImageDescription() {
        const modal = document.getElementById('imageModal');

        // Sembunyikan modal
        modal.style.display = 'none';
    }
</script>


