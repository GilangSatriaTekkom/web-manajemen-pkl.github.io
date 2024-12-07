<!-- resources/views/components/board-column.blade.php -->
<div class="bg-gray-200 p-4 rounded shadow-md">
    <h3 class="font-semibold text-lg mb-3">{{ $title }}</h3>

    <!-- Menampilkan Card -->
    @foreach ($cards as $card)
        @include('components.card', ['title' => $card['title'], 'description' => $card['description']])
    @endforeach

    <!-- Tombol untuk menambahkan kartu -->
    <button class="text-blue-600 text-sm mt-2" onclick="toggleModal()">+ Add another card</button>

    <!-- Modal Form -->
    <div id="modal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg p-6 w-1/3">
            <h2 class="text-xl font-semibold mb-4">Add Card</h2>

            <!-- Form -->
            <form action="{{ route('add.card') }}" method="POST">
                @csrf
                <!-- Input untuk title -->
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700">Card Title</label>
                    <input type="text" id="title" name="title" required
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Card Description</label>
                    <div id="description-container" style="height: 200px;" class="border border-gray-300 rounded-md"></div>
                    <input type="hidden" id="description" name="description">
                </div>

                <div class="flex justify-end">
                    <button type="button" onclick="toggleModal()" class="text-gray-600 hover:text-gray-900 mr-3">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Card</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
    function toggleModal() {
        const modal = document.getElementById('modal');
        modal.classList.toggle('hidden');
    }
</script>

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
    // Inisialisasi Quill editor
    var quill = new Quill('#description-container', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': '1' }, { 'header': '2' }, { 'font': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['bold', 'italic', 'underline'],
                [{ 'align': [] }],
                ['link', 'image', 'video', 'formula'],
            ]
        }
    });

    // Saat form disubmit, set nilai input hidden dengan isi editor
    document.querySelector('form').addEventListener('submit', function () {
        var description = document.querySelector('#description');
        description.value = quill.root.innerHTML;
    });
</script>

