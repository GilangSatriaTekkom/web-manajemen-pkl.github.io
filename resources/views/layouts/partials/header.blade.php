    <header class=" p-4 text-black flex items-center justify-between {{ request()->is('login', 'register') ? 'hidden' : '' }}">
        <h1 class="text-2xl font-bold">
            {{
                request()->segment(1) === 'dashboard'
                    ? 'Project'
                    : (in_array(request()->segment(1), ['pembimbing', 'peserta'])
                        ? 'Users/' . ucfirst(request()->segment(1))
                        : ucfirst(request()->segment(1))
                      )
            }}
        </h1>

        <div class="flex items-center space-x-4">

            @auth
                {{-- @if (Auth::user()->roles === 'admin')
                    <!-- Menampilkan tautan hanya untuk admin -->
                    <a href="{{ route('report.index') }}"
                        class="px-4 py-2 bg-blue-700 text-white rounded hover:bg-blue-800">
                        Report
                    </a>
                @endif
                @if (Auth::user()->roles === 'admin' || Auth::user()->roles === 'pembimbing')
                    <!-- Tombol +Create dengan Checkbox -->
                    <button id="open-modal-btn" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        +Create
                    </button>
                @endif --}}

                <!-- Dropdown User -->
                <div class="relative">
                    <input type="checkbox" id="user-menu-toggle" class="hidden peer" />
                    <label for="user-menu-toggle" class="flex items-center cursor-pointer">
                        <img class="w-14 h-14 rounded-full" src="{{ Auth::user()->profile_pict ? asset('storage/' . Auth::user()->profile_pict) : asset('images/default-profile.png') }}" alt="">
                    </label>

                    <div
                        class="absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded shadow-lg hidden peer-checked:block"
                        id="user-menu">
                        <a href="{{ route('profile', ['id' => Auth::id()]) }}" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Logout</button>
                        </form>
                    </div>
                </div>

            @else
                <a href="{{ route('login') }}" class="bg-blue-700 px-3 py-1 rounded text-sm hover:bg-blue-800">Login</a>
            @endauth
        </div>
    </header>

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
            // Ambil elemen checkbox dan menu
            const toggleMenuProfile = document.getElementById("user-menu-toggle");
            const userMenu = document.getElementById("user-menu");

            // Menambahkan event listener untuk klik di luar modal
            document.addEventListener("click", function(event) {
                // Cek jika klik terjadi di luar area dropdown
                if (!userMenu.contains(event.target) && event.target !== toggleMenuProfile) {
                    // Matikan checkbox jika klik di luar dropdown
                    toggleMenuProfile.checked = false;
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
