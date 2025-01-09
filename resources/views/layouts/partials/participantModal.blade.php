  <!-- Modal Popup -->
  <div id="participantModal" class="fixed z-20 inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
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
        <div id="addParticipantButton">
            @if(isset($project) && $project->id)
                <button
                    class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                    onclick="openAddParticipantPopup()">
                    Tambah Peserta
                </button>
            @else
                <p class="text-gray-500">Tidak ada proyek yang tersedia.</p>
            @endif
        </div>

        <!-- Popup untuk input nama peserta -->
        <div id="addParticipantModal" class="fixed z-30 inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
            <div class="bg-white p-6 rounded-lg w-96 relative">
                <!-- Tombol X di pojok kanan atas untuk menutup popup -->
                <button onclick="closeAddParticipantPopup()" class="absolute top-2 right-2 text-xl font-bold text-gray-600">&times;</button>

                <h3 class="text-lg font-semibold mb-4">Tambah Peserta</h3>
                <form id="addParticipantForm" onsubmit="addParticipant(event)">
                    <div class="mb-4">
                        <label for="participantName" class="block text-sm font-medium text-gray-700">Nama Peserta</label>
                        <input
                            type="text"
                            id="participantName"
                            name="participantName"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            required
                        >
                    </div>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Tambahkan
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>


<script>
    function openAddParticipantPopup() {
        document.getElementById('addParticipantModal').classList.remove('hidden');
    }

    function closeAddParticipantPopup() {
        document.getElementById('addParticipantModal').classList.add('hidden');
    }

    function addParticipant(event) {
        event.preventDefault();
        const participantName = document.getElementById('participantName').value;
        const participantList = document.getElementById('participantList');

        // Tambahkan nama peserta ke daftar peserta
        const listItem = document.createElement('li');
        listItem.textContent = participantName;
        listItem.className = 'bg-gray-100 p-2 rounded shadow';
        participantList.appendChild(listItem);

        // Bersihkan input dan tutup popup
        document.getElementById('participantName').value = '';
        closeAddParticipantPopup();
    }
</script>
