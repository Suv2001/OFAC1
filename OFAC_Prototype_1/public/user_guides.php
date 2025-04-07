<?php
include "../templates/session_management.php";
include("../templates/user_auth.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Guides - OFAC System</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Roboto', sans-serif;
            padding-top: 20px;
            padding-bottom: 40px;
        }

        .container {
            max-width: 1000px;
        }

        .nav-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            padding: 15px;
        }

        .section-btn {
            padding: 10px 20px;
            border: none;
            background-color: #e9ecef;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
            margin: 0 5px;
            font-weight: 500;
        }

        .section-btn.active,
        .section-btn:hover {
            background-color: #007bff;
            color: white;
        }

        .content-section {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 20px;
            display: none;
        }

        .content-section.active {
            display: block;
        }

        .content-section h3 {
            color: #343a40;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
        }

        .guide-img {
            max-width: 100%;
            height: auto;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            margin: 15px 0;
        }

        .download-btn {
            background-color: #28a745;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        .download-btn:hover {
            background-color: #218838;
            color: white;
            text-decoration: none;
        }

        .back-btn {
            margin-top: 20px;
            display: inline-block;
        }

        .template-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .template-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .template-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        code {
            background-color: #f8f9fa;
            padding: 2px 5px;
            border-radius: 3px;
            color: #e83e8c;
        }

        .requirements-list li {
            margin-bottom: 10px;
        }

        .api-endpoint {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 0 5px 5px 0;
        }

        .api-method {
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 4px;
            margin-right: 10px;
        }

        .get {
            background-color: #28a745;
            color: white;
        }

        .post {
            background-color: #007bff;
            color: white;
        }

        .put {
            background-color: #fd7e14;
            color: white;
        }

        .delete {
            background-color: #dc3545;
            color: white;
        }

        .circular-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .circular-btn:hover {
            transform: scale(1.1);
            background-color: #17a2b8;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="help_admin.php#contact-section" class="btn btn-outline-info mr-3 mt-2 mb-2 circular-btn">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="nav-container">
            <div class="d-flex justify-content-center">
                <button class="section-btn" data-section="admin-manual">Admin Manual</button>
                <button class="section-btn" data-section="quick-start">Quick Start Guide</button>
                <button class="section-btn" data-section="master-list">Master List Template</button>
                <button class="section-btn" data-section="business-details">Business Details Template</button>
                <!-- <button class="section-btn" data-section="api-documentation">API Documentation</button> -->
                <button class="section-btn" data-section="system-requirements">System Requirements</button>
            </div>
        </div>

        <!-- Admin Manual Section -->
        <div id="admin-manual-section" class="content-section active">
            <h3><strong>Administrator Manual</strong></h3>

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> This comprehensive guide covers all aspects of administering the OFAC system.
            </div>

            <h4>1. System Overview</h4>
            <p>The OFAC Compliance System is designed to help organizations verify businesses against the Office of Foreign Assets Control (OFAC) sanctions list. This manual provides detailed instructions for system administrators.</p>

            <h4>2. Administrator Dashboard</h4>
            <p>The dashboard provides a quick overview of system status and recent activities:</p>
            <img src="../assets/images/dashboard_admin.png" alt="Admin Dashboard" class="guide-img">
            <ul>
                <li>View system statistics</li>
                <li>Monitor recent activities</li>
                <li>Access quick links to common tasks</li>
                <li>View pending verifications</li>
            </ul>

            <h4>3. User Management</h4>
            <p>Administrators can manage system users through the Users section:</p>
            <img src="../assets/images/users_admin.png" alt="User Management" class="guide-img">
            <ul>
                <li>Add new users</li>
                <li>Edit user details</li>
                <li>Activate/deactivate accounts</li>
                <li>Reset passwords</li>
            </ul>

            <h4>4. Master List Management</h4>
            <p>The Master List contains all sanctioned entities:</p>
            <img src="../assets/images/master_list.png" alt="Master List" class="guide-img">
            <ul>
                <li>Upload new master list</li>
                <li>View current entries</li>
                <li>Export list in various formats</li>
                <li>Track list update history</li>
            </ul>

            <h4>5. Verification Process</h4>
            <p>Handle business verification requests:</p>
            <img src="../assets/images/pending_checks.png" alt="Verification Process" class="guide-img">
            <ul>
                <li>Review pending verification requests</li>
                <li>Compare business details against master list</li>
                <li>Approve or reject verification requests</li>
                <li>Add notes to verification records</li>
            </ul>

            <h4>6. System Logs</h4>
            <p>Monitor system activities through logs:</p>
            <img src="../assets/images/logs.png" alt="System Logs" class="guide-img">
            <ul>
                <li>Track user activities</li>
                <li>Monitor file uploads</li>
                <li>Review verification history</li>
                <li>Export logs for compliance reporting</li>
            </ul>
        </div>

        <!-- Quick Start Guide Section -->
        <div id="quick-start-section" class="content-section">
            <h3><strong>Quick Start Guide</strong></h3>

            <div class="alert alert-success">
                <i class="fas fa-bolt"></i> Get started quickly with these essential steps.
            </div>

            <h4>1. First Login</h4>
            <p>After receiving your credentials:</p>
            <ol>
                <li>Navigate to the login page</li>
                <li>Enter your username and temporary password</li>
                <li>You'll be prompted to change your password</li>
                <li>Create a strong, unique password</li>
            </ol>

            <h4>2. System Navigation</h4>
            <p>The main navigation menu includes:</p>
            <ul>
                <li><strong>Dashboard</strong> - System overview and quick stats</li>
                <li><strong>Users</strong> - User management</li>
                <li><strong>Master List</strong> - Sanctions database</li>
                <li><strong>Pending Checks</strong> - Verification queue</li>
                <li><strong>Logs</strong> - System activity records</li>
                <li><strong>Help</strong> - Documentation and support</li>
            </ul>

            <h4>3. Essential Tasks</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5><i class="fas fa-upload"></i> Uploading Master List</h5>
                            <ol>
                                <li>Go to Dashboard</li>
                                <li>Click "Upload Master List"</li>
                                <li>Select Excel/CSV file</li>
                                <li>Verify and submit</li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5><i class="fas fa-user-plus"></i> Adding Users</h5>
                            <ol>
                                <li>Go to Users page</li>
                                <li>Click "Add New Employee"</li>
                                <li>Fill required information</li>
                                <li>Submit the form</li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5><i class="fas fa-check-circle"></i> Verifying Businesses</h5>
                            <ol>
                                <li>Go to Pending Checks</li>
                                <li>Click "Resolve" on a request</li>
                                <li>Review business details</li>
                                <li>Approve or reject</li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5><i class="fas fa-file-export"></i> Exporting Reports</h5>
                            <ol>
                                <li>Navigate to desired section</li>
                                <li>Click "Export" button</li>
                                <li>Select format (PDF/Excel/CSV)</li>
                                <li>Download the file</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Master List Template Section -->
        <div id="master-list-section" class="content-section">
            <h3><strong>Master List Upload Template</strong></h3>

            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> Using the correct template format is essential for successful uploads.
            </div>

            <div class="row" style="display: flex; justify-content: center;">
                <div class="col-md-6">
                    <div class="template-card">
                        <div class="text-center">
                            <i class="fas fa-file-excel text-success template-icon"></i>
                            <h4>Master List CSV Template</h4>
                            <p>Standard template for uploading OFAC master list data</p>
                            <a href="#" class="master-download-btn">
                                <i class="fas fa-download"></i> Download Template
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                document.querySelector(".master-download-btn").addEventListener("click", function() {
                    // Define CSV content with actual business data
                    let csvContent = "data:text/csv;charset=utf-8," + "Business Name,Owner,Address,Registration No,Status\n" +
                        "\"Tech Solutions Inc.\",\"David Wilson\",\"966 Main St, City 1, State IL\",\"REG-895876\",\"Not Eligible\"\n" +
                        "\"Green Energy Ltd.\",\"Daniel Martinez\",\"768 Main St, City 2, State NY\",\"REG-416853\",\"Eligible\"";

                    // Encode the CSV data
                    let encodedUri = encodeURI(csvContent);

                    // Create a hidden download link
                    let link = document.createElement("a");
                    link.setAttribute("href", encodedUri);
                    link.setAttribute("download", "OFAC Master List.csv");

                    // Append to the document, trigger the click, and remove it
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                });
            </script>

            <h4>Upload Instructions</h4>
            <ol>
                <li>Download the appropriate template</li>
                <li>Fill in the required data</li>
                <li>Save the file in CSV format</li>
                <li>Go to the Dashboard and click "Upload Master List"</li>
                <li>Select your file and click "Upload"</li>
            </ol>

            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <strong>Important:</strong> Uploading a new master list will replace the existing one. Make sure your data is complete and accurate.
            </div>
        </div>

        <!-- Business Details Template Section -->
        <div id="business-details-section" class="content-section">
            <h3><strong>Business Details Upload Template</strong></h3>

            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> Using the correct template format is essential for successful business verification.
            </div>

            <div class="row" style="display: flex; justify-content: center;">
                <div class="col-md-6">
                    <div class="template-card">
                        <div class="text-center">
                            <i class="fas fa-file-excel text-success template-icon"></i>
                            <h4>Business Details Template</h4>
                            <p>Standard template for uploading business information</p>
                            <a href="#" class="business-download-btn">
                                <i class="fas fa-download"></i> Download Template
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                document.querySelector(".business-download-btn").addEventListener("click", function() {
                    // Define CSV content with addresses enclosed in double quotes
                    let csvContent = "data:text/csv;charset=utf-8," +
                        "Business Name,Owner,Address,Registration No\n" +
                        "\"Tech Solutions Inc.\",\"David Wilson\",\"966 Main St, City 1, State IL\",\"REG-895876\"\n" +
                        "\"Green Energy Ltd.\",\"Daniel Martinez\",\"768 Main St, City 2, State NY\",\"REG-416853\"";


                    // Encode the CSV data
                    let encodedUri = encodeURI(csvContent);

                    // Create a hidden download link
                    let link = document.createElement("a");
                    link.setAttribute("href", encodedUri);
                    link.setAttribute("download", "business_details.csv");

                    // Append to the document, trigger the click, and remove it
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                });
            </script>

            <h4>Upload Instructions</h4>
            <ol>
                <li>Download the appropriate template</li>
                <li>Fill in the required business information</li>
                <li>Save the file in CSV format</li>
                <li>Go to the Dashboard and click "Upload Business Details"</li>
                <li>Select your file and click "Upload"</li>
            </ol>

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> <strong>Note:</strong> After uploading, businesses will be queued for verification against the OFAC master list.
            </div>
        </div>

        <!-- API Documentation Section
        <div id="api-documentation-section" class="content-section">
            <h3><strong>API Documentation</strong></h3>

            <div class="alert alert-primary">
                <i class="fas fa-code"></i> The OFAC system provides API endpoints for integration with other systems.
            </div>

            <h4>Authentication</h4>
            <p>All API requests require authentication using an API key:</p>
            <pre><code>Authorization: Bearer YOUR_API_KEY</code></pre>
            <p>To obtain an API key, please contact the system administrator.</p>

            <h4>Available Endpoints</h4>

            <div class="api-endpoint">
                <h5><span class="api-method get">GET</span> /api/v1/status</h5>
                <p>Check the API service status.</p>
                <p><strong>Response:</strong></p>
                <pre><code>{
  "status": "online",
  "version": "1.0.0",
  "timestamp": "2023-12-01T12:00:00Z"
}</code></pre>
            </div>

            <div class="api-endpoint">
                <h5><span class="api-method post">POST</span> /api/v1/verify</h5>
                <p>Verify a business against the OFAC master list.</p>
                <p><strong>Request Body:</strong></p>
                <pre><code>{
  "business_name": "Example Corp",
  "registration_number": "12345678",
  "country": "United States",
  "address": "123 Main St, Anytown, USA"
}</code></pre>
                <p><strong>Response:</strong></p>
                <pre><code>{
  "verification_id": "ver_123456",
  "status": "pending",
  "timestamp": "2023-12-01T12:05:00Z",
  "estimated_completion": "2023-12-01T12:10:00Z"
}</code></pre>
            </div>

            <div class="api-endpoint">
                <h5><span class="api-method get">GET</span> /api/v1/verification/{verification_id}</h5>
                <p>Get the status of a verification request.</p>
                <p><strong>Response:</strong></p>
                <pre><code>{
  "verification_id": "ver_123456",
  "status": "completed",
  "result": "no_match",
  "timestamp": "2023-12-01T12:08:00Z",
  "details": {
    "match_percentage": 0,
    "notes": "No matches found in OFAC database"
  }
}</code></pre>
            </div>

            <div class="api-endpoint">
                <h5><span class="api-method get">GET</span> /api/v1/history</h5>
                <p>Get verification history for your account.</p>
                <p><strong>Query Parameters:</strong></p>
                <ul>
                    <li><code>limit</code> - Maximum number of results (default: 10)</li>
                    <li><code>offset</code> - Pagination offset (default: 0)</li>
                    <li><code>start_date</code> - Filter by start date (format: YYYY-MM-DD)</li>
                    <li><code>end_date</code> - Filter by end date (format: YYYY-MM-DD)</li>
                </ul>
                <p><strong>Response:</strong></p>
                <pre><code>{
  "total": 42,
  "limit": 10,
  "offset": 0,
  "results": [
    {
      "verification_id": "ver_123456",
      "business_name": "Example Corp",
      "status": "completed",
      "result": "no_match",
      "timestamp": "2023-12-01T12:08:00Z"
    },
    // Additional results...
  ]
}</code></pre>
            </div>

            <h4>Error Handling</h4>
            <p>The API uses standard HTTP status codes and returns error details in the response body:</p>
            <pre><code>{
  "error": {
    "code": "invalid_request",
    "message": "Missing required field: business_name",
    "status": 400
  }
}</code></pre>
        </div> -->

        <!-- System Requirements Section -->
        <div id="system-requirements-section" class="content-section">
            <h3><strong>System Requirements</strong></h3>

            <div class="alert alert-info">
                <i class="fas fa-laptop"></i> The OFAC system is web-based and requires the following specifications for optimal performance.
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h4><i class="fas fa-desktop"></i> Client Requirements</h4>
                        </div>
                        <div class="card-body">
                            <h5>Supported Browsers</h5>
                            <ul class="requirements-list">
                                <li>Google Chrome (version 90 or higher)</li>
                                <li>Mozilla Firefox (version 88 or higher)</li>
                                <li>Microsoft Edge (version 90 or higher)</li>
                                <li>Safari (version 14 or higher)</li>
                            </ul>

                            <h5>Hardware Requirements</h5>
                            <ul class="requirements-list">
                                <li>Processor: 2 GHz dual-core or better</li>
                                <li>RAM: 4 GB minimum (8 GB recommended)</li>
                                <li>Display: 1366 x 768 resolution or higher</li>
                                <li>Internet: Broadband connection (1 Mbps or faster)</li>
                            </ul>

                            <h5>Operating Systems</h5>
                            <ul class="requirements-list">
                                <li>Windows 10 or 11</li>
                                <li>macOS 10.14 (Mojave) or higher</li>
                                <li>Ubuntu 18.04 LTS or higher</li>
                                <li>Chrome OS (latest version)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h4><i class="fas fa-server"></i> Server Requirements</h4>
                        </div>
                        <div class="card-body">
                            <h5>For Self-Hosted Installations</h5>
                            <ul class="requirements-list">
                                <li>Web Server: Apache 2.4+ or Nginx 1.18+</li>
                                <li>PHP: Version 7.4 or higher</li>
                                <li>Database: MySQL 5.7+ or MariaDB 10.3+</li>
                                <li>Storage: 10 GB minimum (depends on data volume)</li>
                                <li>Memory: 8 GB RAM minimum (16 GB recommended)</li>
                                <li>Processor: 4 cores minimum (8 cores recommended)</li>
                                <li>SSL Certificate: Required for secure connections</li>
                            </ul>

                            <h5>PHP Extensions</h5>
                            <ul class="requirements-list">
                                <li>PDO and PDO_MySQL</li>
                                <li>OpenSSL</li>
                                <li>Mbstring</li>
                                <li>Tokenizer</li>
                                <li>XML</li>
                                <li>Ctype</li>
                                <li>JSON</li>
                                <li>GD or Imagick</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h4><i class="fas fa-shield-alt"></i> Security Recommendations</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Network Security</h5>
                            <ul class="requirements-list">
                                <li>Firewall configuration to restrict access</li>
                                <li>HTTPS with TLS 1.2 or higher</li>
                                <li>Regular security updates for all components</li>
                                <li>IP whitelisting for administrative access</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Data Protection</h5>
                            <ul class="requirements-list">
                                <li>Regular database backups</li>
                                <li>Data encryption at rest and in transit</li>
                                <li>Secure password policies</li>
                                <li>Regular security audits</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> <strong>Note:</strong> These requirements are for the current version of the OFAC system. Requirements may change with future updates.
            </div>
        </div>

        <div class="text-center mt-4 mb-3">
            <a href="help_admin.php#contact-section" class="btn btn-primary back-btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Get the guide parameter from URL
            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }

            // Hide all sections initially
            $('.content-section').removeClass('active');
            $('.section-btn').removeClass('active');

            // Get the guide parameter
            var guide = getUrlParameter('guide');

            // If guide parameter exists, show that section
            if (guide) {
                $('#' + guide + '-section').addClass('active');
                $('.section-btn[data-section="' + guide + '"]').addClass('active');
            } else {
                // Default to admin manual if no parameter
                $('#admin-manual-section').addClass('active');
                $('.section-btn[data-section="admin-manual"]').addClass('active');
            }

            // Section navigation
            $('.section-btn').click(function() {
                // Remove active class from all buttons and sections
                $('.section-btn').removeClass('active');
                $('.content-section').removeClass('active');

                // Add active class to clicked button
                $(this).addClass('active');

                // Get the section to show
                var sectionToShow = $(this).data('section');
                $('#' + sectionToShow + '-section').addClass('active');

                // Update URL without reloading the page
                var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?guide=' + sectionToShow;
                window.history.pushState({
                    path: newUrl
                }, '', newUrl);
            });

            // Download button hover effect
            $('.download-btn').hover(
                function() {
                    $(this).css('background-color', '#218838');
                },
                function() {
                    $(this).css('background-color', '#28a745');
                }
            );
        });
    </script>
</body>

</html>