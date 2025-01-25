// Fungsi untuk mengupdate status tugas

import Swal from "sweetalert2";

let timer;
let elapsedTime = 0; // Waktu yang sudah berjalan

document.addEventListener("DOMContentLoaded", function () {
    window.updateTaskStatus = function (status, boardId) {
        const startButton = document.getElementById("startTaskButton");
        // const stopButton = document.getElementById("stopTaskButton");
        const resumeButton = document.getElementById("resumeTaskButton");
        const finishButton = document.getElementById("finishTaskButton");

        if (status === "in_progress") {
            // Jika menekan "Mulai Pekerjaan" atau "Lanjutkan Pekerjaan"
            startButton.classList.add("hidden");
            // stopButton.classList.remove("hidden");
            resumeButton.classList.add("hidden");
            finishButton.classList.remove("hidden");

            const pathname = window.location.pathname;
            const taskId = pathname.split("/")[4];
            console.log(taskId);
            axios
                .post("/tasks/update", {
                    task_id: taskId,
                    board_id: boardId,
                })
                .then((response) => {
                    console.log(
                        "Board ID updated successfully:",
                        response.data
                    );
                    // Tambahkan logic untuk memperbarui tampilan
                    return response.data;
                })
                .then((data) => {
                    console.log("Mulai proses pembaruan task...", data);

                    // Cari elemen dengan ID yang sesuai (task)
                    const targetElement = document.getElementById(
                        `task-${data.task.id}`
                    );

                    // Cari elemen board-column yang baru berdasarkan ID
                    const newBoardColumn = document.getElementById(
                        `board-column-${data.task.new_board_id}`
                    );

                    if (newBoardColumn) {
                        // Jika board-column ditemukan, pindahkan task ke board baru
                        newBoardColumn.appendChild(targetElement);
                        console.log(
                            `Task dengan ID ${data.id} dipindahkan ke board-column-${data.new_board_id}.`
                        );
                    } else {
                        // Jika board-column tidak ditemukan
                        console.error(
                            `Tidak ada elemen untuk board-column-${data.new_board_id}.`
                        );
                        // Kirim log kesalahan ke Laravel
                        fetch("/log-js-error", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                            },
                            body: JSON.stringify({
                                message: `Tidak ada elemen untuk board-column-${data.new_board_id}.`,
                                context: { data },
                            }),
                        })
                            .then(() => {
                                console.log(
                                    `Log kesalahan dikirim ke server: Tidak ada elemen untuk board-column-${data.new_board_id}.`
                                );
                            })
                            .catch((error) => {
                                console.error(
                                    "Gagal mengirim log ke Laravel:",
                                    error
                                );
                            });
                    }
                })
                .catch((error) => {
                    console.error("Error updating Board ID:", error);
                });

            // Timer mulai berjalan
            if (!timer) {
                timer = setInterval(() => {
                    elapsedTime++;
                    console.log(`Waktu berjalan: ${elapsedTime} detik`);
                }, 1000);
            }
        } else if (status === "paused") {
            // Jika menekan "Hentikan Pekerjaan"
            // stopButton.classList.add("hidden");
            resumeButton.classList.remove("hidden");

            // Timer berhenti
            if (timer) {
                clearInterval(timer);
                timer = null;
                console.log("Pekerjaan dihentikan sementara.");
            }
        } else if (status === "done") {
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Tugas ini akan ditandai sebagai selesai!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, selesaikan!",
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    const modal = document.getElementById("modalImageBukti");
                    modal.classList.toggle("hidden");
                    modal.classList.toggle("flex");
                } else {
                    console.log("Selesai dibatalkan.");
                }
            });
        }
    };

    window.handleImageSubmit = function (boardId) {
        boardId = 2;
        if (!boardId) {
            console.error("Board ID tidak terambil dengan benar.");
            return;
        }

        const formData = new FormData();
        const images = document.querySelectorAll(
            'input[name="imagesReportSubmit[]"]'
        );

        images.forEach((input) => {
            const files = input.files;
            if (files.length > 0) {
                formData.append("imagesReportSubmit[]", files[0]);
            }
        });

        console.log("FormData yang terkumpul:", [...formData.entries()]);

        const pathname = window.location.pathname;
        const taskId = pathname.split("/")[4];
        formData.append("task_id", taskId);
        formData.append("board_id", boardId);

        console.log("Task ID:", taskId);
        axios
            .post("/tasks/update", formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            })
            .then((response) => {
                console.log("Board ID updated successfully:", response.data);
                return response.data;
            })
            .then((data) => {
                console.log("Mulai proses pembaruan task...", data);

                const targetElement = document.getElementById(
                    `task-${data.task.id}`
                );

                const newBoardColumn = document.getElementById(
                    `board-column-${data.task.new_board_id}`
                );

                if (newBoardColumn) {
                    newBoardColumn.appendChild(targetElement);
                    console.log(
                        `Task dengan ID ${data.id} dipindahkan ke board-column-${data.new_board_id}.`
                    );
                } else {
                    console.error(
                        `Tidak ada elemen untuk board-column-${data.new_board_id}.`
                    );
                    fetch("/log-js-error", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({
                            message: `Tidak ada elemen untuk board-column-${data.new_board_id}.`,
                            context: { data },
                        }),
                    })
                        .then(() => {
                            console.log(
                                `Log kesalahan dikirim ke server: Tidak ada elemen untuk board-column-${data.new_board_id}.`
                            );
                        })
                        .catch((error) => {
                            console.error(
                                "Gagal mengirim log ke Laravel:",
                                error
                            );
                        });
                }
            })
            .catch((error) => {
                console.error("Error updating Board ID:", error);
            });

        resetTaskButtons();
        clearInterval(timer);
        timer = null;
        elapsedTime = 0;
    };
});

// Fungsi untuk mereset tombol
window.resetTaskButtons = function () {
    document.getElementById("startTaskButton").classList.add("hidden");
    // document.getElementById("stopTaskButton").classList.add("hidden");
    document.getElementById("resumeTaskButton").classList.add("hidden");
    document.getElementById("finishTaskButton").classList.add("hidden");
};

window.deleteTask = function () {
    Swal.fire({
        title: "Apakah Anda yakin ingin menghapus tugas ini?",
        text: "Tugas yang dihapus tidak dapat dikembalikan!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            const pathname = window.location.pathname;
            const taskId = pathname.split("/")[4];
            axios
                .delete(`/tasks/${taskId}/destroy`)
                .then((response) => {
                    Swal.fire(
                        "Tugas berhasil dihapus!",
                        "Tugas telah dihapus.",
                        "success"
                    );
                    location.reload();
                })
                .catch((error) => {
                    Swal.fire(
                        "Gagal menghapus tugas!",
                        "Terjadi kesalahan saat menghapus tugas.",
                        "error"
                    );
                });
        }
    });
};
