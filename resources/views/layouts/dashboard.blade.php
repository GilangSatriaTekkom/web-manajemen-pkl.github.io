<!-- resources/views/dashboard.blade.php -->
<div class="flex flex-row">
    <div>
        @extends('layouts.app')
        @section('content')
        <div class="flex flex-row">
            @include('layouts.partials.sidebar')
            <div class="w-full ml-64">
                @include('layouts.partials.header')
                <div class="flex flex-row justify-between px-4">
                    <div class="pr-4 pl-2 py-2 border border-gray-300 rounded-md w-full max-w-[480px] flex flex-row items-center gap-3">
                        <input type="text" id="search-input" placeholder="Search..." class="border-none w-full">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
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

                    </div>

                    @if (Auth::user()->roles === 'admin' || Auth::user()->roles === 'pembimbing')
                        <!-- Tombol +Create dengan Checkbox -->
                        <button id="open-modal-btn" class="px-[24px] py-[12px] rounded-full bg-[#0A0A18] text-white hover:bg-[#54577A]">
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
                                        <label for="search-users" class="block text-sm font-medium text-gray-700">Search Peserta
                                            PKL</label>
                                        <input type="text" id="search-users" placeholder="Cari peserta PKL..."
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        {{--    ID peserta:   --}}
                                        <input type="hidden" name="participants" id="participants">
                                        {{--    End ID Peserta   --}}

                                        <ul id="suggestions"
                                            class="bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-auto hidden"></ul>
                                        <div id="selected-participants" class="mt-2 flex flex-wrap gap-2"></div>
                                    </div>

                                    <button type="button" id="close-modal-btn" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                        Assign Project
                                    </button>
                                </form>
                            </div>
                        </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mt-16 px-4">
                    @foreach ($projects as $project)
                    <div class="bg-white rounded shadow relative p-4">
                         <button class="absolute size-5 top-2 right-2 text-gray-500 hover:text-gray-700 focus:outline-none " onclick="toggleDropdown({{ $project->id }})">
                                &#x22EE;
                            </button>
                        <a class=" z-0" href="{{ route('project.show', $project->id) }}">


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


                            <h2 class="text-xl font-semibold">
                                {{ $project->name }}
                            </h2>
                            <p>{{ $project->genre }}</p>
                            <div class="flex flex-row justify-between  items-center w-full">
                                <p class="flex flex-row items-center">Created by:
                                    <img class="w-[24px] h-[24px] rounded-full ml-1" src="{{ asset('storage/' . $creatorPict) }}" alt="">
                                </p>
                                <a href="javascript:void(0)" onclick="openModal('{{ $project->id }}')" class="flex items-center space-x-[-10px]">
                                    @foreach ($project['participants'] as $index => $participant)

                                        @if ($index < 5)
                                            <!-- Gambar Profil -->
                                            <div class="w-[24px] h-[24px] rounded-full border-2 border-white overflow-hidden">
                                                <img src="{{ $participant['profile_image'] ? asset('storage/' . $participant->profile_pict) : asset('images/default-profile.png') }}" alt="Profile {{ $index + 1 }}" class="w-full h-full object-cover" />
                                            </div>
                                        @elseif ($index === 5)
                                            <!-- Placeholder untuk peserta tambahan -->
                                            <div class="w-[24px] h-[24px] rounded-full border-2 border-white bg-gray-200 flex items-center justify-center text-sm font-semibold">
                                                +{{ count($project['participants']) - 5 }}
                                            </div>
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


<script>
    document.querySelector('form').addEventListener('submit', function(event) {
        event.preventDefault(); // Mencegah submit untuk sementara waktu
        const formData = new FormData(this);
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        this.submit(); // Melanjutkan submit setelah pengecekan
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-users');
        const suggestionsList = document.getElementById('suggestions');
        const selectedParticipants = document.getElementById('selected-participants');
        const participantsInput = document.getElementById('participants');

        searchInput.addEventListener('input', function() {
            const query = searchInput.value.trim();
            if (query.length > 2) {
                fetch(`/search-users?query=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        suggestionsList.innerHTML = '';
                        if (data.length > 0) {
                            suggestionsList.classList.remove('hidden');
                            data.forEach(user => {
                                // Cek apakah user sudah ada di selected participants
                                if (!isUserAlreadySelected(user.id)) {
                                    const li = document.createElement('li');
                                    li.textContent = `${user.name} || ${user.asal_sekolah}`;
                                    li.classList.add('p-2', 'hover:bg-gray-100',
                                        'cursor-pointer');
                                    li.addEventListener('click', function() {
                                        addParticipant(user.id, user.name, user
                                            .asal_sekolah);
                                        searchInput.value = '';
                                        suggestionsList.classList.add('hidden');
                                    });
                                    suggestionsList.appendChild(li);
                                }
                            });
                        } else {
                            suggestionsList.classList.add('hidden');
                        }
                    });
            } else {
                suggestionsList.classList.add('hidden');
            }
        });

        function isUserAlreadySelected(userId) {
            return document.querySelector(`input[value="${userId}"]`) !== null;
        }

        function addParticipant(id, name, asal_sekolah) {
            // Menambahkan peserta yang dipilih ke kontainer
            const participantDiv = document.createElement('div');
            participantDiv.classList.add('bg-gray-200', 'p-2', 'rounded', 'flex', 'items-center', 'gap-2');

            const participantText = document.createElement('span');
            participantText.textContent = `${name} (${asal_sekolah})`;

            const removeButton = document.createElement('button');
            removeButton.textContent = 'Remove';
            removeButton.classList.add('text-red-600');
            removeButton.addEventListener('click', function() {
                selectedParticipants.removeChild(participantDiv);
                removeParticipantId(id); // Hapus ID dari input hidden
            });

            participantDiv.appendChild(participantText);
            participantDiv.appendChild(removeButton);
            selectedParticipants.appendChild(participantDiv);

            addParticipantId(id); // Tambahkan ID ke input tersembunyi
        }

        function addParticipantId(id) {
            let currentParticipants = participantsInput.value ? JSON.parse(participantsInput.value) : [];
            currentParticipants.push(id);
            participantsInput.value = JSON.stringify(currentParticipants);
        }

        function removeParticipantId(id) {
            let currentParticipants = participantsInput.value ? JSON.parse(participantsInput.value) : [];
            currentParticipants = currentParticipants.filter(participantId => participantId !== id);
            participantsInput.value = JSON.stringify(currentParticipants);
        }



    });
</script>

<script>
    // Memasukkan teks editor ke dalam input hidden sebelum submit
    document.querySelector('form').addEventListener('submit', function() {
        document.getElementById('project-description').value = document.getElementById('editor').innerText;
        // Masukkan data peserta yang dipilih ke input hidden
        const selectedParticipants = Array.from(document.querySelectorAll('#selected-participants span'))
            .map(participant => participant.dataset.userId);
        document.getElementById('selected-participants').value = JSON.stringify(selectedParticipants);
    });
</script>
