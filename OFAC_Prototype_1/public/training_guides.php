<?php
include "../templates/session_management.php";
include("../templates/user_auth.php");

// Get the requested guide
$guide = isset($_GET['guide']) ? $_GET['guide'] : 'admin_dashboard';

// Define the guide titles
$guideTitles = [
    'admin_dashboard' => 'Introduction to Admin Dashboard',
    'user_permissions' => 'Managing Users and Permissions',
    'business_verification' => 'Handling Business Verifications',
    'reports_logs' => 'Working with Reports and Logs'
];

// Set the current guide title
$currentGuideTitle = isset($guideTitles[$guide]) ? $guideTitles[$guide] : 'Training Guide';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $currentGuideTitle; ?> - OFAC Training Guide</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/table_style.css">
    <style>
        body {
            padding-top: 20px;
            background-color: #f8f9fa;
        }

        .guide-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }

        .guide-header {
            border-bottom: 2px solid #007bff;
            margin-bottom: 25px;
            padding-bottom: 15px;
        }

        .guide-nav {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 25px;
        }

        .step-container {
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 1px dashed #dee2e6;
        }

        .step-container:last-child {
            border-bottom: none;
        }

        .step-number {
            display: inline-block;
            width: 35px;
            height: 35px;
            line-height: 35px;
            text-align: center;
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            margin-right: 10px;
        }

        .step-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 15px;
        }

        .step-description {
            margin-bottom: 20px;
            color: #6c757d;
        }

        .step-image {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 5px;
            background-color: #fff;
            margin-bottom: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            max-width: 100%;
            height: auto;
        }

        .step-note {
            background-color: #f8f9fa;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-top: 15px;
            border-radius: 0 5px 5px 0;
        }

        .guide-nav .nav-link.active {
            background-color: #007bff;
            color: white;
        }

        .guide-nav .nav-link {
            color: #495057;
        }

        .back-to-help {
            margin-top: 20px;
        }

        /* Animation for step transitions */
        .step-container {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.5s ease forwards;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .step-container:nth-child(1) { animation-delay: 0.1s; }
        .step-container:nth-child(2) { animation-delay: 0.2s; }
        .step-container:nth-child(3) { animation-delay: 0.3s; }
        .step-container:nth-child(4) { animation-delay: 0.4s; }
        .step-container:nth-child(5) { animation-delay: 0.5s; }
    </style>
</head>

<body>
    <div class="container">
        <div class="guide-container">
            <div class="guide-header">
                <h1><i class="fas fa-book-open text-primary"></i> <?php echo $currentGuideTitle; ?></h1>
                <p class="lead">Step-by-step instructions to help you master the OFAC system</p>
            </div>

            <div class="guide-nav">
                <ul class="nav nav-pills nav-fill">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($guide == 'admin_dashboard') ? 'active' : ''; ?>" href="training_guides.php?guide=admin_dashboard">
                            <i class="fas fa-tachometer-alt"></i> Admin Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($guide == 'user_permissions') ? 'active' : ''; ?>" href="training_guides.php?guide=user_permissions">
                            <i class="fas fa-users-cog"></i> User Permissions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($guide == 'business_verification') ? 'active' : ''; ?>" href="training_guides.php?guide=business_verification">
                            <i class="fas fa-check-circle"></i> Business Verification
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($guide == 'reports_logs') ? 'active' : ''; ?>" href="training_guides.php?guide=reports_logs">
                            <i class="fas fa-chart-line"></i> Reports & Logs
                        </a>
                    </li>
                </ul>
            </div>

            <?php if ($guide == 'admin_dashboard'): ?>
                <!-- Admin Dashboard Guide -->
                <div class="step-container">
                    <h3 class="step-title"><span class="step-number">1</span> Accessing the Admin Dashboard</h3>
                    <p class="step-description">After logging in with your admin credentials, you'll be directed to the admin dashboard which provides an overview of the system.</p>
                    <img src="../assets/images/dashboard_admin.png" alt="Admin Dashboard" class="step-image img-fluid">
                    <p>The dashboard displays key metrics and quick access to important functions:</p>
                    <ul>
                        <li>Total number of users in the system</li>
                        <li>Recent activity logs</li>
                        <li>Pending business verifications</li>
                        <li>System status indicators</li>
                    </ul>
                </div>

                <div class="step-container">
                    <h3 class="step-title"><span class="step-number">2</span> Navigating the Admin Interface</h3>
                    <p class="step-description">The admin interface is designed for easy navigation with a sidebar menu containing all administrative functions.</p>
                    <img src="../assets/images/Sidebar_menu.png" alt="Admin Navigation" class="step-image img-fluid">
                    <p>The main navigation menu includes:</p>
                    <ul>
                        <li>Dashboard - Overview and quick stats</li>
                        <li>Users - User management functions</li>
                        <li>Master List - Database management</li>
                        <li>Pending Checks - Business verification queue</li>
                        <li>Logs - System activity records</li>
                        <li>Help - Documentation and support</li>
                    </ul>
                </div>

                <div class="step-container">
                    <h3 class="step-title"><span class="step-number">3</span> Understanding Dashboard Widgets</h3>
                    <p class="step-description">The dashboard contains several widgets that provide real-time information about system status and activities.</p>
                    <img src="../assets/images/dashboard_admin.png" alt="Dashboard Widgets" class="step-image img-fluid">
                    <p>Key dashboard elements include:</p>
                    <ul>
                        <li>Statistics Cards - Quick overview of important metrics</li>
                        <li>Recent Activity Feed - Latest actions in the system</li>
                        <li>Alert Notifications - Important system messages</li>
                        <li>Quick Action Buttons - Shortcuts to common tasks</li>
                    </ul>
                    <div class="step-note">
                        <strong>Note:</strong> Dashboard widgets are refreshed automatically every 5 minutes. You can manually refresh by clicking the refresh icon in each widget.
                    </div>
                </div>
            <?php elseif ($guide == 'user_permissions'): ?>
                <!-- User Permissions Guide -->
                <div class="step-container">
                    <h3 class="step-title"><span class="step-number">1</span> Accessing User Management</h3>
                    <p class="step-description">To manage users and their permissions, navigate to the Users section from the admin sidebar.</p>
                    <img src="../assets/images/users_admin.png" alt="User Management" class="step-image img-fluid">
                    <p>The Users page provides a complete list of all system users with options to:</p>
                    <ul>
                        <li>View user details</li>
                        <li>Add new users</li>
                        <li>Edit existing users</li>
                        <li>Deactivate/reactivate accounts</li>
                    </ul>
                </div>

                <div class="step-container">
                    <h3 class="step-title"><span class="step-number">2</span> Adding a New User</h3>
                    <p class="step-description">To add a new user to the system, click the "Add New Employee" button on the Users page.</p>
                    <img src="../assets/images/add_new_employee.png" alt="Add New User" class="step-image img-fluid">
                    <p>When adding a new user, you'll need to provide:</p>
                    <ul>
                        <li>First and Last Name</li>
                        <li>Email Address (will be used as username)</li>
                        <li>User Type (Admin or Regular User)</li>
                        <li>Designation(Admin/User)</li>
                        <li>Status(Active/Inactive)</li>
                    </ul>
                    <div class="step-note">
                        <strong>Note:</strong> The system will automatically generate a temporary password and send it to the user's email address.
                    </div>
                </div>

                <div class="step-container">
                    <h3 class="step-title"><span class="step-number">3</span> Modifying User Permissions</h3>
                    <p class="step-description">To change a user's permissions, locate the user in the list and click the "Edit" button.</p>
                    <img src="../assets/images/edit_employee.png" alt="Edit User Permissions" class="step-image img-fluid">
                    <p>In the edit user form, you can:</p>
                    <ul>
                        <li>Change user type between Admin and Regular User</li>
                        <li>Update personal information</li>
                        <li>Reset password</li>
                        <li>Change account status (Active/Inactive)</li>
                    </ul>
                </div>
            <?php elseif ($guide == 'business_verification'): ?>
                <!-- Business Verification Guide -->
                <div class="step-container">
                    <h3 class="step-title"><span class="step-number">1</span> Accessing Pending Verifications</h3>
                    <p class="step-description">To review businesses awaiting verification, navigate to the "Pending Checks" section from the admin sidebar.</p>
                    <img src="../assets/images/pending_checks.png" alt="Pending Checks" class="step-image img-fluid">
                    <p>The Pending Checks page displays all businesses that require verification with:</p>
                    <ul>
                        <li>Business name and registration details</li>
                        <li>Submission date and time</li>
                        <li>Submitting user information</li>
                        <li>Action buttons for verification</li>
                    </ul>
                </div>

                <div class="step-container">
                    <h3 class="step-title"><span class="step-number">2</span> Reviewing Business Details</h3>
                    <p class="step-description">Click the "Resolve" button next to a business to review its complete details.</p>
                    <img src="../assets/images/pending_checks_resolve.png" alt="Business Details" class="step-image img-fluid">
                    <p>The business details view shows:</p>
                    <ul>
                        <li>Complete business information</li>
                        <li>Option to select a status</li>
                    </ul>
                </div>

                 <div class="step-container">
                    <h3 class="step-title"><span class="step-number">3</span> Updating Business Information</h3>
                    <p class="step-description">After reviewing the business details, you need to upload the business information.</p>
                    <img src="../assets/images/resolve_form.png" alt="Verification Process" class="step-image img-fluid">
                    <p>To Upload:</p>
                    <ul>
                        <li>Change the status to Eligible or Not Eligible</li>
                        <li>Click on the "Update" Button</li>
                    </ul>
                    <div class="step-note">
                        <strong>Important:</strong> After updating, check the OFAC Master List to verify the updation.
                    </div>
                </div>

              <!--  <div class="step-container">
                    <h3 class="step-title"><span class="step-number">4</span> Recording Verification Results</h3>
                    <p class="step-description">After completing the verification, you must record the results in the system.</p>
                    <img src="../assets/images/Forgot_Pass_admin.png" alt="Recording Results" class="step-image img-fluid">
                    <p>When recording verification results:</p>
                    <ul>
                        <li>Select the appropriate status (Approved/Rejected/Pending Further Review)</li>
                        <li>Add detailed notes about your findings</li>
                        <li>Upload any supporting documentation</li>
                        <li>Submit the verification for record-keeping</li>
                    </ul>
                </div> -->
            <?php elseif ($guide == 'reports_logs'): ?>
                <!-- Reports and Logs Guide -->
                <div class="step-container">
                    <h3 class="step-title"><span class="step-number">1</span> Accessing System Logs</h3>
                    <p class="step-description">To view system logs, navigate to the "Logs" section from the admin sidebar.</p>
                    <img src="../assets/images/logs.png" alt="System Logs" class="step-image img-fluid">
                    <p>The Logs section provides access to various system logs:</p>
                    <ul>
                        <li>User Activity Logs - Track user actions</li>
                        <li>System Logs - Record system events and errors</li>
                        <li>Verification Logs - History of business verifications</li>
                        <li>Login/Logout Logs - User session information</li>
                    </ul>
                </div>

                <div class="step-container">
                    <h3 class="step-title"><span class="step-number">2</span> Filtering and Searching Logs</h3>
                    <p class="step-description">Use the filtering options to narrow down log entries and find specific information.</p>
                    <img src="../assets/images/history_user.png" alt="Filtering Logs" class="step-image img-fluid">
                    <p>Available filtering options include:</p>
                    <ul>
                        <li>Date Range - Filter by specific time periods</li>
                        <li>User - Filter by specific user actions</li>
                        <li>Action Type - Filter by type of activity</li>
                        <li>Status - Filter by outcome (success/failure)</li>
                    </ul>
                    <div class="step-note">
                        <strong>Tip:</strong> For complex searches, you can combine multiple filters to narrow down results more precisely.
                    </div>
                </div>

                <!-- <div class="step-container">
                    <h3 class="step-title"><span class="step-number">3</span> Generating Reports</h3>
                    <p class="step-description">The system allows you to generate various reports for analysis and compliance purposes.</p>
                    <img src="../assets/images/logs.png" alt="Generating Reports" class="step-image img-fluid">
                    <p>To generate a report:</p>
                    <ol>
                        <li>Navigate to the Reports section</li>
                        <li>Select the report type you need</li>
                        <li>Set parameters (date range, users, etc.)</li>
                        <li>Click "Generate Report"</li>
                    </ol>
                    <p>Available report types include:</p>
                    <ul>
                        <li>User Activity Summary</li>
                        <li>Verification Statistics</li>
                        <li>System Performance Metrics</li>
                        <li>Compliance Audit Reports</li>
                    </ul>
                </div> -->

                <div class="step-container">
                    <h3 class="step-title"><span class="step-number">4</span> Exporting and Sharing Reports</h3>
                    <p class="step-description">After generating a report, you can export it in various formats for sharing or archiving.</p>
                    <img src="../assets/images/history_user.png" alt="Exporting Reports" class="step-image img-fluid">
                    <p>Export options include:</p>
                    <ul>
                        <li>PDF - For formal documentation</li>
                        <li>Excel - For further data analysis</li>
                        <li>CSV - For data integration with other systems</li>
                        <li>Print - For physical copies</li>
                    </ul>
                    <div class="step-note">
                        <strong>Security Note:</strong> Exported reports may contain sensitive information. Always follow your organization's data handling policies when sharing reports.
                    </div>
                </div>
            <?php endif; ?>

            <div class="back-to-help text-center mt-5">
                <a href="help_admin.php#contact-section" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Help Center
                </a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add smooth scrolling to step links
        $(document).ready(function() {
            // Highlight steps on scroll
            $(window).scroll(function() {
                $('.step-container').each(function() {
                    var position = $(this).offset().top;
                    var scrollPosition = $(window).scrollTop() + 300;
                    
                    if (position < scrollPosition) {
                        $(this).addClass('active');
                    }
                });
            });
            
            // Trigger initial scroll check
            $(window).scroll();
        });
    </script>
</body>
</html>