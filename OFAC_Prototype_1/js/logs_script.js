const dropdownMenu = document.querySelector('.dropdown-menu');
const logs = document.querySelector('.logs');
const dropdownMenuItems = document.querySelectorAll('.dropdown-menu-item');

if (logs.classList.contains('active')) {
    dropdownMenu.style.display = 'block';
}