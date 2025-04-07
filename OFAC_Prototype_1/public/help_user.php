<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Help</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/table_style.css">
    <style>
        .content {
            overflow-y: scroll;
        }

        .nav {
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
                <i class="fas fa-user"></i>
                User Dashboard
            </h5>
        </div>
        <a href="home.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="user_own_history.php"><i class="fas fa-history"></i> History</a>
        <a href="help_user.php" class="active"><i class="fas fa-hands-helping"></i> Help</a>

        <a href="../templates/logout.php" class="mt-auto"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Page Content -->

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5" style="font-weight: 750;">User Help</h2>
                    <nav class="nav">
                        <ul class="navbtn">
                            <button class="section-btn active" data-section="general">
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
                        <p>
                        <h5 style="font-weight: 700;">User Dashboard</h5>
                        <p>
                            The User Dashboard is the main page for the user. It contains the following options:
                        <ul>
                            <li><b>Dashboard</b> - The main page of the user.</li>
                            <li><b>History</b> - The history of the user's uploads.</li>
                            <li><b>Help</b> - The help page for the user.</li>
                        </ul>
                        </p>
                        </p>
                        <hr>

                        <p>
                        <h5 style="font-weight: 700;">Dashboard</h5>
                        <p><img src="../assets/images/dashboard_user.png" alt="image.png" style="height: 500px; width: 800px"></p>

                        <p>
                            The Dashboard is the main page of the user. It contains the following options:
                        <ul>
                            <li><b>Upload Business Details</b> - The user can upload the business details here.</li>
                            <li><b>Check Business Details</b> - The user can check the business details here.</li>
                            <li><b>Edit Profile</b> - The user can edit their name and change their password here.</li>


                        </ul>
                        </p>
                        </p>
                        <hr>

                        <p>
                        <h5 style="font-weight: 700;">History</h5>
                        <p><img src="../assets/images/history_user.png" alt="image.png" style="height: 500px; width: 800px"></p>
                        <p>
                            The History page contains the history of the user's uploads. It contains the following options:
                        <ul>
                            <li><b>Business Details Upload History</b> - The history of the user's business details uploads.</li>
                        </ul>
                        </p>
                        </p>
                        <hr>

                        <p>
                        <h5 style="font-weight: 700;">Help</h5>
                        <p>
                            The Help page contains the help information for the user. It contains the following options:
                        <ul>
                            <li><b>User Help</b> - The help information for the user.</li>
                        </ul>
                        </p>
                        </p>
                        <hr>

                        <h2 id="forgot-password" style="font-weight: 700;">Forgot Password</h2>
                        <p><img src="../assets/images/Forgot_Pass_user.png" alt="image.png" style="height: 450px; width: 700px"></p>
                        <ol>
                            <li>User navigates to the login page.</li>
                            <li>User clicks on the "Forgot Password?" link.</li>
                            <li>User enters the registered email address.</li>
                            <li>User clicks the "Submit" button.</li>
                            <li>System sends an email with a password reset OTP to the registered user mail ID.</li>
                            <li>User receives the email with an OTP and clicks on the &ldquo;Verify Request&rdquo; Button.</li>
                            <li>User enters the OTP received on the mail ID.</li>
                            <li>User clicks the "Verify OTP" button.</li>
                            <li>After Verifying tab will close automatically.</li>
                            <li>User attempts to change the password and make new password and update it.</li>
                            <li>Success message is shown: &rdquo;Password changed successfully . Please login with your new password.</li>
                        </ol>
                        <hr>
                    </div>

                    <!-- FAQs Section -->
                    <div id="faqs-section" class="content-section">
                        <h3><strong>Frequently Asked Questions</strong></h3>
                        <div class="accordion" id="faqAccordion">
                            <div class="card">
                                <div class="card-header" id="faqOne">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne">
                                            How do I upload business details?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseOne" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        To upload business details:
                                        <ol>
                                            <li>Go to the Dashboard</li>
                                            <li>Click on "Upload Business Details"</li>
                                            <li>Select your Excel/CSV file</li>
                                            <li>Verify the data format matches the template</li>
                                            <li>Click "Upload" to submit</li>
                                        </ol>
                                        Note: Make sure your file follows the correct format to avoid errors.
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="faqTwo">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseTwo">
                                            How do I check the status of my business verification?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseTwo" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        To check verification status:
                                        <ol>
                                            <li>Go to "History" in the sidebar</li>
                                            <li>View your Business Details Upload History</li>
                                            <li>Check the "Status" column for each entry</li>
                                            <li>Statuses can be:
                                                <ul>
                                                    <li>Pending - Still under review</li>
                                                    <li>Eligible - Business approved</li>
                                                    <li>Not Eligible - Business not approved</li>
                                                </ul>
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="faqThree">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseThree">
                                            How do I update my profile information?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseThree" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        To update your profile:
                                        <ol>
                                            <li>Go to Dashboard</li>
                                            <li>Click on "Edit Profile"</li>
                                            <li>Update your information:
                                                <ul>
                                                    <li>Name</li>
                                                    <li>Password</li>
                                                    <li>Contact details</li>
                                                </ul>
                                            </li>
                                            <li>Click "Save Changes"</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="faqFour">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseFour">
                                            What should I do if my upload fails?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseFour" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        If your upload fails:
                                        <ol>
                                            <li>Check common issues:
                                                <ul>
                                                    <li>File format (must be Excel or CSV)</li>
                                                    <li>File size (maximum 10MB)</li>
                                                    <li>Data format matches template</li>
                                                    <li>All required fields are filled</li>
                                                </ul>
                                            </li>
                                            <li>Try uploading again after fixing any issues</li>
                                            <li>If problems persist, Support Docs</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="faqFive">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseFive">
                                            How can I view my upload history?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseFive" class="collapse" data-parent="#faqAccordion">
                                    <div class="card-body">
                                        To view your history:
                                        <ol>
                                            <li>Click on "History" in the sidebar</li>
                                            <li>You can see:
                                                <ul>
                                                    <li>Upload dates</li>
                                                    <li>File names</li>
                                                    <li>Verification status</li>
                                                    <li>Results</li>
                                                </ul>
                                            </li>
                                            <li>Use filters to find specific uploads</li>
                                            <li>Export history if needed</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Support Docs Section -->
                    <div id="contact-section" class="content-section">
                        <h3><strong>Support Docs</strong></h3>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add required scripts -->
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
    </script>
</body>

</html>