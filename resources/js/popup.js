document.addEventListener("DOMContentLoaded", function () {
    const openModalBtn = document.getElementById("open-modal-btn");
    const closeModalBtn = document.getElementById("close-modal-btn");
    const modal = document.getElementById("create-project-modal");

    if (openModalBtn && closeModalBtn && modal) {
        // event listeners here
    } else {
        console.log("One or more required elements are missing.");
    }

    if (openModalBtn && closeModalBtn && modal) {
        openModalBtn.addEventListener("click", function () {
            modal.classList.remove("hidden");
            modal.classList.add("flex");
        });

        closeModalBtn.addEventListener("click", function () {
            modal.classList.add("hidden");
            modal.classList.remove("flex");
        });

        // Optional: Close modal on background click
        modal.addEventListener("click", function (e) {
            if (e.target === this) {
                this.classList.add("hidden");
                this.classList.remove("flex");
            }
        });
    }
});
