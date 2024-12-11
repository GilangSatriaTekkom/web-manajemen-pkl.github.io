import "./bootstrap";
import "../css/app.css";
import "./popup.js";
import Quill from "quill";
import "quill/dist/quill.snow.css";
import { comment } from "postcss";

document.addEventListener("DOMContentLoaded", function () {});

document.addEventListener("DOMContentLoaded", function () {
    let currentTaskId = null;
    // Pastikan modal sudah ada
    const taskPopup = document.getElementById("taskPopup");
    const exitButton = document.getElementById("exitButton");

    const commentsContainer = document.getElementById("comments");
    const commentInput = document.getElementById("commentInput");
    // Function untuk menambahkan komentar
    window.addComment = function () {
        const commentText = commentInput.value.trim();
        if (commentText) {
            const commentElement = document.createElement("div");
            commentElement.classList.add("p-2", "border-b", "text-gray-700");
            commentElement.textContent = commentText;
            commentsContainer.appendChild(commentElement);
            commentInput.value = ""; // Kosongkan input
        }
    };

    window.newCard = function () {
        const modal = document.getElementById("modal");
        modal.classList.toggle("hidden"); // Menambah/menghapus kelas hidden
        modal.classList.toggle("flex"); // Menambah/menghapus kelas flex
    };

    if (taskPopup) {
        // Menambahkan event listener untuk keluar dari modal jika klik di luar area konten
        taskPopup.addEventListener("click", function (event) {
            if (event.target === taskPopup) {
                taskPopup.classList.add("hidden");
                history.back(); // Kembali ke halaman sebelumnya
            }
        });
    } else {
        console.error('Modal element with ID "taskPopup" not found');
    }

    // Menambahkan event listener untuk tombol exit
    if (exitButton) {
        exitButton.addEventListener("click", function () {
            taskPopup.classList.add("hidden");
            history.back(); // Kembali ke halaman sebelumnya
        });
    } else {
        console.error("Exit button not found");
    }

    // Fungsi untuk membuka task popup

    const pathname = window.location.pathname;
    const projectId = pathname.split("/")[2];

    window.openTaskPopup = function (taskId) {
        currentTaskId = taskId;

        // Melakukan permintaan AJAX menggunakan axios
        axios
            .get(`/project/${projectId}/tasks/${taskId}/data`)
            .then((response) => {
                const task = response.data;

                // Mengisi data task ke dalam modal
                document.getElementById("popupTitle").innerText = task.title;

                // Memuat Delta dari task description ke dalam Quill editor
                const quill = new Quill("#taskDescription", {
                    theme: "snow",
                    modules: {
                        toolbar: false, // Menghilangkan toolbar
                    },
                    readOnly: true, // Hanya untuk membaca
                });

                // Mengisi konten Quill editor dengan Delta dari task description
                const delta = task.tasksDescription; // Asumsikan ini adalah format Delta
                quill.setContents(delta);

                // Menampilkan modal hanya setelah data berhasil dimuat
                taskPopup.classList.remove("hidden");
                history.pushState(
                    null,
                    "",
                    `/project/${projectId}/tasks/${taskId}/data`
                ); // Update URL ke task yang dibuka

                // Mengambil dan menampilkan komentar untuk task ini
                const comments = task.comments; // Asumsi comments ada di response.data
                // Clear previous comments
                document.getElementById("comments").innerHTML = ""; // Kosongkan komentar yang ada
                // Loop untuk menampilkan komentar
                comments.forEach((comment) => {
                    if (comment.task_id === Number(currentTaskId)) {
                        // Pastikan task_id sesuai
                        const commentDiv = document.createElement("div");
                        commentDiv.classList.add(
                            "bg-gray-200",
                            "p-2",
                            "rounded",
                            "mt-2"
                        );
                        commentDiv.innerText = comment.comment;

                        // Menambahkan komentar ke dalam kolom comments
                        document
                            .getElementById("comments")
                            .appendChild(commentDiv);
                    } else {
                        console.log("Task ID tidak sesuai");
                    }
                });
            })
            .catch(function (error) {
                console.error(error);
                alert("An error occurred while fetching task details.");
            });
    };

    // Fungsi untuk menambahkan komentar
    window.addComment = function () {
        const commentInput = document.getElementById("commentInput");
        const commentText = commentInput.value.trim();

        if (commentText) {
            axios
                .post(
                    `/tasks/${currentTaskId}/comments`,
                    { text: commentText },
                    {
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                            "Content-Type": "application/json",
                        },
                    }
                )
                .then(function (response) {
                    const comment = response.data; // Ambil komentar yang diterima dari respons

                    console.log("Comment received:", comment); // Debugging

                    // Tambahkan komentar baru ke dalam kolom komentar
                    const commentDiv = document.createElement("div");
                    commentDiv.classList.add(
                        "bg-gray-200",
                        "p-2",
                        "rounded",
                        "mt-2"
                    );
                    commentDiv.innerText = comment.comment;

                    document.getElementById("comments").appendChild(commentDiv);

                    // Kosongkan input setelah berhasil
                    commentInput.value = "";
                })
                .catch(function (error) {
                    console.error("Error adding comment:", error);
                    alert("Failed to add comment. Please try again.");
                });
        }
    };

    var quill = new Quill("#description-container", {
        theme: "snow",
        modules: {
            toolbar: [
                [{ list: "ordered" }, { list: "bullet" }],
                ["bold", "italic", "underline"],
                [{ align: [] }],
                ["link", "image"],
            ],
        },
    });

    // Override prompt untuk menambahkan https:// jika tidak ada protokol
    const originalPrompt = Quill.import("ui/prompt");
    Quill.register("ui/prompt", function (defaultValue) {
        const url = originalPrompt(defaultValue);
        if (url && !url.startsWith("http://") && !url.startsWith("https://")) {
            return "https://" + url;
        }
        return url;
    });

    // Update hidden input with Delta on content change
    quill.on("text-change", function () {
        const delta = quill.getContents(); // Get Delta JSON
        document.getElementById("description").value = JSON.stringify(delta);
    });
});
