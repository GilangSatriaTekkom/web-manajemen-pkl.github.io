export function closeModalImageSubmit() {
    const modal = document.getElementById("modalImageBukti");
    modal.classList.add("hidden");
    modal.classList.remove("flex");
}

export function closeModalOnOutsideClickImageSubmit(event) {
    const modal = document.getElementById("modalImageBukti");
    // Pastikan klik terjadi di luar elemen modal (konten di dalam modal)
    if (event.target === modal) {
        closeModalImageSubmit();
    }
}

window.closeModalImageSubmit = closeModalImageSubmit;

window.closeModalOnOutsideClickImageSubmit =
    closeModalOnOutsideClickImageSubmit;
