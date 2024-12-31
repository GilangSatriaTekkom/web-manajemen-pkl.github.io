<!-- resources/views/dashboard.blade.php -->
<div class="flex flex-row">

    <div>

        @extends('layouts.app')
        @section('content')

        <div class="flex flex-row h-full">
            @include('layouts.partials.sidebar')
            <div class="container mt-4">
                @include('layouts.partials.header')
                <div class="flex flex-row justify-between">
                <h1 class="text-2xl font-bold mb-4">Daftar Project</h1>
                @if (Auth::user()->roles === 'admin' || Auth::user()->roles === 'pembimbing')
                    <!-- Tombol +Create dengan Checkbox -->
                    <button id="open-modal-btn" class="px-5 py-3 bg-[#0A0A18] text-white rounded hover:bg-green-700">
                        + Create Project
                    </button>
                @endif
                </div>

                <div class="grid grid-cols-3 gap-4 mt-16">
                    @foreach ($projects as $project)
                    <div class="bg-white p-4 rounded shadow relative">
                        <button class="absolute size-5 top-2 right-2 text-gray-500 hover:text-gray-700 focus:outline-none " onclick="toggleDropdown({{ $project->id }})">
                            &#x22EE;
                        </button>

                        <div id="dropdown-{{ $project->id }}" class="dropdown-project absolute right-0 mt-2 w-32 bg-white rounded shadow hidden">
                            <ul class="text-sm text-gray-700">
                                <li>
                                    <button
                                        onclick="openEditModal({{ json_encode($project) }})"
                                        class="block px-4 py-2 hover:bg-gray-100 w-full text-left"
                                    >
                                        Edit
                                    </button>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('project.delete', $project->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="block px-4 py-2 hover:bg-gray-100 w-full text-left text-red-500"
                                        >
                                            Hapus
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>

                        <a href="{{ route('project.show', $project->id) }}">
                            <h2 class="text-xl font-semibold">
                                {{ $project->name }}
                            </h2>
                            <p>Created by:
                                {{ $userName }}
                            </p>
                            <a href="javascript:void(0)" onclick="openModal('{{ $project->id }}')" class="flex items-center space-x-[-10px]">
                                @foreach ($project['participants'] as $index => $participant)

                                    @if ($index < 5)
                                        <!-- Gambar Profil -->
                                        <div class="w-12 h-12 rounded-full border-2 border-white overflow-hidden">
                                            <img src="{{ $participant['profile_image'] ? asset('storage/' . $participant->profile_pict) : asset('images/default-profile.png') }}" alt="Profile {{ $index + 1 }}" class="w-full h-full object-cover" />
                                        </div>
                                    @elseif ($index === 5)
                                        <!-- Placeholder untuk peserta tambahan -->
                                        <div class="w-12 h-12 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center text-sm font-semibold">
                                            +{{ count($project['participants']) - 5 }}
                                        </div>
                                    @endif
                                @endforeach
                            </a>

                        </a>
                    </div>
                    @endforeach

                    <!-- Modal Popup -->
                    <div id="participantModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
                        <div class="bg-white p-6 rounded-lg w-96 relative">
                            <!-- Tombol X di pojok kanan atas untuk menutup modal -->
                            <button onclick="closeModal()" class="absolute top-2 right-2 text-xl font-bold text-gray-600">
                                &times;
                            </button>

                            <h3 class="text-lg font-semibold mb-4">Daftar Peserta</h3>
                            <ul id="participantList" class="flex flex-col gap-3">
                                <!-- Peserta akan dimasukkan di sini secara dinamis -->
                            </ul>

                            <!-- Tombol Tambah Peserta untuk admin atau pembimbing -->
                            {{-- <div id="addParticipantButton">
                                @if(isset($project) && $project->id)
                                    <button
                                        class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                        onclick="openEditModal('{{ $project->id }}')">
                                        Tambah Peserta
                                    </button>
                                @else
                                    <p class="text-gray-500">Tidak ada proyek yang tersedia.</p>
                                @endif
                            </div> --}}
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
        </div>
        @endsection
    </div>

</div>


<script>
    document.addEventListener("DOMContentLoaded", function () {
            // Fungsi untuk membuka modal
        participantModal = document.getElementById('participantModal');

        if (participantModal) {
            // Menambahkan event listener untuk keluar dari modal jika klik di luar area konten
            participantModal.addEventListener("click", function (event) {
                if (event.target === participantModal) {
                    console.log ("Clicked outside the modal");
                    participantModal.classList.add("hidden"); // Kembali ke halaman sebelumnya
                }
            });
        } else {
            console.error('Modal element with ID "taskPopup" not found');
        }
    })

    window.openModal = function (projectId) {
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


    function closeModal() {
        document.getElementById('participantModal').classList.add('hidden');
    }

    function toggleDropdown(projectId) {

        const dropdown = document.getElementById(`dropdown-${projectId}`);

        // Toggle visibility
        if (dropdown.classList.contains('hidden')) {
            dropdown.classList.remove('hidden');

            // Tambahkan event listener untuk klik di luar dropdown

        } else {
            dropdown.classList.add('hidden');
        }

    }



    document.addEventListener("DOMContentLoaded", function () {
            // Fungsi untuk membuka modal
        const dropdown = document.getElementById(`dropdown-${projectId}`);

        if (dropdown) {
            // Menambahkan event listener untuk keluar dari modal jika klik di luar area konten
            dropdown.addEventListener("click", function (event) {
                if (event.target === dropdown) {
                    console.log ("Clicked outside the modal");
                    dropdown.classList.add("hidden"); // Kembali ke halaman sebelumnya
                }
            });
        } else {
            console.error('Modal element with ID "taskPopup" not found');
        }
    })


    function openEditModal(projectId) {
        document.getElementById('edit-project-id').value = projectId;
        document.getElementById('edit-project-name').value = projectId.name;
        // Populate participants
        const participantsContainer = document.getElementById('edit-selected-participants');
        participantsContainer.innerHTML = '';
        project.participants.forEach(participant => {
            const participantDiv = document.createElement('div');
            participantDiv.classList.add('bg-gray-200', 'rounded', 'px-2', 'py-1');
            participantDiv.textContent = participant.name;
            participantsContainer.appendChild(participantDiv);
        });
        document.getElementById('edit-project-modal').classList.remove('hidden');
    }
</script>
