console.log("comment.js loaded");

const commentInput = document.getElementById("commentInput");
const sendButton = document.getElementById("sendButton");

// Event listener untuk menangani input pada komentar
commentInput.addEventListener("input", () => {
    // Jika ada value pada input, tampilkan tombol
    if (commentInput.value.trim() !== "") {
        sendButton.classList.remove("hidden");
    } else {
        // Jika input kosong, sembunyikan tombol
        sendButton.classList.add("hidden");
    }
});

// Event listener untuk menangani fokus pada input
commentInput.addEventListener("focus", () => {
    // Tampilkan tombol jika input memiliki value
    if (commentInput.value.trim() !== "") {
        sendButton.classList.remove("hidden");
    }
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
