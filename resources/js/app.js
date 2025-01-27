import "./bootstrap";
import "./taskButton";
import "../css/app.css";
import "./popup";
import "./modalSubmitPictReport";
import "./alert";
import Quill from "quill";
import "quill/dist/quill.snow.css";
import { comment } from "postcss";
import "flowbite";
import swal from "sweetalert";
import Swal from "sweetalert2";

window.globalTaskId = null;

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
                clearAssignedTo();
                taskPopup.classList.add("hidden");
                history.back(); // Kembali ke halaman sebelumnya

                const attachmentsContainer =
                    document.getElementById("attachments");
                // Reset kontainer lampiran
                attachmentsContainer.innerHTML = ""; // Menghapus semua elemen di dalam kontainer
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
            clearAssignedTo();

            const attachmentsContainer = document.getElementById("attachments");
            // Reset kontainer lampiran
            attachmentsContainer.innerHTML = ""; // Menghapus semua elemen di dalam kontainer
        });
    } else {
        console.error("Exit button not found");
    }

    // Fungsi untuk membuka task popup

    const pathname = window.location.pathname;
    const projectId = pathname.split("/")[2];

    window.openTaskPopup = function (taskId) {
        currentTaskId = taskId;
        globalTaskId = taskId;

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

                // Cek apakah task.tasksDescription kosong atau tidak
                if (
                    !task.tasksDescription ||
                    !task.tasksDescription.ops ||
                    task.tasksDescription.ops.length === 0
                ) {
                    // Jika kosong, tampilkan pesan di editor
                    quill.setText("Tidak ada deskripsi pada task ini");
                } else {
                    // Filter hanya `insert` yang berupa teks dan tidak memiliki atribut
                    const delta = task.tasksDescription.ops.filter((op) => {
                        return typeof op.insert === "string" && !op.attributes;
                    });

                    // Membuat Delta baru yang hanya berisi teks
                    const filteredDelta = {
                        ops: delta,
                    };

                    // Mengisi konten Quill editor dengan Delta yang sudah difilter
                    quill.setContents(filteredDelta);
                }

                // Menampilkan modal hanya setelah data berhasil dimuat
                taskPopup.classList.remove("hidden");
                history.pushState(
                    null,
                    "",
                    `/project/${projectId}/tasks/${taskId}/data`
                ); // Update URL ke task yang dibuka

                // Proses data untuk menampilkan attachment
                const attachmentsContainer =
                    document.getElementById("attachments");

                // Cek apakah task.tasksDescription dan ops ada
                if (
                    task &&
                    task.tasksDescription &&
                    task.tasksDescription.ops
                ) {
                    const ops = task.tasksDescription.ops;
                    let imageWrapper = null;

                    ops.forEach((item) => {
                        // Cek jika ada gambar
                        if (item.insert && item.insert.image) {
                            // Jika belum ada wrapper gambar, buat wrapper baru
                            if (!imageWrapper) {
                                imageWrapper = document.createElement("div");
                                imageWrapper.classList.add(
                                    "attachment-card",
                                    "p-4",
                                    "mt-2",
                                    "border",
                                    "rounded-lg",
                                    "shadow-md",
                                    "flex",
                                    "flex-row", // Flex row untuk gambar
                                    "gap-2"
                                );

                                // Tambahkan wrapper ke dalam container utama
                                attachmentsContainer.appendChild(imageWrapper);

                                // Tambahkan judul "Image:" di atas wrapper
                                const title = document.createElement("h5");
                                title.classList.add(
                                    "font-semibold",
                                    "text-gray-800",
                                    "mb-2"
                                );
                                // Tambahkan wrapper ke dalam container utama
                                attachmentsContainer.appendChild(imageWrapper);

                                // Menambahkan title di paling atas dari attachmentsContainer
                                attachmentsContainer.prepend(title); // Gunakan prepend untuk menambahkannya di awal
                            }

                            // Membuat elemen gambar dan menambahkannya ke dalam div yang dapat diklik
                            const imageLinkWrapper =
                                document.createElement("div");
                            imageLinkWrapper.classList.add("gap-2"); // Flex row untuk gambar

                            const imageLink = document.createElement("a");
                            imageLink.setAttribute("target", "_blank"); // Membuka gambar di tab baru
                            imageLink.setAttribute(
                                "onclick",
                                `openModalImageDescription('${item.insert.image}')` // Panggil fungsi openModalImageDescription dengan parameter gambar
                            );

                            const imageElement = document.createElement("img");
                            imageElement.classList.add(
                                "w-12",
                                "h-12",
                                "rounded-sm"
                            );
                            imageElement.src = item.insert.image;
                            imageElement.alt = "Image";

                            imageLink.appendChild(imageElement);
                            imageLinkWrapper.appendChild(imageLink);

                            // Menambahkan link wrapper ke dalam imageWrapper
                            imageWrapper.appendChild(imageLinkWrapper);
                        }

                        // Cek jika ada link
                        if (item.attributes && item.attributes.link) {
                            const linkCard = document.createElement("div");
                            linkCard.classList.add(
                                "attachment-card",
                                "p-4",
                                "mt-2",
                                "border",
                                "rounded-lg",
                                "shadow-md"
                            );
                            linkCard.innerHTML = `
                <h5 class="font-semibold text-gray-800">Link:</h5>
                <a href="${item.attributes.link}" class="text-blue-500 hover:text-blue-700" target="_blank">${item.attributes.link}</a>
            `;
                            attachmentsContainer.appendChild(linkCard);
                        }
                    });

                    // Jika ada gambar, tambahkan wrapper gambar ke container
                    if (imageWrapper) {
                        attachmentsContainer.appendChild(imageWrapper);
                    }
                } else {
                    // Jika ops kosong, tampilkan pesan
                    const noAttachmentsMessage = document.createElement("p");
                    noAttachmentsMessage.classList.add("text-gray-500", "mt-4");
                    noAttachmentsMessage.innerText =
                        "Tidak ada Link atau gambar pada task ini.";
                    attachmentsContainer.appendChild(noAttachmentsMessage);
                }

                // Mengambil dan menampilkan komentar untuk task ini
                const comments = task.comments; // Asumsi comments ada di response.data
                // Clear previous comments
                document.getElementById("comments").innerHTML = ""; // Kosongkan komentar yang ada
                // Loop untuk menampilkan komentar
                // Pastikan loggedInUserId tersedia
                const loggedInUserId = task.loggedInUserId;

                comments.forEach((comment) => {
                    if (comment.task_id === Number(currentTaskId)) {
                        // Elemen utama komentar
                        const commentWrapper = document.createElement("div");

                        // Cek apakah komentar dibuat oleh user yang sedang login
                        const isLoggedInUser =
                            comment.user_id === loggedInUserId;

                        // Tambahkan kelas berdasarkan posisi komentar
                        commentWrapper.classList.add(
                            "flex",
                            "items-start",
                            "gap-2.5",
                            "mt-4",
                            isLoggedInUser ? "justify-end" : "justify-start" // Posisi komentar
                        );

                        // Gambar profil
                        const profileImage = document.createElement("img");
                        profileImage.classList.add(
                            "w-8",
                            "h-8",
                            "rounded-full"
                        );
                        profileImage.src =
                            comment.image_url || "/path/to/default/image.jpg";
                        profileImage.alt = "Profile Picture";

                        // Kontainer isi komentar
                        const commentContent = document.createElement("div");
                        commentContent.classList.add(
                            "flex",
                            "flex-col",
                            "w-full",
                            "max-w-[320px]",
                            "leading-1.5",
                            "p-4",
                            "border-gray-200",
                            "bg-gray-100",
                            "rounded-e-xl",
                            "rounded-es-xl",
                            "dark:bg-gray-700",
                            isLoggedInUser ? "bg-blue-100" : "bg-gray-100" // Warna berbeda untuk komentar user login
                        );

                        // Header komentar
                        const commentHeader = document.createElement("div");
                        commentHeader.classList.add(
                            "flex",
                            "items-center",
                            "space-x-2",
                            "rtl:space-x-reverse"
                        );

                        const commenterName = document.createElement("span");
                        commenterName.classList.add(
                            "text-sm",
                            "font-semibold",
                            "text-gray-900",
                            "dark:text-white"
                        );
                        commenterName.textContent =
                            comment.user.name || "Anonymous";

                        const commentTime = document.createElement("span");
                        commentTime.classList.add(
                            "text-sm",
                            "font-normal",
                            "text-gray-500",
                            "dark:text-gray-400"
                        );
                        commentTime.textContent =
                            comment.time_ago || "Just now";

                        commentHeader.appendChild(commenterName);
                        commentHeader.appendChild(commentTime);

                        // Isi komentar
                        const commentTextElement = document.createElement("p");
                        commentTextElement.classList.add(
                            "text-sm",
                            "font-normal",
                            "py-2.5",
                            "text-gray-900",
                            "dark:text-white"
                        );
                        commentTextElement.textContent = comment.comment;

                        // Menyusun elemen
                        commentContent.appendChild(commentHeader);
                        commentContent.appendChild(commentTextElement);

                        if (!isLoggedInUser) {
                            commentWrapper.appendChild(profileImage); // Profil di kiri
                            commentWrapper.appendChild(commentContent);
                        } else {
                            commentWrapper.appendChild(commentContent); // Profil di kanan
                            commentWrapper.appendChild(profileImage);
                        }

                        // Menambahkan komentar ke dalam elemen komentar
                        document
                            .getElementById("comments")
                            .appendChild(commentWrapper);
                    } else {
                        console.log("Task ID tidak sesuai");
                    }
                });

                const status = task.status;

                const startButton = document.getElementById("startTaskButton");
                // const stopButton = document.getElementById("stopTaskButton");
                const resumeButton =
                    document.getElementById("resumeTaskButton");
                const finishButton =
                    document.getElementById("finishTaskButton");

                if (status === "to_do") {
                    // Jika menekan "Mulai Pekerjaan" atau "Lanjutkan Pekerjaan"
                    startButton.classList.remove("hidden");
                    // stopButton.classList.add("hidden");
                    resumeButton.classList.add("hidden");
                    finishButton.classList.add("hidden");
                } else if (status === "in_progress") {
                    // Jika menekan "Mulai Pekerjaan" atau "Lanjutkan Pekerjaan"
                    startButton.classList.add("hidden");
                    //  stopButton.classList.remove("hidden");
                    resumeButton.classList.add("hidden");
                    finishButton.classList.remove("hidden");

                    // Menampilkan informasi pengguna yang mengerjakan task
                    const assignedTo = document.getElementById("assignedTo");
                    assignedTo.innerHTML = ""; // Clear previous content

                    // Menggunakan data yang dikirimkan dari server
                    const user =
                        task.profile_picture &&
                        task.user_name &&
                        task.profileUrl
                            ? {
                                  name: task.user_name,
                                  profilePicture: task.profile_picture,
                                  profileUrl: task.profileUrl,
                              }
                            : null;

                    if (user) {
                        // Menyusun elemen gambar profil
                        const userProfileImage = document.createElement("img");
                        userProfileImage.src = user.profilePicture; // Menggunakan URL gambar dari user.profilePicture
                        userProfileImage.alt = user.name; // Menambahkan alt dengan nama user
                        userProfileImage.classList.add(
                            "w-8",
                            "h-8",
                            "rounded-full",
                            "mr-2"
                        ); // Menambahkan kelas untuk styling

                        // Menyusun elemen nama pengguna
                        const userName = document.createElement("span");
                        userName.classList.add("text-sm", "text-gray-600");
                        userName.innerText = user.name; // Menambahkan nama pengguna ke dalam elemen

                        // Menambahkan teks "Dikerjakan oleh"
                        const assignedText = document.createElement("span");
                        assignedText.classList.add(
                            "text-sm",
                            "text-gray-600",
                            "mr-2"
                        );
                        assignedText.innerText = "Dikerjakan oleh:"; // Teks "Dikerjakan oleh"

                        // Membungkus gambar profil dan nama pengguna dalam elemen <a> untuk membuatnya menjadi link
                        const userLink = document.createElement("a");
                        userLink.href = user.profileUrl; // Mengarahkan ke halaman profil pengguna
                        userLink.classList.add(
                            "flex",
                            "items-center",
                            "space-x-2",
                            "p-2",
                            "border",
                            "border-gray-300",
                            "rounded",
                            "hover:bg-gray-100"
                        ); // Menambahkan kelas untuk styling seperti button

                        // Menyusun elemen yang akan ditampilkan
                        const userElement = document.createElement("div");
                        userElement.classList.add("flex", "items-center");

                        // Menambahkan teks "Dikerjakan oleh", gambar profil, dan nama pengguna ke dalam link
                        userLink.appendChild(assignedText);
                        userLink.appendChild(userProfileImage);
                        userLink.appendChild(userName);

                        // Menambahkan elemen ke dalam container yang ada di halaman
                        const assignedTo =
                            document.getElementById("assignedTo");
                        assignedTo.appendChild(userLink);
                    } else {
                        assignedTo.innerHTML =
                            "<p>Belum ada yang mengerjakan</p>";
                    }
                } else if (status === "paused") {
                    // Jika menekan "Hentikan Pekerjaan"
                    //    stopButton.classList.add("hidden");
                    resumeButton.classList.remove("hidden");
                } else if (status === "done") {
                    // Jika statusnya "Done", sembunyikan semua tombol
                    startButton.classList.add("hidden");
                    //    stopButton.classList.add("hidden");
                    resumeButton.classList.add("hidden");
                    finishButton.classList.add("hidden");

                    // Menampilkan informasi pengguna yang mengerjakan task
                    const assignedTo = document.getElementById("assignedTo");
                    assignedTo.innerHTML = ""; // Clear previous content

                    const user =
                        task.profile_picture && task.user_name
                            ? {
                                  name: task.user_name,
                                  profilePicture: task.profile_picture,
                              }
                            : null;
                    // Pastikan task.worked_by berisi ID user yang mengerjakan task
                    if (user) {
                        // Menampilkan nama dan foto profil
                        const userElement = document.createElement("div");
                        userElement.classList.add("flex", "items-center");

                        // Foto profil pengguna
                        const userProfileImage = document.createElement("img");
                        userProfileImage.src = user.profilePicture; // Sesuaikan path dengan path foto profil di server
                        userProfileImage.alt = user.name;
                        userProfileImage.classList.add(
                            "w-8",
                            "h-8",
                            "rounded-full",
                            "mr-2"
                        );

                        // Nama pengguna
                        const userName = document.createElement("span");
                        userName.classList.add("text-sm", "text-gray-600");
                        userName.innerText = user.name;

                        // Menambahkan elemen ke dalam container
                        userElement.appendChild(userProfileImage);
                        userElement.appendChild(userName);
                        assignedTo.appendChild(userElement);
                    } else {
                        assignedTo.innerHTML = "<p>Unassigned</p>";
                    }
                }
            })
            .catch(function (error) {
                console.error(error);
                alert("An error occurred while fetching task details.");
            });
    };

    function clearAssignedTo() {
        const assignedTo = document.getElementById("assignedTo");
        assignedTo.innerHTML = ""; // Menghapus semua elemen di dalam assignedTo
    }

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

                    // Elemen utama komentar
                    const commentWrapper = document.createElement("div");
                    commentWrapper.classList.add(
                        "flex",
                        "items-start",
                        "gap-2.5",
                        "mt-4"
                    );

                    // Gambar profil
                    const profileImage = document.createElement("img");
                    profileImage.classList.add("w-8", "h-8", "rounded-full");
                    profileImage.src = comment.image_url;
                    profileImage.alt = "Profile Picture";

                    // Kontainer isi komentar
                    const commentContent = document.createElement("div");
                    commentContent.classList.add(
                        "flex",
                        "flex-col",
                        "w-full",
                        "max-w-[320px]",
                        "leading-1.5",
                        "p-4",
                        "border-gray-200",
                        "bg-gray-100",
                        "rounded-e-xl",
                        "rounded-es-xl",
                        "dark:bg-gray-700"
                    );

                    // Header komentar
                    const commentHeader = document.createElement("div");
                    commentHeader.classList.add(
                        "flex",
                        "items-center",
                        "space-x-2",
                        "rtl:space-x-reverse"
                    );

                    const commenterName = document.createElement("span");
                    commenterName.classList.add(
                        "text-sm",
                        "font-semibold",
                        "text-gray-900",
                        "dark:text-white"
                    );
                    commenterName.textContent = comment.user.name; // Ganti dengan nama pengguna jika tersedia

                    const commentTime = document.createElement("span");
                    commentTime.classList.add(
                        "text-sm",
                        "font-normal",
                        "text-gray-500",
                        "dark:text-gray-400"
                    );
                    commentTime.textContent = comment.time_ago; // Ganti dengan waktu sebenarnya

                    commentHeader.appendChild(commenterName);
                    commentHeader.appendChild(commentTime);

                    // Isi komentar
                    const commentTextElement = document.createElement("p");
                    commentTextElement.classList.add(
                        "text-sm",
                        "font-normal",
                        "py-2.5",
                        "text-gray-900",
                        "dark:text-white"
                    );
                    commentTextElement.textContent = comment.comment;

                    // Status komentar
                    const commentStatus = document.createElement("span");
                    commentStatus.classList.add(
                        "text-sm",
                        "font-normal",
                        "text-gray-500",
                        "dark:text-gray-400"
                    );
                    commentStatus.textContent = "Delivered";

                    // Dropdown menu
                    const dropdownButton = document.createElement("button");
                    dropdownButton.id = "dropdownMenuIconButton";
                    dropdownButton.dataset.dropdownToggle = "dropdownDots";
                    dropdownButton.dataset.dropdownPlacement = "bottom-start";
                    dropdownButton.classList.add(
                        "inline-flex",
                        "self-center",
                        "items-center",
                        "p-2",
                        "text-sm",
                        "font-medium",
                        "text-center",
                        "text-gray-900",
                        "bg-white",
                        "rounded-lg",
                        "hover:bg-gray-100",
                        "focus:ring-4",
                        "focus:outline-none",
                        "dark:text-white",
                        "focus:ring-gray-50",
                        "dark:bg-gray-900",
                        "dark:hover:bg-gray-800",
                        "dark:focus:ring-gray-600"
                    );

                    const dropdownIcon = document.createElement("svg");
                    dropdownIcon.classList.add(
                        "w-4",
                        "h-4",
                        "text-gray-500",
                        "dark:text-gray-400"
                    );
                    dropdownIcon.setAttribute("aria-hidden", "true");
                    dropdownIcon.setAttribute(
                        "xmlns",
                        "http://www.w3.org/2000/svg"
                    );
                    dropdownIcon.setAttribute("fill", "currentColor");
                    dropdownIcon.setAttribute("viewBox", "0 0 4 15");

                    const dropdownPath = document.createElement("path");
                    dropdownPath.setAttribute(
                        "d",
                        "M3.5 1.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 6.041a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 5.959a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"
                    );

                    dropdownIcon.appendChild(dropdownPath);
                    dropdownButton.appendChild(dropdownIcon);

                    // Menyusun elemen
                    commentContent.appendChild(commentHeader);
                    commentContent.appendChild(commentTextElement);
                    commentContent.appendChild(commentStatus);

                    commentWrapper.appendChild(profileImage);
                    commentWrapper.appendChild(commentContent);
                    commentWrapper.appendChild(dropdownButton);

                    // Menambahkan komentar ke dalam elemen komentar
                    document
                        .getElementById("comments")
                        .appendChild(commentWrapper);

                    // Kosongkan input setelah berhasil
                    commentInput.value = "";
                })
                .catch(function (error) {
                    console.error("Error adding comment:", error);
                    alert("Failed to add comment. Please try again.");
                });
        }
    };

    // Override prompt untuk menambahkan https:// jika tidak ada protokol
    const originalPrompt = Quill.import("ui/prompt");
    Quill.register("ui/prompt", function (defaultValue) {
        const url = originalPrompt(defaultValue);
        if (url && !url.startsWith("http://") && !url.startsWith("https://")) {
            return "https://" + url;
        }
        return url;
    });

    // Fungsi untuk menambahkan komentar
});

window.addEventListener("load", function () {
    // Setelah halaman selesai dimuat, sembunyikan elemen loading
    const loadingOverlay = document.getElementById("loading-overlay");
    if (loadingOverlay) {
        loadingOverlay.style.display = "none"; // Menyembunyikan overlay
    }
});
