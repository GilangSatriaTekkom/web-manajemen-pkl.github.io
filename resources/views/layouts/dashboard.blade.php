<!-- resources/views/dashboard.blade.php -->
<div class="flex flex-row">
    <div>
        @extends('layouts.app')
        @section('content')
        <div class="flex flex-row">
            @include('layouts.partials.sidebar')
            <div class="w-full ml-64">


                <div class="bg-white py-4 px-4 ">
                    @include('layouts.partials.header')
                    <div class="flex flex-row justify-between px-4">
                        <div class="pr-4 pl-2 py-2 border border-gray-300 rounded-md w-full max-w-[480px] flex flex-row items-center gap-3">
                            <form method="GET" action="{{ route('search.projects') }}" class="flex gap-4 w-full m-0 items-center" id="search-form">
                                <input type="text" id="search-input" placeholder="Search..." class="border-none w-full" type="text" name="query" value="{{ request('query') }}" placeholder="Cari berdasarkan ID atau nama proyek...">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" id="search-icon" style="cursor: pointer;">
                                    <g clip-path="url(#clip0_2207_4271)">
                                        <path d="M9.58268 17.5C13.9549 17.5 17.4993 13.9556 17.4993 9.58335C17.4993 5.2111 13.9549 1.66669 9.58268 1.66669C5.21043 1.66669 1.66602 5.2111 1.66602 9.58335C1.66602 13.9556 5.21043 17.5 9.58268 17.5Z" stroke="#8E92BC" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M18.3327 18.3333L16.666 16.6666" stroke="#8E92BC" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_2207_4271">
                                            <rect width="20" height="20" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </form>


                        </div>

                        @if (Auth::user()->roles === 'admin' || Auth::user()->roles === 'pembimbing')
                            <!-- Tombol +Create dengan Checkbox -->
                            <button id="open-modal-btn" class="px-[24px] py-[12px] font-bold rounded-full bg-[#54577A] text-white hover:bg-[#0A0A18]">
                                + Create Project
                            </button>
                        @endif

                        <div id="create-project-modal" class="fixed inset-0 z-10 bg-black bg-opacity-50 hidden items-center justify-center" style="z-index: 10;">
                            <div class="bg-white rounded-lg shadow-lg w-1/3">
                                <div class="p-4 border-b">
                                    <h3 class="text-lg font-semibold">Create Project</h3>
                                </div>
                                <form method="POST" action="{{ route('assign-project') }}" class="p-4 scroll-auto">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="project-name" class="block text-sm font-medium text-gray-700">Project Name</label>
                                        <input type="text" name="name" id="project-name" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div class="mb-4">
                                        <label for="search-users" class="block text-sm font-medium text-gray-700">Search Peserta PKL</label>
                                        <input type="text" id="search-users" placeholder="Cari peserta PKL..."
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        {{-- ID peserta: --}}
                                        <input type="hidden" name="participants" id="participants">
                                        {{-- End ID Peserta --}}

                                        <ul id="suggestions" class="bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-auto hidden"></ul>
                                        <div id="selected-participants" class="mt-2 flex flex-wrap gap-2"></div>
                                    </div>

                                    <button type="button" id="close-modal-btn" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                                        Cancel
                                    </button>
                                    <button onclick="showConfirmation(event)" type="button" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                        Assign Project
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>


                <div class="grid grid-cols-3 gap-4 mt-16 px-4">
                    @foreach ($projects as $project)
                        <div class="bg-white rounded shadow relative p-3">
                            {{-- <button class="absolute size-5 top-2 right-2 text-gray-500 hover:text-gray-700 focus:outline-none" onclick="toggleDropdownProject({{ $project->id }})">
                                &#x22EE;
                            </button>

                            <!-- Dropdown yang tersembunyi secara default -->
                            <div id="dropdown-{{ $project->id }}" class="dropdown-menu absolute bg-white border border-gray-300 rounded shadow-lg mt-2 right-0">
                                <button class="block w-full text-left px-4 py-2 text-red-500 hover:bg-gray-200" onclick="deleteProject({{ $project->id }})">
                                    Delete
                                </button>
                            </div> --}}
                            <a class="z-0" href="{{ route('project.show', $project->id) }}">
                                <div id="dropdownDelete-{{ $project->id }}" class="dropdown-project absolute right-0 mt-2 w-32 bg-white rounded shadow hidden">
                                    <ul class="text-sm text-gray-700">
                                        <li>
                                            <button onclick="openEditModal({{ json_encode($project) }})" class="block px-4 py-2 hover:bg-gray-100 w-full text-left">
                                                Edit
                                            </button>
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('projects.destroy', $project->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="block px-4 py-2 hover:bg-gray-100 w-full text-left text-red-500">
                                                    Hapus
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>

                                <h2 class="text-xl font-semibold">
                                    {{ $project->name }}
                                </h2>
                                <p>{{ $project->genre }}</p>
                                <div class="flex flex-row justify-between items-center w-full">
                                    <p class="flex flex-row items-center">Created by:
                                        <img class="w-[24px] h-[24px] rounded-full ml-1" src="{{ asset($creatorPict ? 'storage/' . $creatorPict : 'images/default-profile.png') }}" alt="">
                                    </p>
                                    <a href="{{ route('project.participants', $project->id) }}" class="flex items-center -space-x-2">
                                        @foreach ($project['participants'] as $index => $participant)
                                            @if ($index < 2)
                                                <div class="w-8 h-8 rounded-full border-2 border-white overflow-hidden">
                                                    <img src="{{ $participant['profile_pict'] ? asset('storage/' . $participant['profile_pict']) : asset('images/default-profile.png') }}" alt="Profile {{ $index + 1 }}" class="w-full h-full object-cover">
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
                            </a>
                        </div>
                    @endforeach

                    @include('layouts.partials.participantModal')
                </div>
            </div>
        </div>
        @endsection
    </div>
</div>

<script>

document.addEventListener('DOMContentLoaded', function() {
    // Pastikan elemen yang dimaksud ada di DOM
    const someElement = document.querySelector('#search-input'); // Ganti dengan selector yang sesuai

    if (someElement) {
        // Lakukan sesuatu jika elemen ditemukan
        someElement.addEventListener('click', function() {
            if (someElement.classList.contains('active')) {
                console.log('Elemen memiliki kelas "active"');
            }
        });
    } else {
        console.error('Elemen tidak ditemukan');
    }
});



function showConfirmation(event) {
        event.preventDefault(); // Mencegah form dikirimkan langsung

        // Menampilkan konfirmasi menggunakan SweetAlert
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to assign this project?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, assign it!',
            cancelButtonText: 'No, cancel!',
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika pengguna mengonfirmasi, kirimkan form
                event.target.submit(); // Kirimkan form setelah konfirmasi
            }
        });
    };

function toggleDropdownProject(projectId) {
        const dropdown = document.getElementById('dropdownDelete-' + projectId);
        dropdown.classList.toggle('hidden'); // Menampilkan atau menyembunyikan dropdown
    }

    // Fungsi untuk menghapus project
    function deleteProject(projectId) {
        if (confirm('Are you sure you want to delete this project?')) {
            // Proses penghapusan project (misalnya, kirim permintaan ke server)
            alert('Project ' + projectId + ' deleted');
            // Lakukan aksi penghapusan project sesuai dengan kebutuhan
        }
    }

    import Quill from "quill";
    import "quill/dist/quill.snow.css";

    document.addEventListener("DOMContentLoaded", () => {
        window.quill = new Quill("#description-container", {
            theme: "snow",
            modules: {
                toolbar: [
                    [{ list: "ordered" }, { list: "bullet" }],
                    ["bold", "italic", "underline"],
                    [{ align: [] }],
                    ["link", "image"],
                ],
            },
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
        // Fungsi untuk membuka modal
        const participantModal = document.getElementById('participantModal');
        if (participantModal) {
            participantModal.addEventListener("click", function (event) {
                if (event.target === participantModal) {
                    participantModal.classList.add("hidden"); // Kembali ke halaman sebelumnya
                }
            });
        }
    });

    window.openModal = function (projectId) {
        // Kosongkan daftar peserta sebelumnya
        const participantList = document.getElementById('participantList'); // Ambil elemen ul
        participantList.innerHTML = ''; // Kosongkan isi daftar

        // Panggil API untuk mendapatkan data peserta
        axios.get('/dashboard/' + projectId + '/participants')
            .then((response) => {
                const participants = response.data.participants; // Ambil data peserta dari API
                participants.forEach((participant) => {
                    const li = document.createElement('li');
                    li.textContent = participant.name;
                    participantList.appendChild(li);
                });
            })
            .catch((error) => {
                console.error('Error fetching participants:', error);
            });
    };
</script>
