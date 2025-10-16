document.addEventListener('DOMContentLoaded', function () {
    const adminMenu = document.getElementById('adminMenu');
    const studentMenu = document.getElementById('studentMenu');
    const dropdownMenu = document.querySelector('.dropdown-menu');

    if (adminMenu) {
        adminMenu.addEventListener('click', function () {
            dropdownMenu.classList.toggle('show');
        });
    }

    if (studentMenu) {
        studentMenu.addEventListener('click', function () {
            dropdownMenu.classList.toggle('show');
        });
    }

    // Close the dropdown if the user clicks outside of it
    window.addEventListener('click', function (event) {
        if (dropdownMenu && dropdownMenu.classList.contains('show')) {
            if (adminMenu && !adminMenu.contains(event.target)) {
                dropdownMenu.classList.remove('show');
            }
            if (studentMenu && !studentMenu.contains(event.target)) {
                dropdownMenu.classList.remove('show');
            }
        }
    });
});
