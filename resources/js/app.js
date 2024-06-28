import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();


// MODALS
document.addEventListener("DOMContentLoaded", function () {
    const openModalButtons = document.querySelectorAll("[data-open-modal]");
    openModalButtons.forEach(button => {
        button.addEventListener("click", function () {
            const modalId = this.getAttribute("data-open-modal");
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = "block";
            }
        });
    });

    const closeButtons = document.querySelectorAll(".modal .close");
    closeButtons.forEach(button => {
        button.addEventListener("click", function () {
            const modal = this.closest(".modal");
            modal.style.display = "none";
        });
    });

    window.addEventListener("click", function (event) {
        const modals = document.querySelectorAll(".modal");
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    });
});

