<!-- resources/views/project.blade.php -->
@extends('layouts.app')

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@section('content')

    <div class="">
        <div class="flex flex-row w-full justify-between container mx-auto mt-4">
            <h1 class="text-2xl font-bold mb-4">{{ $projectName }}</h1>
            <a href="{{ route('project.participants', $project->id) }}" class="flex items-center -space-x-2">
                @foreach ($project['participants'] as $index => $participant)
                    @if ($index < 2)
                        <div class="w-8 h-8 rounded-full border-2 border-white overflow-hidden">
                            <img
                                src="{{ $participant['profile_image'] ? asset('storage/' . $participant['profile_image']) : asset('images/default-profile.png') }}"
                                alt="Profile {{ $index + 1 }}"
                                class="w-full h-full object-cover">
                        </div>
                    @elseif ($index === 2)
                        <div class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center text-sm font-semibold">
                            +{{ count($project['participants']) - 2 }}
                        </div>
                        @break
                    @endif
                @endforeach
            </a>
        </div>
    </div>


    <div class="container mx-auto mt-4">
        <div class="grid grid-cols-3 gap-4">
            @foreach ($columns as $column)

                <div id="board-column-{{ $column['id'] }}" class="">
                    <h3 class="font-semibold text-md mb-3 flex flex-row items-center">
                        <span class="w-2 h-2 rounded-full mr-2 block
                            @if ($loop->index % 3 == 0) bg-gray-500
                            @elseif ($loop->index % 3 == 1) bg-yellow-500
                            @elseif ($loop->index % 3 == 2) bg-green-500
                            @endif"></span>
                            {{ $column['title'] }}
                    </h3>

                    <!-- Tombol untuk menambahkan kartu -->
                    @if ($roles)
                    <div class="bg-white p-2 rounded shadow mb-3 cursor-pointer {{ $column['id'] == 1 ? '' : 'hidden' }}">

                            <button id="addTaskButton" class="w-full bg-white font-bold text-black text-sm py-2"
                                onclick="newCard()">+ Tambah Tugas
                            </button>

                    </div>
                    @endif



                    <!-- Cek apakah ada cards -->
                    @if ($column['cards']->isNotEmpty())
                    @foreach ($column['cards'] as $task)
                    <div id="task-{{ $task->id }}" class="bg-white p-2 rounded shadow mb-3 cursor-pointer"
                        onclick="openTaskPopup('{{ $task->id }}')">



                        <h4 class="font-semibold text-lg">{{ $task->title }}</h4>

                        <hr class="my-2">
                        <div class="text-sm gap-2">
                            Deskripsi
                            @php
                            $description = $task->description;

                            // Cek apakah $description->text tidak null dan formatnya valid
                            $data = null;
                            if ($description && !empty($description->text)) {
                                $data = json_decode($description->text, true);

                                // Cek apakah json_decode berhasil
                                if (json_last_error() !== JSON_ERROR_NONE) {
                                    $data = null;  // Jika terjadi kesalahan pada JSON, set $data ke null
                                }
                            }

                            // Pastikan $data ada sebelum melanjutkan
                            if ($data && isset($data['ops'])) {
                                // Filter hanya `insert` yang berupa teks dan tidak memiliki atribut
                                $filteredData = array_filter($data['ops'], function($op) {
                                    // Memastikan bahwa `insert` adalah string dan tidak memiliki `attributes`
                                    return isset($op['insert'])
                                        && is_string($op['insert'])
                                        && (!isset($op['attributes']) || empty($op['attributes']))
                                        && strpos($op['insert'], 'http') === false;
                                });

                                // Membuat array baru dengan hanya teks tanpa atribut
                                $filteredDelta = ['ops' => array_values($filteredData)];

                                // Jika ada ops yang memiliki atribut (misalnya link), tetap tampilkan itu
                                $linkData = array_filter($data['ops'], function($op) {
                                    // Memastikan bahwa ada atribut 'link' dan 'insert' berupa URL
                                    return isset($op['insert'])
                                        && isset($op['attributes']['link'])
                                        && is_string($op['insert'])
                                        && strpos($op['insert'], 'http') !== false;
                                });

                                // Gabungkan teks biasa dan link
                                $filteredDelta['ops'] = array_merge($filteredDelta['ops'], array_values($linkData));
                            } else {
                                $filteredDelta = ['ops' => []]; // Jika data tidak valid, set filteredDelta kosong
                            }
                        @endphp

                        <p class="text-[#6b6f99] flex flex-row truncate mt-2">
                            @if (!empty($filteredDelta['ops']))
                                @foreach ($filteredDelta['ops'] as $item)
                                    {{-- Hanya tampilkan teks biasa tanpa atribut --}}
                                    @if (isset($item['insert']) && is_string($item['insert']) && !isset($item['attributes']))
                                        @php
                                            $text = str_replace('<br>', '', $item['insert']);
                                        @endphp
                                        <span class="line-clamp-3">{{ $text }}</span>
                                    @endif
                                @endforeach
                            @endif
                        </p>

                            <div class="flex flex-row gap-2 mt-4">
                                @if (!empty($data['ops']))
                                    @foreach ($data['ops'] as $item)
                                        {{-- Jika ada gambar --}}
                                        @if (isset($item['insert']['image']))
                                            <img class="w-[30px] h-[30px] rounded-sm" src="{{ $item['insert']['image'] }}" alt="Image">
                                        @endif
                                        {{-- Jika ada link --}}
                                        @if (isset($item['insert']['link']))
                                            <a href="{{ $item['insert']['link'] }}" class="text-blue-500">{{ $item['insert']['link'] }}</a>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="flex flex-row gap-2 justify-between">
                            <!-- Kolom Komentar dan File -->
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
                    </div>
                    @endforeach

                        <!-- Popup Task -->
                        <div id="taskPopup"
                            class=" fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden ">
                            <div class="bg-white rounded shadow-lg flex flex-col w-[50%] card-layout-scoll">
                                <div class="relative ">
                                    <div class=" p-4">
                                        <div class="flex justify-between items-center">
                                            <div class="flex flex-row align-middle">

                                                <h3 id="popupTitle" class="text-xl font-semibold"></h3>
                                                <button class="ml-4 button text-[12px]" onclick="editTask()">Edit Task</button>
                                                <button class="ml-1 button-alert text-[12px]" onclick="deleteTask()">Delete Task</button>
                                            </div>
                                            <button id="exitButton"
                                                class="justify-end text-2xl text-gray-700 hover:text-gray-900 focus:outline-none">
                                                &times;
                                            </button>
                                        </div>

                                        @if (isset($columns[1]['cards']) && $columns[1]['cards']->isNotEmpty() && $columns[1]['cards'][0]->board_id !== 1)
                                            <div class="text-sm text-gray-600 mt-4" id="assignedTo">

                                            </div>
                                        @endif

                                        <h4 class="mt-8 italic">Description</h4>

                                        <div id="taskDescription" class="description-border" style="padding: 9px 12px ;border-radius: 0.5rem; --tw-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
                                        --tw-shadow-colored: 0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -2px var(--tw-shadow-color);
                                        box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);">
                                        </div>




                <div id="attachments">
                    <div class="flex mt-12 flex-row justify-between">
                        <p class="italic">Attachment</p>
                    </div>
                    @if (isset($task) && isset($task->tasksDescription) && isset($task->tasksDescription->ops))
                        @php
                            $ops = $task->tasksDescription->ops;
                            $imageWrapper = null;
                        @endphp

                        @foreach ($ops as $item)
                            @if (isset($item['insert']) && isset($item['insert']['image']))
                                {{-- Jika gambar ada --}}
                                @if (!$imageWrapper)
                                    @php
                                        $imageWrapper = true;
                                    @endphp
                                    <div class="attachment-card p-4 mt-2 border rounded-lg shadow-md flex flex-row gap-2">
                                        <h5 class="font-semibold text-gray-800 mb-2">Image:</h5>
                                    </div>
                                @endif
                                <div class="gap-2">
                                    <a href="{{ $item['insert']['image'] }}" target="_blank" onclick="openModalImageDescription('{{ $item['insert']['image'] }}')">
                                        <img src="{{ $item['insert']['image'] }}" alt="Image" class="w-12 h-12 rounded-sm">
                                    </a>
                                </div>
                            @endif

                            @if (isset($item['attributes']) && isset($item['attributes']['link']))
                                {{-- Jika ada link --}}
                                <div class="attachment-card p-4 mt-2 border rounded-lg shadow-md">
                                    <h5 class="font-semibold text-gray-800">Link:</h5>
                                    <a href="{{ $item['attributes']['link'] }}" class="text-blue-500 hover:text-blue-700" target="_blank">
                                        {{ $item['attributes']['link'] }}
                                    </a>
                                </div>
                            @endif
                        @endforeach

                    @endif
                </div>


                                    </div>



                                    <!-- Kolom Komentar -->

                                    <div class=" border-l p-4 flex flex-col mt-8" >
                                        <h4 class="italic">Comment</h4>

                                        <div class="mt-4">
                                            <input id="commentInput" type="text" class="w-full border rounded p-2"
                                                placeholder="Write a comment..." />
                                            <button id="sendButton" onclick="addComment()"
                                                class="bg-blue-600 text-white px-4 py-2 mt-2 rounded hidden">Send</button>
                                        </div>
                                        <div class="flex-1 px-[12px] pb-4" id="comments" style="border-radius: 0.5rem; --tw-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
                                        --tw-shadow-colored: 0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -2px var(--tw-shadow-color);
                                        box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);">
                                            <!-- Komentar akan diisi melalui JS -->
                                        </div>

                                    </div>
                                </div>

                                <div class="relative mt-10 pl-4 pr-4 pb-4 space-x-2 flex justify-end">
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

                    <!-- Modal Form -->
                    <div id="modal" class=" add-card hidden fixed inset-0 bg-gray-800 bg-opacity-50 items-center justify-center">
                        <div class=" bg-white rounded-lg p-6 w-1/3 card-layout-scoll">
                            <h2 class="text-xl font-semibold mb-4">Add Card</h2>
                            <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data" id ="taskForm">
                                @csrf
                                <input type="hidden" name="project_id" value="{{ $project->id }}">

                                <div class="mb-4">
                                    <label for="title" class="block text-sm font-medium text-gray-700">Card Title</label>
                                    <input type="text" id="title" name="title" required
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>

                                <div class="mb-4">
                                    <label for="description" class="block text-sm font-medium text-gray-700">Card Description</label>
                                    <div class="border">
                                        <div id="description-container" style="height: 200px;"
                                            class="border border-gray-300 rounded-md"></div>
                                        <input type="hidden" id="description" name="description">

                                    </div>
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
                                    <label for="Images" class="block text-sm font-medium text-gray-700">Images</label>
                                    <div id="images-container">
                                        <div class="flex items-center mb-2">
                                            <input type="file" multiple name="images[]" accept="image/*" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            <button type="button" onclick="addImage()" class="ml-2 text-blue-600">Add Image</button>
                                        </div>
                                    </div>
                                </div>


                                {{-- <div class="mb-4">
                                    <label for="tasks" class="block text-sm font-medium text-gray-700">Tasks</label>
                                    <div id="tasks-container">
                                        <div class="flex items-center mb-2">
                                            <input type="checkbox" name="tasks[]" value="Task 1" class="mr-2">
                                            <label for="tasks[]" class="text-sm text-gray-700">Task 1</label>
                                        </div>
                                        <div class="flex items-center mb-2">
                                            <input type="checkbox" name="tasks[]" value="Task 2" class="mr-2">
                                            <label for="tasks[]" class="text-sm text-gray-700">Task 2</label>
                                        </div>
                                        <div class="flex items-center mb-2">
                                            <input type="checkbox" name="tasks[]" value="Task 3" class="mr-2">
                                            <label for="tasks[]" class="text-sm text-gray-700">Task 3</label>
                                        </div>
                                        <button type="button" onclick="addTask()" class="mt-2 text-blue-600">Add Task</button>
                                    </div>
                                </div> --}}

                                <div class="flex justify-end">
                                    <button type="button" onclick="newCard()" class="text-gray-600 hover:text-gray-900 mr-3">Cancel</button>
                                    <button type="button" onclick="handleFormSubmission()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Card</button>
                                </div>
                            </form>

                        </div>
                    </div>


                    <!-- Modal IMAGE DAN LINK -->
                    <div id="modalEditTask" class=" add-card hidden fixed inset-0 bg-gray-800 bg-opacity-50 items-center justify-center">
                        <div class=" bg-white rounded-lg p-6 w-1/3 card-layout-scoll">
                            <h2 class="text-xl font-semibold mb-4">Edit Task</h2>
                            <form method="POST" enctype="multipart/form-data" id ="taskEdit">
                                @csrf
                                <input type="hidden" name="project_id" value="{{ $project->id }}">
                                <input type="hidden" name="taskId" id="taskIdInput">

                                <div class="mb-4">
                                    <label for="title" class="block text-sm font-medium text-gray-700">Card Title</label>
                                    <input type="text" id="titleTaskEdit" name="titleTaskEdit" required
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>

                                <div class="mb-4">
                                    <label for="description" class="block text-sm font-medium text-gray-700">Card Description</label>
                                    <div class="border">
                                        <div id="description-container-edit" style="height: 200px;"
                                            class="border border-gray-300 rounded-md"></div>
                                        <input type="hidden" id="descriptionTaskEdit" name="description">
                                    </div>


                                </div>

                                <div class="mb-4">
                                    <label for="links" class="block text-sm font-medium text-gray-700">Links</label>
                                    <div id="links-container-task-edit">
                                        <div class="flex items-center mb-2">
                                            <input type="url" name="linksEdit[]" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Enter link">
                                            <button type="button" onclick="addLinkEdit()" class="ml-2 text-blue-600">Add Link</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="Images" class="block text-sm font-medium text-gray-700">Images</label>
                                    <div id="images-container-task-edit">
                                        <div class="flex items-center mb-2">
                                            <input type="file" name="imagesEdit[]" accept="image/*" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            <button type="button" onclick="addImageEdit()" class="ml-2 text-blue-600">Add Image</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button type="button" onclick="editTask()" class="text-gray-600 hover:text-gray-900 mr-3">Cancel</button>
                                    <button type="button" onclick="handleEditTask()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Confirm Edit Task</button>
                                </div>
                            </form>

                        </div>
                    </div>

                    <div id="modalImageBukti" class=" add-card hidden fixed inset-0 bg-gray-800 bg-opacity-50 items-center justify-center" onclick="closeModalOnOutsideClickImageSubmit(event)">
                        <div class=" bg-white rounded-lg p-6 w-1/3 card-layout-scoll"  onclick="event.stopPropagation()">
                            <h2 class="text-xl font-semibold mb-4">Submit bukti pengerjaan</h2>
                            <form method="POST" enctype="multipart/form-data" id ="formImageBukti">
                                @csrf
                                <input type="hidden" name="project_id" value="{{ $project->id }}">
                                <input type="hidden" name="taskId" id="taskIdInput">


                                <div class="mb-4">
                                    <label for="Images" class="block text-sm font-medium text-gray-700">Images</label>
                                    <div id="images-container-task-edit">
                                        <div class="flex items-center mb-2">
                                            <input type="file" name="imagesReportSubmit[]" accept="image/*" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            <button type="button" onclick="addImageEdit()" class="ml-2 text-blue-600">Add Image</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button type="button" onclick="closeModalImageSubmit()" class="text-gray-600 hover:text-gray-900 mr-3">Cancel</button>
                                    <button type="button" onclick="handleImageSubmit()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Submit Image</button>
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
                <button onclick="closeModalImageDescription()" style="width: 44px; position: absolute; top: 6%; right: 6%; background: rgba(100, 100, 100, 0.664); color: white; border: none; padding: 10px; cursor: pointer; border-radius: 999px;">X</button>
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


window.editTask = function () {
    const modal = document.getElementById("modalEditTask");

    // Menambah/menghapus kelas hidden dan flex untuk membuka modal
    modal.classList.toggle("hidden");
    modal.classList.toggle("flex");

    const pathname = window.location.pathname;
    const projectId = pathname.split("/")[2];
    const taskId = pathname.split("/")[4];

    // Ambil data task berdasarkan taskId
    axios
    .get(`/project/${projectId}/tasks/${taskId}/EditData`)
    .then(response => {
        const data = response.data;

        // Mengisi form dengan data yang diterima
        document.getElementById('titleTaskEdit').value = data.task.title;

        // Mengisi Card Description dengan format Quill
        const descriptionContainer = document.getElementById('description-container-edit');

        // Cek apakah Quill sudah diinisialisasi
        let quill;
        if (!descriptionContainer.__quill) {
            // Inisialisasi Quill pada elemen container
            quill = new Quill(descriptionContainer, {
                theme: "snow",
                modules: {
                    toolbar: [
                        [{ list: "ordered" }, { list: "bullet" }],
                        ["bold", "italic", "underline"],
                        [{ align: [] }],
                    ],
                },
            });

            // Simpan instance Quill pada elemen container
            descriptionContainer.__quill = quill;
        } else {
            quill = descriptionContainer.__quill;
        }

        // Parsing Delta JSON dari data
        const quillDescription = JSON.parse(data.description.text);

        // Filter Delta untuk hanya menyertakan `insert` tanpa atribut tambahan
        const filteredDelta = quillDescription.ops.filter(op => {
            return typeof op.insert === "string" && !op.attributes;
        });

        // Mengisi editor Quill dengan Delta yang sudah difilter
        quill.setContents({ ops: filteredDelta });

        // Mengisi hidden field description
        document.getElementById('descriptionTaskEdit').value = JSON.stringify(quillDescription);

        // Mengisi Links
        const linksContainer = document.getElementById('links-container-task-edit');
        linksContainer.innerHTML = ''; // Bersihkan links-container sebelum menambah

        // Mengambil hanya link yang ada dalam format Quill
        const links = data.description.text.match(/"link":"([^"]+)"/g);

        if (links && links.length > 0) {
            links.forEach(link => {
                // Parse string menjadi objek untuk mendapatkan nilai link
                const parsedLink = JSON.parse(`{${link}}`);
                const linkValue = parsedLink.link; // Ambil nilai link
                addLinkEdit(linkValue); // Tambahkan input dengan nilai link
            });
        } else {
            addLinkEdit(); // Tambahkan input default jika tidak ada link
        }

        // Mengisi Images
        const imagesContainer = document.getElementById('images-container-task-edit');
        imagesContainer.innerHTML = ''; // Bersihkan images-container sebelum menambah

        const images = data.description.text.match(/"image":"([^"]+)"/g);

        if (images && images.length > 0) {
            images.forEach(image => {
                const parsedImage = JSON.parse(`{${image}}`);
                const imageUrl = parsedImage.image; // Ambil nilai image

                // const input = document.createElement('input');
                // input.type = 'hidden';
                // input.name = 'imagesEdit[]';
                // input.value = imageUrl;
                // document.getElementById('images-container-task-edit').appendChild(input);

                addImageEdit(imageUrl, '', 'top'); // Tambahkan input dengan nilai image

            });
            addImageEdit('', '', 'bottom');
        } else {
            addImageEdit('', '', 'bottom'); // Tambahkan input default jika tidak ada image
        }
    })
    .catch(error => {
        console.error('Error fetching task data:', error);
    });
};





        document.addEventListener("DOMContentLoaded", () => {
            const quill = new Quill("#description-container", {
                theme: "snow",
                modules: {
                    toolbar: [
                        [{ list: "ordered" }, { list: "bullet" }],
                        ["bold", "italic", "underline"],
                        [{ align: [] }],
                    ],
                },
            });
        });

     function closeModal() {
        document.getElementById('participantModal').classList.add('hidden');
    }

    document.addEventListener("DOMContentLoaded", function () {
    const currentUrl = window.location.href;


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

    function addLinkEdit(linkValue = '') {
        const linksContainer = document.getElementById('links-container-task-edit');
        const newLinkDiv = document.createElement('div');
        newLinkDiv.classList.add('flex', 'items-center', 'mb-2');

        // Membuat input link
        newLinkDiv.innerHTML = `
            <input type="url" name="linksEdit[]" value="${linkValue}"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                placeholder="Enter link"
                oninput="checkLinkInput()">
        `;

        // Menambahkan tombol "Remove" pada semua input kecuali yang pertama
        if (linksContainer.querySelectorAll('.flex').length > 1) {
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.textContent = 'Remove';
            removeButton.classList.add('ml-2', 'text-red-600');
            removeButton.onclick = () => removeLink(removeButton); // Menghapus input saat tombol "Remove" diklik
            newLinkDiv.appendChild(removeButton);
        }

        linksContainer.appendChild(newLinkDiv);

        // Pastikan tombol "Add" selalu ada di bawah input terakhir
        addAddButton();
    }

    function removeLink(button) {
        const linkDiv = button.parentElement;
        linkDiv.remove();
        addAddButton(); // Perbarui tombol "Add" setelah penghapusan
    }

    function addAddButton() {
        const linksContainer = document.getElementById('links-container-task-edit');
        const existingAddButton = linksContainer.querySelector('.add-button');

        // Hapus tombol "Add" yang sudah ada
        if (existingAddButton) {
            existingAddButton.remove();
        }

        // Menambahkan tombol "Add" di bawah input terakhir
        const addButtonDiv = document.createElement('div');
        addButtonDiv.classList.add('flex', 'items-center', 'mb-2', 'add-button');
        const addButton = document.createElement('button');
        addButton.type = 'button';
        addButton.textContent = 'Add';
        addButton.classList.add('ml-2', 'text-blue-600');
        addButton.onclick = () => addLinkEdit(); // Menambahkan input baru saat tombol "Add" diklik
        addButtonDiv.appendChild(addButton);
        linksContainer.appendChild(addButtonDiv);
    }



    function addImage() {
        const imagesContainer = document.getElementById('images-container');

        // Membuat elemen container baru untuk input gambar
        const imageGroup = document.createElement('div');
        imageGroup.classList.add('flex', 'items-center', 'mb-2');

        // Membuat elemen input file baru
        const imageInput = document.createElement('input');
        imageInput.type = 'file';
        imageInput.name = 'images[]';
        imageInput.accept = 'image/*';
        imageInput.classList.add('mt-1', 'block', 'w-full', 'border', 'border-gray-300', 'rounded-md', 'shadow-sm', 'focus:ring-blue-500', 'focus:border-blue-500', 'sm:text-sm');

        // Membuat tombol Remove
        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.textContent = 'Remove';
        removeButton.classList.add('ml-2', 'text-red-600');
        removeButton.onclick = function () {
            imagesContainer.removeChild(imageGroup);
        };

        // Menambahkan input dan tombol ke dalam grup
        imageGroup.appendChild(imageInput);
        imageGroup.appendChild(removeButton);

        // Menambahkan grup ke dalam container
        imagesContainer.appendChild(imageGroup);
    }

// Fungsi untuk menambahkan gambar dan input gambar
function addImageEdit(imageUrl = '', imageName = '', position = 'top') {
    const imagesContainer = document.getElementById('images-container-task-edit');
    const newImageDiv = document.createElement('div');
    newImageDiv.classList.add('flex', 'items-center', 'mb-2', 'space-x-2'); // Menjajar gambar dan tombol

    if (imageUrl) {
        // Jika ada URL gambar, tampilkan thumbnail dan tombol "Remove"
        console.log("Add Image:", imageUrl);
        newImageDiv.innerHTML = `
            <div class="relative flex items-center">
                <img src="${imageUrl}" alt="Thumbnail" class="w-12 h-12 object-cover rounded-md">
                <span class="ml-2">${imageName}</span>
                <button type="button" class="ml-2 text-red-600" onclick="removeImage(this)">
                    Remove
                </button>
                <input type="url" name="imagesEdit[]" value="${imageUrl}" class="hidden">
            </div>
        `;
    } else {
        // Jika tidak ada URL gambar, tampilkan input untuk memasukkan gambar
        newImageDiv.innerHTML = `
            <div class="relative flex items-center">
                <div class="w-12 h-12 bg-gray-200 flex items-center justify-center rounded-md cursor-pointer" onclick="triggerFileInput(this)">
                    <span class="text-xl text-gray-600">+</span>
                </div>
                <input type="file" name="imagesEdit[]" class="hidden" onchange="handleFileSelect(this)">
            </div>
        `;
    }

    if (position === 'top') {
        imagesContainer.prepend(newImageDiv); // Tambahkan di atas
    } else {
        imagesContainer.appendChild(newImageDiv); // Tambahkan di bawah
    }

}

// Fungsi untuk menghapus gambar dan input terkait
function removeImage(button) {
    const imageDiv = button.closest('.flex');
    imageDiv.remove();
    addAddImageButton(); // Perbarui tombol "Add Image" setelah penghapusan
}



// Fungsi untuk memicu input file
function triggerFileInput(imageDiv) {
    const fileInput = imageDiv.nextElementSibling;
    fileInput.click(); // Memicu file input untuk memilih gambar
}

// Fungsi untuk menangani pemilihan file
function handleFileSelect(input) {
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const base64Data = e.target.result; // Base64 data dari FileReader
            const fileFromBase64 = base64ToFile(base64Data, file.name); // Konversi Base64 ke File

            // Tambahkan gambar ke container dengan file yang dikonversi
            addImageEdit(URL.createObjectURL(fileFromBase64), file.name);

            // Simpan file ke elemen input hidden untuk dikirimkan ke server
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'file';
            hiddenInput.name = 'imagesEdit[]';
            hiddenInput.classList.add('hidden');

            // Gunakan DataTransfer untuk mengisi file input dengan file yang baru
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(fileFromBase64);
            hiddenInput.files = dataTransfer.files;

            // Tambahkan input hidden ke form atau container
            const imagesContainer = document.getElementById('images-container-task-edit');
            imagesContainer.appendChild(hiddenInput);
        };
        reader.readAsDataURL(file);

        input.value = '';
    }
}

function base64ToFile(base64, filename) {
    const arr = base64.split(',');
    const mime = arr[0].match(/:(.*?);/)[1];
    const bstr = atob(arr[1]);
    let n = bstr.length;
    const u8arr = new Uint8Array(n);

    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }

    return new File([u8arr], filename, { type: mime });
}


// Fungsi untuk menampilkan gambar yang sudah ada dari URL
function showExistingImages() {
    const images = [
        { url: '/storage/images/bMSFGVKaXrr6IEWLrtPsDMCy7dW3LOy9QRhmrP2Z.png', name: 'Image 1' },
        { url: '/storage/images/cWCQRC0McZoiuOH3XqAuypE0TcBsCJWud8GTWSkp.png', name: 'Image 2' }
    ];

    images.forEach(image => {
        addImageEdit(image.url, image.name);
    });
}

// Panggil showExistingImages untuk menampilkan gambar yang sudah ada
showExistingImages();






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



// --------------------------INPUT GAMBAR LINK KE QUILL----------------------------------

function handleFormSubmission() {
    // Ambil nilai dari input title
    const title = document.querySelector('#title').value;
    const quillEditor = document.querySelector('#description-container').__quill;

    // Validasi title
    if (!title.trim()) {
        alert('Title is required!');
        return;
    }

    // Ambil data dari input gambar (sekarang bisa banyak gambar)
    const imageInputs = document.querySelectorAll('[name="images[]"]');
    const links = Array.from(document.querySelectorAll('[name="links[]"]')).map(input => input.value);


    // Buat FormData untuk mengirim gambar ke server
    const formData = new FormData();


    // Loop untuk setiap input gambar dan tambahkan ke FormData
    imageInputs.forEach(input => {
        const files = input.files;
        if (files.length > 0) {
            formData.append('images[]', files[0]); // Mengambil file pertama dari setiap input
        }
    });

    // Kirim gambar ke server menggunakan AJAX
    if (formData.has('images[]')) {
        fetch(`/upload-image`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            // Jika ada gambar, sisipkan URL gambar ke Quill

            if (data.imageUrls) {
                quillEditor.insertEmbed(quillEditor.getLength() - 1, 'image', { image: data.imageUrls });
            }

            // Lanjutkan proses pengiriman form
            finalizeSubmission(quillEditor, links, title, data.imageUrls);
        })
        .catch(error => {
            console.error('Error uploading image:', error);
            // Tetap lanjutkan pengiriman form meskipun ada error
            finalizeSubmission(quillEditor, links, title, imageUrls);
        });

    } else {
        // Jika tidak ada gambar, langsung lanjutkan pengiriman form
        finalizeSubmission(quillEditor, links, title);
    }
}


function finalizeSubmission(quillEditor, links, title, imageUrls) {

    // Validasi dan perbaiki link
    const validatedLinks = links.map(link => {
        if (link && !/^https?:\/\/(www\.)?/.test(link)) {
            if (/^https?:\/\//.test(link)) {
                return link.replace(/^(https?:\/\/)/, '$1www.');
            } else {
                return `https://www.${link}`;
            }
        }
        return link;
    });

    // Menyisipkan link ke dalam konten Quill
    validatedLinks.forEach(link => {
        if (link) {
            quillEditor.insertText(quillEditor.getLength() - 1, link, { link: link });
        }
    });

    let imageUrlData = null;
    if (imageUrls && imageUrls.length > 0) {
        // Jika ada gambar, ambil URL gambar
        imageUrlData = { images: imageUrls };
    }

    // Mengirimkan konten Quill ke input hidden untuk dikirimkan ke server
    document.querySelector('#description').value = JSON.stringify(quillEditor.getContents());

    // Ambil data dari form dan tambahkan title serta taskId
    const formData = new FormData(document.querySelector('#taskForm'));
    const formObject = {};


    // Mengonversi FormData menjadi objek
    formData.forEach((value, key) => {
        formObject[key] = value;
    });

    formObject.title = title; // Menambahkan title ke formObject
    formObject.description = JSON.stringify(quillEditor.getContents()); // Menambahkan konten Quill

    if (imageUrlData) {
        formObject.images = imageUrlData.images; // Menambahkan URL gambar ke formObject
    }

    // Mengirimkan data dengan JSON
    fetch(`/tasks`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json' // Pastikan header Content-Type adalah 'application/json'
        },
        method: 'POST',
        body: JSON.stringify(formObject), // Mengirimkan data dalam format JSON
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Task berhasil diperbarui!');
            // Merefresh halaman
            window.location.reload();
        } else {
            alert('Terjadi kesalahan saat mengedit task');
        }
    })
    .catch(error => {
        console.error('Error submitting form:', error);
    });
}




// ---------------------------EDIT GAMBAR LINK DAN DESCRIPTION TASK----------------------

function handleEditTask() {
    const pathname = window.location.pathname;
    const taskId = pathname.split("/")[4];
    // Ambil kontainer Quill
    const quillEditor = document.querySelector('#description-container-edit').__quill;

    // Ambil data dari input gambar (bisa banyak gambar)
    const imageInputs = document.querySelectorAll('[name="imagesEdit[]"]');

    console.log(imageInputs);


    // Buat FormData untuk mengirim gambar ke server
    const formData = new FormData();



    // Loop untuk setiap input gambar dan tambahkan ke FormData
    imageInputs.forEach(input => {
        if (input.files && input.files.length > 0) { // Periksa jika files tidak null dan ada file
            test = formData.append('imagesEdit[]', input.files[0]); // Mengambil file pertama dari setiap input
        } else {
            console.log("Tidak ada file yang dipilih atau input tidak valid.");
        }
    });

    console.log(formData.has('imagesEdit[]'));




    // Kirim gambar ke server menggunakan AJAX
    if (formData.has('imagesEdit[]')) {
        fetch(`/upload-image`, { // Ganti dengan route yang sesuai di Laravel
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                 // Jika ada gambar yang sudah ada (dari database atau server), sisipkan URL gambar ke Quill
                if (data.imageUrls) {
                    data.imageUrls.forEach(imageUrl => {
                        quillEditor.insertEmbed(quillEditor.getLength(), 'image', imageUrl);
                    });
                }

                const imageInputs = document.querySelectorAll('[name="imagesEdit[]"]');

                // Ekstrak nilai value dari setiap elemen input
                const imageUrls = Array.from(imageInputs)
                    .map(input => input.value) // Ambil nilai
                    .filter(value => {
            // Filter untuk memastikan nilai yang valid
                        return value !== '' &&
                            !value.includes('C:\\fakepath\\') &&  // Menghindari path lokal
                            !value.startsWith('blob:') &&         // Menghindari blob URL
                            !value.startsWith('//');              // Menghindari URL yang tidak lengkap
                    });

                // Menambahkan setiap gambar baru ke Quill editor satu per satu
                imageUrls.forEach(imageUrl => {
                    quillEditor.insertEmbed(quillEditor.getLength(), 'image', imageUrl);
                });

                // Lanjutkan proses pengiriman form
                finalizeEditSubmission(quillEditor, taskId, [...data.imageUrls, ...imageUrls]); // Gabungkan gambar yang sudah ada dan gambar baru

            })
            .catch(error => {
                console.error('Error uploading image:', error);
                // Tetap lanjutkan pengiriman form meskipun ada error
                finalizeEditSubmission(quillEditor, taskId);
            });
    } else {
        const imageInputs = document.querySelectorAll('[name="imagesEdit[]"]');

        // Ekstrak nilai value dari setiap elemen input
        const imageUrls = Array.from(imageInputs)
            .map(input => input.value) // Ambil nilai
            .filter(value => value !== ''); // Hanya ambil yang tidak kosong

        // Menambahkan setiap gambar ke Quill editor satu per satu
        imageUrls.forEach(imageUrl => {
            quillEditor.insertEmbed(quillEditor.getLength() - 1, 'image', imageUrl);
        });

        // Kirim data ke fungsi finalizeEditSubmission
        finalizeEditSubmission(quillEditor, taskId, imageUrls);
    }
}

function finalizeEditSubmission(quillEditor, taskId, imageUrls) {

    console.log(imageUrls);


    // Ambil data dari form dan tambahkan taskId
    const formData = new FormData(document.querySelector('#taskEdit'));
    const formObject = {};

    // Mengonversi FormData menjadi objek
    formData.forEach((value, key) => {
        formObject[key] = value;
    });

    // Ambil link dari formData dan validasi
    const formLinks = formData.getAll('linksEdit[]');

    // Validasi dan perbaiki link
    const validatedLinks = formLinks.map(link => {
        if (link && !/^https?:\/\/(www\.)?/.test(link)) {
            if (/^https?:\/\//.test(link)) {
                return link.replace(/^(https?:\/\/)/, '$1www.');
            } else {
                return `https://www.${link}`;
            }
        }
        return link;
    });

    // Menyisipkan link ke dalam konten Quill setelah gambar
    validatedLinks.forEach(link => {
        if (link) {
            quillEditor.insertText(quillEditor.getLength() - 1, link, { link: link });
        }
    });

    // Mengambil URL gambar dari Quill atau imageUrls yang telah diberikan
    let imageUrlData = null;
    if (imageUrls && imageUrls.length > 0) {
        // Jika ada gambar, ambil URL gambar
        imageUrlData = { images: imageUrls };
    }


    // Mengirimkan konten Quill ke input hidden untuk dikirimkan ke server
    document.querySelector('#descriptionTaskEdit').value = JSON.stringify(quillEditor.getContents());

    // Menambahkan taskId dan image ke dalam objek formObject
    formObject.taskId = taskId;
    formObject.description = JSON.stringify(quillEditor.getContents()); // Menambahkan konten Quill

    if (imageUrlData) {
        formObject.images = imageUrlData.images; // Menambahkan URL gambar ke formObject
    }

    // Mengirimkan data dengan JSON
    fetch(`/tasks/${taskId}/edit`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json' // Pastikan header Content-Type adalah 'application/json'
        },
        method: 'POST',
        body: JSON.stringify(formObject), // Mengirimkan data dalam format JSON
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Task berhasil diperbarui!');
            // Merefresh halaman
            window.location.reload();
        } else {
            alert('Terjadi kesalahan saat mengedit task');
        }
    })
    .catch(error => {
        console.error('Error submitting form:', error);
    });
}





</script>


