import swal from "sweetalert";
import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", function () {
    if (document.getElementById("alert-message")) {
        swal({
            title: document
                .getElementById("alert-message")
                .getAttribute("data-title"),
            text: document
                .getElementById("alert-message")
                .getAttribute("data-text"),
            icon: document
                .getElementById("alert-message")
                .getAttribute("data-icon"),
            button: document
                .getElementById("alert-message")
                .getAttribute("data-button"),
        });
    }
});
