document.addEventListener("DOMContentLoaded", function() {
    const roleSelect = document.getElementById("role");
    const kelasInput = document.getElementById("kelasInput");

    function toggleKelasInput() {
        const role = roleSelect.value;

        if (role === "siswa") {
            kelasInput.style.display = "block";
        } else {
            kelasInput.style.display = "none";
        }
    }

    toggleKelasInput();

    roleSelect.addEventListener("change", toggleKelasInput);
});


function openModal(modalId) {
    document.getElementById(modalId).style.display = "flex";
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = "none";
}

window.onclick = function(event) {
    const modals = document.getElementsByClassName('modal-overlay');
    for (let i = 0; i < modals.length; i++) {
        if (event.target === modals[i]) {
            modals[i].style.display = "none";
        }
    }
}