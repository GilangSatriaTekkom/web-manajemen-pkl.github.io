@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-10">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-semibold text-center text-blue-600 mb-4">User Profile</h1>

        <!-- Profile Picture -->
        <form action="{{ route('profile.profileUpload', ['id' => Auth::id()]) }}" method="POST" enctype="multipart/form-data" class="mt-4">
            <div class="text-center flex flex-col items-center">
                <label for="profile_picture" class="block text-gray-700 mb-2">Profile Picture:</label>
                @csrf
                <!-- Profile Picture Image -->
                <img id="profileImage" src="{{ $profilePicture }}" alt="Profile Picture"
                    class="w-24 h-24 rounded-full object-cover border-2 border-blue-500 cursor-pointer"
                    onclick="document.getElementById('inputFile').click()">

                <!-- File Input (Hidden) -->
                <div class="form-group">
                    <input type="file" name="profile_pict" id="inputFile"
                        class="form-control @error('profile_pict') is-invalid @enderror" style="display: none"
                        onchange="previewImage()"
                        @if(Auth::id() !== $user->id) disabled @endif>
                    @error('profile_pict')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirmation Popup -->
                <div id="cancelConfirmation" class="hidden fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center z-50">
                    <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                        <p>Are you sure you want to cancel the image upload?</p>
                        <div class="mt-4">
                            <button onclick="closeConfirmation()" class="btn btn-secondary">No</button>
                            <button onclick="closePopup()" class="btn btn-primary ml-2">Yes</button>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-2">Click the picture to upload a new profile photo</p>
            </div>

            <div class="space-y-4">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-gray-700">Name:</label>
                    <input type="text" id="name" name="name" value="{{ $user->name }}"
                        class="w-full border border-gray-300 rounded-lg py-2 px-4 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                        @if(Auth::id() !== $user->id) disabled @endif>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-gray-700">Email:</label>
                    <input type="email" id="email" name="email" value="{{ $user->email }}"
                        class="w-full border border-gray-300 rounded-lg py-2 px-4 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                        @if(Auth::id() !== $user->id) disabled @endif>
                </div>

                <!-- Password -->
                {{-- <div>
                    <label for="password" class="block text-gray-700">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Enter a new password"
                        class="w-full border border-gray-300 rounded-lg py-2 px-4 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                        @if(Auth::id() !== $user->id) disabled @endif>
                </div> --}}

                <!-- Roles -->
                <div>
                    <label for="roles" class="block text-gray-700">Roles:</label>
                    <select id="roles" name="roles"
                        class="w-full border border-gray-300 rounded-lg py-2 px-4 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                        @if(Auth::id() !== $user->id) disabled @endif>
                        <option value="admin" {{ $user->roles == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="peserta" {{ $user->roles == 'peserta' ? 'selected' : '' }}>Peserta</option>
                        <option value="pembimbing" {{ $user->roles == 'pembimbing' ? 'selected' : '' }}>Pembimbing</option>
                    </select>
                </div>

                <!-- Asal Sekolah -->
                <div>
                    <label for="asal_sekolah" class="block text-gray-700">Asal Sekolah:</label>
                    <input type="text" id="asal_sekolah" name="asal_sekolah" value="{{ $user->asal_sekolah }}"
                        class="w-full border border-gray-300 rounded-lg py-2 px-4 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                        @if(Auth::id() !== $user->id) disabled @endif>
                </div>
            </div>

            <div class="flex justify-end items-center mt-6">
                <button id="confirmEditButton" type="submit" class="hidden flex items-center bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                    Confirm Edit
                </button>
            </div>
        </form>

    </div>
</div>

<script>



document.addEventListener('DOMContentLoaded', () => {
    const confirmEditButton = document.getElementById('confirmEditButton');

    // Ambil semua elemen input berdasarkan ID
    const inputs = [
        document.getElementById('name'),
        document.getElementById('email'),
        document.getElementById('password'),
        document.getElementById('roles'),
        document.getElementById('asal_sekolah'),
        document.getElementById('inputFile')
    ];

    // Fungsi untuk mengecek perubahan dan menampilkan tombol
    function checkChanges() {
        let hasChanged = false;

        // Cek setiap input apakah ada perubahan
        inputs.forEach(input => {
                hasChanged = true;
        });

        // Menampilkan atau menyembunyikan tombol Confirm Edit
        if (hasChanged) {
            confirmEditButton.classList.remove('hidden'); // Tampilkan tombol
        } else {
            confirmEditButton.classList.add('hidden'); // Sembunyikan tombol
        }
    }

    // Menambahkan event listener untuk setiap input
    inputs.forEach(input => {
        input.addEventListener("input", checkChanges); // Cek perubahan setiap kali input berubah
    });
});


    // Preview Image when file is selected
    function previewImage() {
        const file = document.getElementById('inputFile').files[0];
        const reader = new FileReader();

        reader.onload = function (e) {
            console.log("File successfully read:", e.target.result); // Debugging line
            // Set the preview image source to the selected file
            document.getElementById('profileImage').src = e.target.result;
            // Show the action buttons (Save and Cancel)
            document.getElementById('actionButtons').classList.remove('hidden');
        }

        if (file) {
            reader.readAsDataURL(file);
        }
    }

    // Show confirmation popup when cancel button is clicked
    function cancelImage() {
        document.getElementById('cancelConfirmation').classList.remove('hidden');
    }

    // Close the confirmation popup without canceling
    function closeConfirmation() {
        document.getElementById('cancelConfirmation').classList.add('hidden');
    }

    // Close the popup and reset the form
    function closePopup() {
        document.getElementById('cancelConfirmation').classList.add('hidden');
        // Reset the file input and image preview
        document.getElementById('inputFile').value = '';
        document.getElementById('profileImage').src = '{{ $profilePicture }}';
        document.getElementById('actionButtons').classList.add('hidden');
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Event listener untuk tombol "Kirim"


</script>
@endsection
