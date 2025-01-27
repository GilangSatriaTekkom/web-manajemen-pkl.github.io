<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS Quill -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <!-- JavaScript Quill -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script defer src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />


    <script defer src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">



    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/popup.js'])
</head>

<body>
    <div id="loading-overlay" class="loading-overlay">
        <div class="spinner"></div>
    </div>


    @php
        $currentUrl = url()->current();
        $loginUrl = url('login');
        $registerUrl = url('register');
    @endphp
    <div id="app" class="{{ request()->is('login', 'register') ? 'bg-color-primary' : '' }} h-screen font-plusjakarta">



        <!-- Modal Popup -->
        <div id="create-project-modal" class="fixed z-20 inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
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
                    <button onclick="showConfirmation(event)" type="button" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Assign Project
                    </button>
                </form>
            </div>
        </div>

        <main class="">
            @yield('content')
        </main>
    </div>

    <script>
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
                const form = event.target.closest('form'); // Cari form yang terkait dengan tombol
                if (form) {
                    form.submit(); // Kirimkan form setelah konfirmasi
                }
            }
        });
    }
    </script>


<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<!-- Load Your Custom Script -->
<script src="path/to/your/new/quill-file.js"></script>



</body>

</html>
