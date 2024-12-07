import "./bootstrap";
import "../css/app.css";
import "./popup.js";

import Quill from "quill";
import "quill/dist/quill.snow.css";

const taskPopup = document.getElementById("taskPopup");
const popupTitle = document.getElementById("popupTitle");
const popupDescription = document.getElementById("popupDescription");
const commentsContainer = document.getElementById("comments");
const commentInput = document.getElementById("commentInput");

// Function untuk membuka popup
function openTaskPopup(title, description) {
    popupTitle.textContent = title;
    popupDescription.textContent = description;
    commentsContainer.innerHTML = ""; // Bersihkan komentar sebelumnya
    taskPopup.classList.remove("hidden");
}

// Function untuk menutup popup
taskPopup.addEventListener("click", (e) => {
    if (e.target === taskPopup) {
        taskPopup.classList.add("hidden");
    }
});

// Function untuk menambahkan komentar
function addComment() {
    const commentText = commentInput.value.trim();
    if (commentText) {
        const commentElement = document.createElement("div");
        commentElement.classList.add("p-2", "border-b", "text-gray-700");
        commentElement.textContent = commentText;
        commentsContainer.appendChild(commentElement);
        commentInput.value = ""; // Kosongkan input
    }
}

window.openTaskPopup = openTaskPopup; // Ekspos fungsi ke global scope
window.addComment = addComment; // Ekspos fungsi ke global scope
console.log("openTaskPopup is:", window.openTaskPopup); // Debugging
console.log("addComment is:", window.addComment); // Debugging

document.addEventListener("DOMContentLoaded", function () {
    var quill = new Quill("#editor", {
        theme: "snow",
    });

    // Sinkronisasi isi editor Quill dengan input hidden
    var descriptionInput = document.getElementById("project-description");
    quill.on("text-change", function () {
        descriptionInput.value = quill.root.innerHTML;
    });
});
