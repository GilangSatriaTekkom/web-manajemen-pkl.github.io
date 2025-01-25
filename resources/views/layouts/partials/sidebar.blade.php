<!-- resources/views/partials/sidebar.blade.php -->

<?php
// Ambil path dari URL
$currentPath = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

// Ambil value kedua
$secondValue = isset($currentPath[0]) ? $currentPath[0] : '';

// Daftar nilai yang valid
$validValues = ['peserta', 'pembimbing', 'dashboard', 'report', 'profile'];
?>

<div class="w-64 flex-1">
    <div class="bg-white fixed text-[#8E92BC] h-screen border-r-2 border-[#DCE4FF] shadow-md">
        <div class="p-4">
            <h2 class="text-lg font-bold">
                <a href="http://127.0.0.1:8000/dashboard">
                    <div class="text-lg font-semibold">Pkl Manajement Project</div>
                </a>
            </h2>
            <ul class="mt-4 space-y-2">
                <li class="<?= $secondValue === 'dashboard' ? 'activate' : '' ?>">
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 hover:bg-[#DFE1F3] rounded flex flex-row gap-3">
                        <svg class="" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22 16.7399V4.66994C22 3.46994 21.02 2.57994 19.83 2.67994H19.77C17.67 2.85994 14.48 3.92994 12.7 5.04994L12.53 5.15994C12.24 5.33994 11.76 5.33994 11.47 5.15994L11.22 5.00994C9.44 3.89994 6.26 2.83994 4.16 2.66994C2.97 2.56994 2 3.46994 2 4.65994V16.7399C2 17.6999 2.78 18.5999 3.74 18.7199L4.03 18.7599C6.2 19.0499 9.55 20.1499 11.47 21.1999L11.51 21.2199C11.78 21.3699 12.21 21.3699 12.47 21.2199C14.39 20.1599 17.75 19.0499 19.93 18.7599L20.26 18.7199C21.22 18.5999 22 17.6999 22 16.7399Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 5.48999V20.49"  stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7.75 8.48999H5.5"  stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8.5 11.49H5.5"  stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        Projects
                    </a>
                </li>
                <li class="<?= $secondValue === 'report' ? 'activate' : '' ?>">
                    @auth
                    @if (Auth::user()->roles === 'admin' || Auth::user()->roles === 'pembimbing')


                    <a href="{{ route('report.index') }}" class="flex flex-row gap-3 px-4 py-2 hover:bg-[#DFE1F3] rounded">
                        <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.25 22H15.25C20.25 22 22.25 20 22.25 15V9C22.25 4 20.25 2 15.25 2H9.25C4.25 2 2.25 4 2.25 9V15C2.25 20 4.25 22 9.25 22Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M10.25 17H14.25C15.9 17 17.25 15.65 17.25 14V10C17.25 8.35 15.9 7 14.25 7H10.25C8.6 7 7.25 8.35 7.25 10V14C7.25 15.65 8.6 17 10.25 17Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12.25 7V17" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7.25 12H17.25" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        Reports
                    </a>
                    @endif
                    @endauth
                </li>
                <li class="<?= ($secondValue === 'peserta' || $secondValue === 'pembimbing') ? 'activate pb-2' : '' ?>">
                    <!-- Tombol Dropdown -->


                    <button
                        onclick="toggleDropdown()"
                        class="relative flex flex-row gap-3 px-4 py-2 hover:bg-[#DFE1F3] rounded focus:outline-none focus:ring-2 focus:ring-gray-600 w-full">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.16055 10.87C9.06055 10.86 8.94055 10.86 8.83055 10.87C6.45055 10.79 4.56055 8.84 4.56055 6.44C4.56055 3.99 6.54055 2 9.00055 2C11.4505 2 13.4405 3.99 13.4405 6.44C13.4305 8.84 11.5405 10.79 9.16055 10.87Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16.4093 4C18.3493 4 19.9093 5.57 19.9093 7.5C19.9093 9.39 18.4093 10.93 16.5393 11C16.4593 10.99 16.3693 10.99 16.2793 11" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4.1607 14.56C1.7407 16.18 1.7407 18.82 4.1607 20.43C6.9107 22.27 11.4207 22.27 14.1707 20.43C16.5907 18.81 16.5907 16.17 14.1707 14.56C11.4307 12.73 6.9207 12.73 4.1607 14.56Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18.3398 20C19.0598 19.85 19.7398 19.56 20.2998 19.13C21.8598 17.96 21.8598 16.03 20.2998 14.86C19.7498 14.44 19.0798 14.16 18.3698 14" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        Users
                    </button>

                    <!-- Konten Dropdown -->
                    <div id="dropdownMenu" class="hidden mx-auto my-2 w-48 rounded shadow-lg bg-[#DCE4FF] px-4">
                        <a href="{{ route('pembimbing') }}" class=" block text-[#54577A] py-2 hover:bg-[#DFE1F3] rounded ">
                            Pembimbing
                        </a>
                        <hr class="border-[#54577A] my-1 border-2 ">
                        <a href="{{ route('peserta') }}" class="block text-[#54577A] py-2 hover:bg-[#DFE1F3] rounded">
                            Peserta
                        </a>
                    </div>
                </li>
                <li class="<?= $secondValue === 'profile' ? 'activate' : '' ?>">


                    <a href="{{ route('profile', ['id' => Auth::id()]) }}" class="flex flex-row gap-3 px-4 py-2 hover:bg-[#DFE1F3] rounded">
                        <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.25 21H7.25C3.25 21 2.25 20 2.25 16V8C2.25 4 3.25 3 7.25 3H17.25C21.25 3 22.25 4 22.25 8V16C22.25 20 21.25 21 17.25 21Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14.25 8H19.25" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M15.25 12H19.25" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17.25 16H19.25" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8.74945 11.29C9.74909 11.29 10.5595 10.4797 10.5595 9.48004C10.5595 8.48041 9.74909 7.67004 8.74945 7.67004C7.74982 7.67004 6.93945 8.48041 6.93945 9.48004C6.93945 10.4797 7.74982 11.29 8.74945 11.29Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12.25 16.33C12.11 14.88 10.96 13.74 9.51 13.61C9.01 13.56 8.5 13.56 7.99 13.61C6.54 13.75 5.39 14.88 5.25 16.33" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        Profile</a>
                </li>
            </ul>
        </div>
    </div>
</div>


     <!-- Modal Popup -->
     <div id="create-project-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
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

    <script>
// -------- JS SUPAYA DROPDOWN TETAP TERBUKA KETIKA ACTIVE--------------
    document.addEventListener('DOMContentLoaded', function () {
        const dropdownMenu = document.getElementById('dropdownMenu');
        if (window.location.pathname.includes('pembimbing') || window.location.pathname.includes('peserta')) {
            dropdownMenu.classList.remove('hidden');
        }
    });

         // Fungsi untuk toggle dropdown
    function toggleDropdown() {
        const dropdownMenu = document.getElementById('dropdownMenu');
        dropdownMenu.classList.toggle('hidden');
    }

    // Menutup dropdown jika klik di luar elemen
    document.addEventListener('click', (event) => {
        const dropdownButtonUsers = document.getElementById('dropdownButton');
        const dropdownMenuPeserta = document.getElementById('dropdownMenu');

        if (!dropdownButtonUsers.contains(event.target) && !dropdownMenuPeserta.contains(event.target)) {
            dropdownMenuPeserta.classList.add('hidden');
        }
    });


        // Ambil elemen checkbox dan menu
        const toggleMenu = document.getElementById("user-menu-toggle");
        const userMenu = document.getElementById("user-menu");

        // Menambahkan event listener untuk klik di luar modal
        document.addEventListener("click", function(event) {
            // Cek jika klik terjadi di luar area dropdown
            if (!userMenu.contains(event.target) && event.target !== toggleMenu) {
                // Matikan checkbox jika klik di luar dropdown
                toggleMenu.checked = false;
            }
        });
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
