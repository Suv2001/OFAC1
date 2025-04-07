setTimeout(function() {
    const activeItem = document.querySelector('.dropdown-menu.scrollable .dropdown-menu-items[style*="background-color"]');
    if (activeItem) {
        activeItem.parentElement.scrollTop = activeItem.offsetTop - 20;
        console.log("Scrolling to active item:", activeItem.textContent);
    }
}, 100);