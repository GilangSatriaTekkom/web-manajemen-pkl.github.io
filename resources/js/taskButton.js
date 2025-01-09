// Fungsi untuk mengupdate status tugas

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

            const taskId = globalTaskId;
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
            // Jika menekan "Selesai"
            if (confirm("Apakah yakin pekerjaan sudah selesai?")) {
                console.log("Pekerjaan selesai.");
                const taskId = globalTaskId;
                console.log(taskId);
                axios
                    .post("/tasks/update", {
                        task_id: taskId,
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

                // Reset tombol dan timer
                resetTaskButtons();
                clearInterval(timer);
                timer = null;
                elapsedTime = 0;
            } else {
                console.log("Selesai dibatalkan.");
            }
        }
    };
});

// Fungsi untuk mereset tombol
window.resetTaskButtons = function () {
    document.getElementById("startTaskButton").classList.add("hidden");
    // document.getElementById("stopTaskButton").classList.add("hidden");
    document.getElementById("resumeTaskButton").classList.add("hidden");
    document.getElementById("finishTaskButton").classList.add("hidden");
};
