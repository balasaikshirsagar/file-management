$(document).ready(function () {
    // Pagination and search state
    let currentPage = 1;
    let recordsPerPage = 10;
    let searchQuery = '';
    let allEmployees = [];

    // Initial fetch of employees
    fetchEmployees();

    // Function to fetch all employees
    function fetchEmployees() {
        $.ajax({
            url: './api/get_employees.php',
            method: 'GET',
            success: function (response) {
                allEmployees = JSON.parse(response);
                updateEmployeeTable();
            },
            error: function () {
                alert('Failed to fetch employees.');
            }
        });
    }

    // Function to filter employees based on search query
    function filterEmployees() {
        if (!searchQuery) return allEmployees;
        
        return allEmployees.filter(employee => 
            employee.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
            employee.designation.toLowerCase().includes(searchQuery.toLowerCase()) ||
            employee.email.toLowerCase().includes(searchQuery.toLowerCase()) ||
            employee.mobile.includes(searchQuery)
        );
    }

    // Function to update the employee table with pagination
    function updateEmployeeTable() {
        const filteredEmployees = filterEmployees();
        const totalRecords = filteredEmployees.length;
        const totalPages = Math.ceil(totalRecords / recordsPerPage);
        
        // Update total records display
        const startRecord = ((currentPage - 1) * recordsPerPage) + 1;
        const endRecord = Math.min(startRecord + recordsPerPage - 1, totalRecords);
        $('#totalRecords').text(
            totalRecords > 0 
                ? `Showing ${startRecord} to ${endRecord} of ${totalRecords} records` 
                : 'No records found'
        );

        // Calculate start and end index for current page
        const startIndex = (currentPage - 1) * recordsPerPage;
        const endIndex = Math.min(startIndex + recordsPerPage, totalRecords);
        
        // Generate table HTML
        let html = '';
        for (let i = startIndex; i < endIndex; i++) {
            const employee = filteredEmployees[i];
            html += `
                <tr>
                    <td>${employee.id}</td>
                    <td>${employee.name}</td>
                    <td>${employee.designation}</td>
                    <td>${employee.dob}</td>
                    <td>${employee.mobile}</td>
                    <td>${employee.email}</td>
                    <td>
                        <button class="btn btn-primary btn-sm view-files" data-id="${employee.id}">View Files</button>
                        <button class="btn btn-danger btn-sm delete-employee" data-id="${employee.id}">Delete</button>
                    </td>
                </tr>
            `;
        }
        $('#employeeTable').html(html || '<tr><td colspan="7" class="text-center">No records found</td></tr>');

        // Update pagination
        updatePagination(totalPages);
    }

    // Function to update pagination controls
    function updatePagination(totalPages) {
        let paginationHtml = '';
        
        // Only show pagination if there are pages
        if (totalPages > 0) {
            paginationHtml = `
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="prev">Previous</a>
                </li>
            `;

            // Show ellipsis for many pages
            if (totalPages <= 7) {
                for (let i = 1; i <= totalPages; i++) {
                    paginationHtml += `
                        <li class="page-item ${currentPage === i ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>
                    `;
                }
            } else {
                // Complex pagination with ellipsis
                if (currentPage <= 3) {
                    for (let i = 1; i <= 5; i++) {
                        paginationHtml += `
                            <li class="page-item ${currentPage === i ? 'active' : ''}">
                                <a class="page-link" href="#" data-page="${i}">${i}</a>
                            </li>
                        `;
                    }
                    paginationHtml += `
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
                        </li>
                    `;
                } else if (currentPage >= totalPages - 2) {
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="1">1</a>
                        </li>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    `;
                    for (let i = totalPages - 4; i <= totalPages; i++) {
                        paginationHtml += `
                            <li class="page-item ${currentPage === i ? 'active' : ''}">
                                <a class="page-link" href="#" data-page="${i}">${i}</a>
                            </li>
                        `;
                    }
                } else {
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="1">1</a>
                        </li>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    `;
                    for (let i = currentPage - 1; i <= currentPage + 1; i++) {
                        paginationHtml += `
                            <li class="page-item ${currentPage === i ? 'active' : ''}">
                                <a class="page-link" href="#" data-page="${i}">${i}</a>
                            </li>
                        `;
                    }
                    paginationHtml += `
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
                        </li>
                    `;
                }
            }

            paginationHtml += `
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="next">Next</a>
                </li>
            `;
        }

        $('#pagination').html(paginationHtml);
    }

    // Event Handlers
    
    // Search input
    $('#searchButton').click(function() {
        searchQuery = $('#searchInput').val().trim();
        currentPage = 1; // Reset to first page on new search
        updateEmployeeTable();
    });

    $('#searchInput').on('keyup', function(e) {
        if (e.key === 'Enter') {
            searchQuery = $(this).val().trim();
            currentPage = 1;
            updateEmployeeTable();
        }
    });

    // Pagination clicks
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        
        if (page === 'prev' && currentPage > 1) {
            currentPage--;
        } else if (page === 'next' && currentPage < Math.ceil(filterEmployees().length / recordsPerPage)) {
            currentPage++;
        } else if (typeof page === 'number') {
            currentPage = page;
        }
        
        updateEmployeeTable();
    });

    // Records per page change
    $('#recordsPerPage').change(function() {
        recordsPerPage = parseInt($(this).val());
        currentPage = 1; // Reset to first page when changing records per page
        updateEmployeeTable();
    });

    // Add Employee Form Submit
    $('#addEmployeeForm').submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: './api/create_employees.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                alert(response);
                $('#addEmployeeModal').modal('hide');
                $('#addEmployeeForm')[0].reset();
                fetchEmployees(); // Refresh the employee list
            },
            error: function () {
                alert('Failed to add employee.');
            }
        });
    });

    // View Files
    $(document).on('click', '.view-files', function () {
        const employeeId = $(this).data('id');
        $.ajax({
            url: './api/get_employee_files.php',
            method: 'GET',
            data: { employee_id: employeeId },
            success: function (response) {
                const files = JSON.parse(response);
                let html = '';
                if (files.length > 0) {
                    files.forEach((file) => {
                        html += `
                            <li class="list-group-item">
                                <a href="./uploads/${file.filename}" target="_blank">${file.filename}</a>
                            </li>
                        `;
                    });
                } else {
                    html = '<li class="list-group-item">No files uploaded.</li>';
                }
                $('#fileList').html(html);
                $('#viewFilesModal').modal('show');
            },
            error: function () {
                alert('Failed to fetch files.');
            }
        });
    });

    // Delete Employee
    $(document).on('click', '.delete-employee', function () {
        if (!confirm('Are you sure you want to delete this employee?')) return;
        
        const employeeId = $(this).data('id');
        $.ajax({
            url: './api/delete_employee.php',
            method: 'POST',
            data: { id: employeeId },
            success: function (response) {
                alert(response);
                fetchEmployees(); // Refresh the employee list
            },
            error: function () {
                alert('Failed to delete employee.');
            }
        });
    });

    // Clear search
    $('#searchInput').on('search', function() {
        if ($(this).val() === '') {
            searchQuery = '';
            currentPage = 1;
            updateEmployeeTable();
        }
    });
});