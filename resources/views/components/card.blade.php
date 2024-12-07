<!-- resources/views/components/card.blade.php -->
<div class="bg-white p-3 rounded shadow mb-3 cursor-pointer" onclick="openTaskPopup('{{ $title }}', '{{ $description }}')">
    <h4 class="font-semibold">{{ $title }}</h4>
    <p class="text-sm text-gray-700">{{ $description }}</p>
</div>
