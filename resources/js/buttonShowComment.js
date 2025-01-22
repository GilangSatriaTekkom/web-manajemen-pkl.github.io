// Ambil elemen input dan tombol

console.log("comment.js loaded");

const commentInput = document.getElementById("commentInput");
const sendButton = document.getElementById("sendButton");

// Event listener untuk menangani klik pada input
commentInput.addEventListener("focus", () => {
    sendButton.classList.remove("hidden"); // Tampilkan tombol ketika input di-klik
});

// Event listener untuk menangani kehilangan fokus (blur) pada input
commentInput.addEventListener("blur", () => {
    // Tunggu sebentar sebelum menyembunyikan tombol, agar pengguna tidak langsung kehilangan tombol
    setTimeout(() => {
        if (!commentInput.value) {
            // Jika input kosong, sembunyikan tombol
            sendButton.classList.add("hidden");
        }
    }, 200); // 200ms delay untuk memastikan pengguna selesai mengetik
});
