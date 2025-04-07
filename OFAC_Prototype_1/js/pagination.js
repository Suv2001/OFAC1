$(document).ready(function() {
    // Load initial data (optional)
    let currentPage = 1;
    const perPage = 20;
    loadTable();

    // Bind input change event to each search field
    $('#searchForm input, #searchForm select').on('keyup change', function() {
        let currentPage = 1;
        loadTable(currentPage);
    });

    // Clear filters button
    $('#clear-filters').click(function() {
        $('#searchForm')[0].reset();
        let currentPage = 1;
        loadTable(currentPage);
    });

    // Function to load the table data based on search filters
    function loadTable(page) {
        const formData = $('#searchForm').serializeArray(); // Serialize form data
        formData.push({
            name: 'page',
            value: page
        }); // Add page number to the form data
        formData.push({
            name: 'perPage',
            value: perPage
        }); // Add items per page to the form data

        $.ajax({
            url: 'view_master_list.php', // Make sure to use the correct AJAX endpoint
            method: 'POST',
            data: $.param(formData),
            success: function(response) {
                $('#tableBody').html(response); // Update the table with the new data
                updatePaginationControls();
            },
            error: function() {
                alert('Error loading data');
            }
        });
    }

    function updatePaginationControls() {
        const totalPages = Math.ceil(totalRecords / perPage); // Calculate total pages
        let paginationHtml = `<ul class="pagination">`; // Start the pagination list

        // Display left arrow
        paginationHtml += `
<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
    <a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
    </a>
</li>
`;

        // Display first page
        paginationHtml += `
<li class="page-item ${currentPage === 1 ? 'active' : ''}">
    <a class="page-link" href="#" data-page="1">1</a>
</li>
`;

        // Display current page if not at the start or end, otherwise skip it
        if (currentPage > 2) {
            paginationHtml += `
    <li class="page-item">
        <span class="page-link">...</span>
    </li>
`;
        }

        // Display the current page
        if (currentPage > 1 && currentPage < totalPages) {
            paginationHtml += `
<li class="page-item active">
    <a class="page-link" href="#" data-page="${currentPage}">${currentPage}</a>
</li>
`;
        }

        // Display last page if not the current page
        if (currentPage < totalPages - 1) {
            paginationHtml += `
    <li class="page-item">
        <span class="page-link">...</span>
    </li>
`;
        }

        // Display last page
        paginationHtml += `
<li class="page-item ${currentPage === totalPages ? 'active' : ''}">
    <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
</li>
`;

        // Display right arrow
        paginationHtml += `
<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
    <a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
    </a>
</li>
`;

        paginationHtml += `</ul>`; // Close the pagination list
        $('#paginationControls').html(paginationHtml); // Update the pagination controls
    }


    // Handle pagination link clicks
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        currentPage = $(this).data('page'); // Get the clicked page number
        loadTable(currentPage); // Load the corresponding page
    });
});

// Prevent form submission on export button click
document.getElementById("export").addEventListener("click", function(event) {
    event.preventDefault(); // Prevents form submission
});