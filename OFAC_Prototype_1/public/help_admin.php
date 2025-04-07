<?php

include "../templates/session_management.php";
// if (!isset($_SESSION['eid']) || ($_SESSION['user_type'] !== 'admin')) {
//     header('Location: ../index.php');
//     exit();
// }
include("../templates/user_auth.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Help</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/table_style.css">
    <style>
        .content {
            overflow-y: scroll;
        }

        .nav {
            /* display: flex; */
            width: 100%;
            height: auto;
            padding: 20px;
            box-sizing: border-box;
            overflow: hidden;
            border-radius: 5px;
            transition: width .75s ease;
        }

        .navbtn {
            list-style-type: none;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        .section-btn {
            padding: 10px 20px;
            margin-right: 10px;
            border: none;
            background-color: #f8f9fa;
            cursor: pointer;
            border-radius: 5px;
        }

        .section-btn.active {
            background-color: #007bff;
            color: white;
        }

        .content-section {
            display: none;
        }

        .content-section.active {
            display: block;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="p-6">
            <h5 class="text-center" style="color:rgb(255, 255, 255);">
                <i class="fas fa-shield-alt"></i>
                Admin Dashboard
            </h5>
        </div>
        <a href="admin_home.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="users.php"><i class="fas fa-users"></i> Users</a>
        <a href="user_upload_history.php"><i class="fas fa-history"></i> History</a>
        <a href="view_master_list.php"><i class="fas fa-list"></i> Master List</a>
        <a href="pending_checks.php"><i class="fas fa-hourglass-half"></i> Pending Checks</a>
        <a href="help_admin.php" class="active"><i class="fas fa-hands-helping"></i>Help</a>

        <!-- Logs Dropdown Menu -->
        <!-- <div class="dropdown">
            <a href="logs.php"><i class="fas fa-file-alt"></i> Logs</a>
            <div class="dropdown-menu">
                <a href="admin_upload_history.php">Business Details Upload History</a>
                <a href="master_list_upload_history.php">Master List Upload History</a>
                <a href="employee_activity.php">Employee Activity</a>
                <a href="master_list_edit_history.php">Master List Edit History</a>
            </div>
        </div> -->
        <?php include("../templates/logs_dropdown.php"); ?>

        <a href="../templates/logout.php" class="mt-auto"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Page Content -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5" style="font-weight: 750;">Admin Help</h2>
                    <nav class="nav">
                        <ul class="navbtn">
                            <button class="section-btn" data-section="general">
                                <li>Relevant Topics</li>
                            </button>
                            <button class="section-btn" data-section="faqs">
                                <li>FAQs</li>
                            </button>
                            <button class="section-btn" data-section="contact">
                                <li>Support Docs</li>
                            </button>
                        </ul>
                    </nav>
                    <hr>

                    <!-- Relevant Topics Section -->
                    <div id="general-section" class="content-section active">
                        <h3><strong>Admin Dashboard</strong></h3>
                        <p>The Admin Dashboard is the main page for the admin. It contains the following options:</p>
                        <ul>
                            <li><strong>Dashboard</strong>&nbsp;- The main page of the admin.</li>
                            <li><strong>Users</strong>&nbsp;- The page where the admin can view all the users and their details.</li>
                            <li><strong>History</strong>&nbsp;- The page where the admin can view the upload history of the users.</li>
                            <li><strong>Master List</strong>&nbsp;- The page where the admin can view the master list of the users.</li>
                            <li><strong>Help</strong>&nbsp;- The page where the admin can view the help section.</li>
                            <li><strong>Logs</strong>&nbsp;- The page where the admin can view the logs of the users and the admins.</li>
                        </ul>
                        <hr />
                        <h3><strong>Dashboard</strong></h3>
                        <p><img src="../assets/images/dashboard_admin.png" alt="image.png" style="height: 500px; width: 800px"></p>
                        <p>The Dashboard is the main page of the admin. It contains the following options:</p>
                        <ul>
                            <li><strong>View Users</strong>&nbsp;- The admin can view all the users and their details here.</li>
                            <li><strong>Add New Employee</strong> - Enables the admin to add a new user.</li>
                            <li><strong>View Master List</strong>&nbsp;<strong>Upload History</strong> - Enables the admin to view Master List Upload History</li>
                            <li><strong>Edit Profile</strong>&nbsp;- The user can change their own name and password.</li>
                            <li><strong>Upload Business Details -</strong> Enables the admin to upload the Business Details.</li>
                            <li><strong>Upload Master List</strong>&nbsp;- Enables the admin to upload the master list. <a href="user_guides.php?guide=master_list" class="text-primary">View Template Guide</a></li>
                        </ul>
                        <hr />
                        <h3><strong>Users</strong></h3>
                        <p><img src="../assets/images/users_admin.png" alt="image.png" style="height: 500px; width: 800px"></p>
                        <p>The Users page contains the list of all the users and their details. It contains the following options:</p>
                        <ul>
                            <li><strong>User Details</strong>&nbsp;- The details of the user.</li>
                            <li><strong>Add New Employee</strong> - The admin can add new user here.</li>
                            <li><strong>Edit Details</strong> - Admin can edit the details of the User here.</li>
                            <li><strong>Export</strong> - Admin can export the User Details in PDF, Excel and CSV.</li>
                        </ul>
                        <hr />
                        <h3><strong>History</strong></h3>
                        <p><img src="../assets/images/history_admin.png" alt="image.png" style="height: 500px; width: 800px"></p>
                        <p>The History page contains the upload history of the users. It contains the following options:</p>
                        <ul>
                            <li><strong>Business Details Upload History</strong>&nbsp;- The upload history of the Business Details. <a href="user_guides.php?guide=business_details" class="text-primary">View Template Guide</a></li>
                            <li><strong>Export</strong> - Admin can export the Business Details Upload History in PDF, Excel and CSV.</li>
                        </ul>
                        <hr />
                        <h3><strong>Master List</strong></h3>
                        <p><img src="../assets/images/master_list.png" alt="image.png" style="height: 500px; width: 800px"></p>
                        <p>The Master List page contains the OFAC Master List. It contains the following options:</p>
                        <ul>
                            <li><strong>View Master List</strong>&nbsp;- The master list uploaded by the admin. <a href="user_guides.php?guide=master_list" class="text-primary">View Template Guide</a></li>
                            <li><strong>Export</strong> - Admin can export the master list Details in PDF, Excel and CSV.</li>
                        </ul>
                        <hr />

                        <h3><strong>Pending Check</strong></h3>
                        <p><img src="../assets/images/pending_checks.png" alt="image.png" style="height: 500px; width: 800px"></p>
                        <p>The Pending Check page contains the businesses which are having status as pending. It contains the following options:</p>
                        <ul>
                            <li><strong>Resolve</strong>&nbsp;- The admin can resolve the pending checks of the users.</li>
                            <li><strong>Export</strong> - Admin can export the pending checks Details in PDF, Excel and CSV.</li>
                        </ul>
                        <hr />

                        <h3><strong>Help</strong></h3>
                        <p>The Help page contains the help information for the admin. It contains the following options:</p>
                        <ul>
                            <li><strong>Admin Help</strong>&nbsp;- The help information for the admin.</li>
                            <li><strong>FAQs</strong>&nbsp;- The frequently asked questions.</li>
                            <li><strong>Support Docs</strong>&nbsp;- The support documents for the admin.</li>
                        </ul>
                        <hr />
                        <h3><strong>Logs</strong></h3>
                        <p><img src="../assets/images/logs.png" alt="image.png" style="height: 500px; width: 800px"></p>
                        <p>The Logs page contains the logs of the users. It contains the following options:</p>
                        <ul>
                            <li><strong>Business Details Upload History</strong>&nbsp;- The history of the business details uploads.</li>
                            <li><strong>Master List Upload History</strong>&nbsp;- The history of the master list uploads.</li>
                            <li><strong>Employee Activity</strong>&nbsp;- The activity of the employees.</li>
                            <li><strong>Master List Edit History</strong>&nbsp;- The history of the master list edits.</li>
                            <li><strong>Pending Checks Resolve History</strong>&nbsp;- The history of the Pending Checks Resolve History</li>
                        </ul>
                        <hr>

                        <h2 id="forgot-password" style="font-weight: 700;">Forgot Password</h2>
                        <p><img src="../assets/images/forgot_pass_admin.png" alt="image.png" style="height: 450px; width: 700px"></p>
                        <ol>
                            <li>Admin navigates to the login page.</li>
                            <li>Admin clicks on the "Forgot Password?" link.</li>
                            <li>Admin enters the registered email address.</li>
                            <li>Admin clicks the "Submit" button.</li>
                            <li>System sends an email with a password reset OTP to the registered user mail ID.</li>
                            <li>Admin receives the email with an OTP and clicks on the &ldquo;Verify Request&rdquo; Button.</li>
                            <li>Admin enters the OTP received on the mail ID..</li>
                            <li>Admin clicks the "Verify OTP" button.</li>
                            <li>After Verifying tab will close automatically.</li>
                            <li>Admin changes the password to new password.</li>
                            <li>Confirmation message is displayed: &rdquo;Password changed successfully. Please login with your new password.</li>
                            <li>Admin can now login with their new password.</li>
                        </ol>
                        <hr>

                    </div>

                    <!-- FAQs Section -->
                    <div id="faqs-section" class="content-section">
                        <h3><strong>Frequently Asked Questions</strong></h3>
                        <div class="accordion" id="faqAccordion">

                            <!-- <div class="card">
                                <div class="card-header" id="faqOne">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne">
                                            How do I reset a user's password?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseOne" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        As an admin, you can reset a user's password by:
                                        <ol>
                                            <li>Going to the Users page</li>
                                            <li>Finding the user in the list</li>
                                            <li>Clicking on the "Edit" button</li>
                                            <li>Using the "Reset Password" option</li>
                                        </ol>
                                    </div>
                                </div>
                            </div> -->

                            <div class="card">
                                <div class="card-header" id="faqTwo">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseTwo">
                                            How do I upload a new Master List?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseTwo" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        To upload a new Master List:
                                        <ol>
                                            <li>Go to the Dashboard</li>
                                            <li>Click on "Upload Master List"</li>
                                            <li>Select your Excel/CSV file containing the master list data</li>
                                            <li>Verify the data format matches the required template</li>
                                            <li>Click "Upload" to process the file</li>
                                        </ol>
                                        Note: Make sure your file follows the correct format to avoid upload errors.
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="faqThree">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseThree">
                                            How do I add a new employee to the system?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseThree" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        To add a new employee:
                                        <ol>
                                            <li>Go to the Users page</li>
                                            <li>Click on "Add New Employee" button</li>
                                            <li>Fill in the required information (First Name, Last Name, Email, etc.)</li>
                                            <li>Select the appropriate designation (User/Admin)</li>
                                            <li>Click "Submit" to create the account</li>
                                        </ol>
                                        The system will automatically send login credentials to the employee's email address.
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="faqFour">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseFour">
                                            How can I view the activity logs?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseFour" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        To access activity logs:
                                        <ol>
                                            <li>Click on the "Logs" option in the sidebar</li>
                                            <li>Choose from the available log types:
                                                <ul>
                                                    <li>Business Details Upload History</li>
                                                    <li>Master List Upload History</li>
                                                    <li>Employee Activity</li>
                                                    <li>Master List Edit History</li>
                                                </ul>
                                            </li>
                                            <li>Use the search filters to find specific entries</li>
                                            <li>Export the logs in PDF, Excel, or CSV format if needed</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="faqFive">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseFive">
                                            What should I do if a user is locked out of their account?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseFive" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        If a user is locked out:
                                        <ol>
                                            <li>Go to the Users page</li>
                                            <li>Locate the user's account</li>
                                            <li>Check their account status</li>
                                            <li>Click "Edit" to access their account settings</li>
                                            <li>Change their status back to "Active" if it's "Suspended"</li>
                                            <li>Optionally, reset their password if needed</li>
                                        </ol>
                                        The user will receive an email notification about the account reactivation.
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="faqSix">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseSix">
                                            How do I handle pending business checks?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseSix" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        To handle pending business checks:
                                        <ol>
                                            <li>Go to "Pending Checks" in the sidebar</li>
                                            <li>Review the list of businesses pending verification</li>
                                            <li>Click the "Resolve" button next to the business</li>
                                            <li>Review the business details carefully</li>
                                            <li>Set the appropriate status (Eligible/Not Eligible)</li>
                                            <li>Click "Update" to save your decision</li>
                                        </ol>
                                        The business will be moved to the master list with the updated status.
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="faqSeven">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseSeven">
                                            How do I export data from the system?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseSeven" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        You can export data in multiple formats:
                                        <ol>
                                            <li>Navigate to the desired section (Users, Master List, History, etc.)</li>
                                            <li>Look for the "Export" button</li>
                                            <li>Choose your preferred format:
                                                <ul>
                                                    <li>PDF - For printable documents</li>
                                                    <li>Excel - For data analysis</li>
                                                    <li>CSV - For data integration</li>
                                                </ul>
                                            </li>
                                        </ol>
                                        The file will be downloaded automatically to your device.
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="faqEight">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseEight">
                                            What should I do if the system is running slowly?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseEight" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        If you experience slow performance:
                                        <ol>
                                            <li>Clear your browser cache and cookies</li>
                                            <li>Try refreshing the page</li>
                                            <li>Check your internet connection</li>
                                            <li>If the issue persists:
                                                <ul>
                                                    <li>Log out and log back in</li>
                                                    <li>Try a different browser</li>
                                                    <li>Contact technical support</li>
                                                </ul>
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="faqNine">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseNine">
                                            How do I track user activity?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseNine" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        To monitor user activity:
                                        <ol>
                                            <li>Click on "Logs" in the sidebar</li>
                                            <li>Select "Employee Activity"</li>
                                            <li>You can view:
                                                <ul>
                                                    <li>Login/Logout times</li>
                                                    <li>Actions performed</li>
                                                    <li>File uploads/downloads</li>
                                                </ul>
                                            </li>
                                            <li>Use filters to search specific dates or users</li>
                                            <li>Export the activity log if needed</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="faqTen">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseTen">
                                            How do I update my admin profile?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseTen" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        To update your profile:
                                        <ol>
                                            <li>Go to Dashboard</li>
                                            <li>Click on "Edit Profile"</li>
                                            <li>You can update:
                                                <ul>
                                                    <li>Name</li>
                                                    <li>Password</li>
                                                    <li>Contact information</li>
                                                </ul>
                                            </li>
                                            <li>Click "Save Changes" to update</li>
                                        </ol>
                                        Remember to use a strong password for security.
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="faqEleven">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseEleven">
                                            What file formats are supported for uploads?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseEleven" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        The system supports:
                                        <ul>
                                            <li>Excel files (.xlsx, .xls)</li>
                                            <li>CSV files (.csv)</li>
                                        </ul>
                                        Important notes:
                                        <ol>
                                            <li>Maximum file size: 10MB</li>
                                            <li>Files must follow the template format</li>
                                            <li>All required columns must be present</li>
                                            <li>Data should be properly formatted</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="faqTwelve">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseTwelve">
                                            How do I manage user permissions?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseTwelve" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        To manage user permissions:
                                        <ol>
                                            <li>Go to the Users page</li>
                                            <li>Find the user you want to modify</li>
                                            <li>Click "Edit"</li>
                                            <li>You can:
                                                <ul>
                                                    <li>Change user type (Admin/User)</li>
                                                    <li>Activate/Deactivate account</li>
                                                </ul>
                                            </li>
                                            <li>Save changes</li>
                                        </ol>
                                        Note: Changes take effect immediately.
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="faqThirteen">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseThirteen">
                                            How to handle system errors?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseThirteen" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        If you encounter system errors:
                                        <ol>
                                            <li>Note the error message</li>
                                            <li>Check if the issue is:
                                                <ul>
                                                    <li>Network related</li>
                                                    <li>File format related</li>
                                                    <li>Permission related</li>
                                                </ul>
                                            </li>
                                            <li>Try basic troubleshooting:
                                                <ul>
                                                    <li>Refresh the page</li>
                                                    <li>Clear browser cache</li>
                                                    <li>Log out and log back in</li>
                                                </ul>
                                            </li>
                                            <li>If the issue persists, contact technical support with the error details</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="faqFourteen">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseFourteen">
                                            How to ensure data security?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseFourteen" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        Best practices for data security:
                                        <ol>
                                            <li>Password Management:
                                                <ul>
                                                    <li>Use strong passwords</li>
                                                    <li>Change passwords regularly</li>
                                                    <li>Never share credentials</li>
                                                </ul>
                                            </li>
                                            <li>Session Security:
                                                <ul>
                                                    <li>Log out after each session</li>
                                                    <li>Don't use public computers</li>
                                                    <li>Clear browser cache regularly</li>
                                                </ul>
                                            </li>
                                            <li>Data Handling:
                                                <ul>
                                                    <li>Verify data before upload</li>
                                                    <li>Regular backups</li>
                                                    <li>Monitor user activities</li>
                                                </ul>
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="faqFifteen">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseFifteen">
                                            How to generate and interpret reports?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseFifteen" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        For report management:
                                        <ol>
                                            <li>Generating Reports:
                                                <ul>
                                                    <li>Navigate to desired section</li>
                                                    <li>Use filter options if needed</li>
                                                    <li>Click export button</li>
                                                    <li>Choose format (PDF/Excel/CSV)</li>
                                                </ul>
                                            </li>
                                            <li>Interpreting Data:
                                                <ul>
                                                    <li>Check status columns</li>
                                                    <li>Review timestamps</li>
                                                    <li>Verify user actions</li>
                                                    <li>Monitor trends</li>
                                                </ul>
                                            </li>
                                            <li>Regular Reports to Generate:
                                                <ul>
                                                    <li>User activity logs</li>
                                                    <li>Upload history</li>
                                                    <li>System performance</li>
                                                </ul>
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            <!-- Support Docs Section -->
            <div id="contact-section" class="content-section">
                <h3><strong>Support Documentation</strong></h3>

                <div class="card mb-4">
                    <div class="card-header">
                        <h4>System Documentation</h4>
                    </div>
                    <div class="card-body">
                        <h5>User Guides</h5>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item">
                                <i class="fas fa-file-pdf text-danger"></i>
                                <a href="user_guides.php?guide=admin-manual">&nbsp;Administrator Manual</a>
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-file-pdf text-danger"></i>
                                <a href="user_guides.php?guide=quick-start">&nbsp;Quick Start Guide</a>
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-file-excel text-success"></i>
                                <a href="user_guides.php?guide=master-list">&nbsp;Master List Upload Template</a>
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-file-excel text-success"></i>
                                <a href="user_guides.php?guide=business-details">&nbsp;Business Details Upload Template</a>
                            </li>
                        </ul>

                        <h5>Technical Resources</h5>
                        <ul class="list-group list-group-flush mb-3">
                            <!-- <li class="list-group-item">
                                <i class="fas fa-code text-primary"></i>
                                <a href="user_guides.php?guide=api-documentation">&nbsp;API Documentation</a>
                            </li> -->
                            <li class="list-group-item">
                                <i class="fas fa-laptop text-secondary"></i>
                                <a href="user_guides.php?guide=system-requirements">&nbsp;System Requirements</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Training Materials</h4>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <a href="training_guides.php?guide=admin_dashboard" class="list-group-item list-group-item-action">
                                <i class="fas fa-images text-info"></i>
                                &nbsp;Introduction to Admin Dashboard
                            </a>
                            <a href="training_guides.php?guide=user_permissions" class="list-group-item list-group-item-action">
                                <i class="fas fa-images text-info"></i>
                                &nbsp;Managing Users and Permissions
                            </a>
                            <a href="training_guides.php?guide=business_verification" class="list-group-item list-group-item-action">
                                <i class="fas fa-images text-info"></i>
                                &nbsp;Handling Business Verifications
                            </a>
                            <a href="training_guides.php?guide=reports_logs" class="list-group-item list-group-item-action">
                                <i class="fas fa-images text-info"></i>
                                &nbsp;Working with Reports and Logs
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Support Contact Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Technical Support</h5>
                                <p><i class="fas fa-envelope"></i> Email: support@example.com</p>
                                <p><i class="fas fa-phone"></i> Phone: (555) 123-4567</p>
                                <p><i class="fas fa-clock"></i> Hours: 24/7</p>
                            </div>
                            <div class="col-md-6">
                                <h5>Emergency Contact</h5>
                                <p><i class="fas fa-phone-square"></i> Hotline: (555) 999-8888</p>
                                <p><i class="fas fa-envelope"></i> Email: emergency@example.com</p>
                                <p><i class="fas fa-exclamation-circle"></i> Available 24/7 for critical issues</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Latest Updates</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5><i class="fas fa-bell"></i> System Updates</h5>
                            <p>Latest version: 2.1.0 (Released: January 2024)</p>
                            <ul>
                                <li>Enhanced security features</li>
                                <li>Improved reporting system</li>
                                <li>New user interface elements</li>
                                <li>Bug fixes and performance improvements</li>
                            </ul>
                            <a href="#" class="btn btn-info btn-sm">View Changelog</a>
                        </div>
                    </div>
                </div>
            </div>

            <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                // Section switching functionality
                const sectionBtns = document.querySelectorAll('.section-btn');
                const contentSections = document.querySelectorAll('.content-section');

                sectionBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        // Remove active class from all buttons and sections
                        sectionBtns.forEach(b => b.classList.remove('active'));
                        contentSections.forEach(s => s.classList.remove('active'));

                        // Add active class to clicked button and corresponding section
                        btn.classList.add('active');
                        document.getElementById(`${btn.dataset.section}-section`).classList.add('active');
                    });
                });

                function activateSection(sectionId) {
                    // Remove active class from all buttons and sections
                    sectionBtns.forEach(b => b.classList.remove('active'));
                    contentSections.forEach(s => s.classList.remove('active'));

                    // Add active class to the corresponding button and section
                    document.querySelector(`.section-btn[data-section="${sectionId}"]`).classList.add('active');
                    document.getElementById(`${sectionId}-section`).classList.add('active');
                }

                sectionBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        activateSection(btn.dataset.section);
                    });
                });

                const urlFragment = window.location.hash.substring(1).replace('-section', '');
                if (urlFragment) {
                    activateSection(urlFragment);
                } else {
                    activateSection('general');
                }
            </script>
</body>

</html>