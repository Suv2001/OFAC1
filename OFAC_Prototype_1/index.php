<?php
// $user = 'user';
include("templates/login_page.php");

if (!isset($_SESSION['error'])) {
    $_SESSION['error'] = null;
}

if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
}
if (!isset($_SESSION['lockout'])) {
    $_SESSION['lockout'] = false;
}
if (!isset($_SESSION['lockout_time'])) {
    $_SESSION['lockout_time'] = time();
}
if ($_SESSION['lockout_time'] < time()) {
    $_SESSION['attempts'] = 0;
    $_SESSION['lockout'] = false;
}
$maxAttempts = 3;
$RemainingAttempts = $maxAttempts - $_SESSION['attempts'];
if ($_SESSION['lockout_time'] < time()) {
    $_SESSION['attempts'] = 0;
    $_SESSION['lockout'] = false;
}

if ($_SESSION['attempts'] >= $maxAttempts && $_SESSION['lockout_time'] > time()) {
    $_SESSION['lockout'] = true;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./assets/images/image.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>OFAC Login</title>
</head>

<body>
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form action="templates/login_page.php" method="POST">
                <h1 class="u">User login</h1>
                <span>Enter your details</span>
                <br>

                <input type="hidden" name="role" value="user">

                <div class="mb-2">
                    <label for="employeeId" class="form-label">Employee ID</label>
                    <input name="eid" type="text" class="form-control employeeId" placeholder="Enter Employee ID" required>
                    <div class="blocked" style="color: rgb(189, 189, 189) !important;">Enter Employee ID</div>
                </div>

                <div class="mb-2">
                    <label for="password" class="form-label">Password</label>
                    <input name="password" type="password" class="form-control password" placeholder="Enter Password" required>
                    <div class="blocked" style="color: rgb(189, 189, 189) !important;">Enter Password</div>
                </div>

                <div class="error-message mb-3">
                    <?php if ($_SESSION['attempts'] >= 3) : ?>
                        <?php
                        $remainingLockoutTime = $_SESSION['lockout_time'] - time();
                        ?>
                        <span id="lockout-message1">Your account is locked. Please try again in <span id="countdown-timer1"><?php echo gmdate("i:s", $remainingLockoutTime); ?></span>.</span> <?php else : ?>
                        Attempts remaining: <?php echo $RemainingAttempts; ?>
                    <?php endif; ?>
                </div>

                <?php if (!empty($_SESSION['error'])) : ?>
                    <div class="error-message mb-3" id="error-message1" style="color: red;">
                        <?php echo $_SESSION['error']; ?>
                    </div>
                    <script>
                        setTimeout(function() {
                            document.getElementById('error-message1').style.display = 'none';
                        }, 5000);
                    </script>
                <?php endif; ?>

                <div class="d-grid gap-2">
                    <?php if ($_SESSION['lockout']) : ?>
                        <div class="btn btn-primary user_login_btn" style="pointer-events: none;">Log In</div>
                    <?php else : ?>
                        <button type="submit" class="btn btn-primary user_login_btn" id="userLogin">Log In</button>
                    <?php endif; ?>
                </div>

                <a href="templates/forgot_pass.php">Forgot Password?</a>
            </form>
        </div>

        <div class="form-container sign-in">
            <form action="templates/login_page.php" method="POST">
                <h1 class="u">Admin login</h1>
                <span>Enter Your details</span>
                <br>

                <input type="hidden" name="role" value="admin">

                <div class="mb-2">
                    <label for="employeeId" class="form-label">Employee ID</label>
                    <input name="eid" type="text" class="form-control employeeId" placeholder="Enter Employee ID" required>
                    <div class="blocked" style="color: rgb(189, 189, 189) !important;">Enter Employee ID</div>
                </div>

                <div class="mb-2">
                    <label for="password" class="form-label">Password</label>
                    <input name="password" type="password" class="form-control password" placeholder="Enter Password" required>
                    <div class="blocked" style="color: rgb(189, 189, 189) !important;">Enter Password</div>
                </div>

                <div class="error-message mb-3">
                    <?php if ($_SESSION['attempts'] >= 3) : ?>
                        <?php
                        $remainingLockoutTime = $_SESSION['lockout_time'] - time();
                        ?>
                        <span id="lockout-message2">Your account is locked. Please try again in <span id="countdown-timer2"><?php echo gmdate("i:s", $remainingLockoutTime); ?></span>.</span> <?php else : ?>
                        Attempts remaining: <?php echo $RemainingAttempts; ?>
                    <?php endif; ?>
                </div>

                <?php if (!empty($_SESSION['error'])) : ?>
                    <div class="error-message mb-3" id="error-message" style="color: red;">
                        <?php echo $_SESSION['error']; ?>
                    </div>
                    <script>
                        setTimeout(function() {
                            document.getElementById('error-message').style.display = 'none';
                        }, 5000);
                    </script>
                <?php endif; ?>

                <div class="d-grid gap-2">
                    <?php if ($_SESSION['lockout']) : ?>
                        <div class="btn btn-primary admin_login_btn" style="pointer-events: none;">Log In</div>
                    <?php else : ?>
                        <button type="submit" class="btn btn-primary admin_login_btn" id="adminLogin">Log In</button>
                    <?php endif; ?>
                </div>
                <a href="templates/forgot_pass.php">Forgot Password?</a>
            </form>
        </div>

        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <img src="assets/images/image.png" alt="ofac-image">
                    <!-- <h1>Hello Users</h1>
                    <p>Enter your details to login!</p> -->
                    <button class="hidden" id="login">Admin Login</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <img src="assets/images/image.png" alt="ofac-image">
                    <!-- <h1>Welcome Back</h1>
                    <p>Sign-in to access the OFAC portal!</p> -->
                    <button class="hidden" id="register">User Login</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let lockoutTime = <?php echo json_encode($remainingLockoutTime > 0 ? $remainingLockoutTime : 0); ?>;
        if (lockoutTime > 0) {
            const countdownElement1 = document.getElementById('countdown-timer1');
            const countdownElement2 = document.getElementById('countdown-timer2');

            const updateCountdown = () => {
                if (lockoutTime > 0) {
                    const minutes = Math.floor(lockoutTime / 60);
                    const seconds = lockoutTime % 60;
                    countdownElement1.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                    countdownElement2.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                    lockoutTime--;
                } else {
                    clearInterval(interval);
                    <?php $_SESSION['error'] = null; ?>
                    document.getElementById('lockout-message1').textContent = 'Your account is unlocked! Refresh the page.';
                    document.getElementById('lockout-message2').textContent = 'Your account is unlocked! Refresh the page.';

                }
            };

            const interval = setInterval(updateCountdown, 1000);
            updateCountdown();
        }
    </script>
    <script>
        var Lockout = <?php echo isset($_SESSION['lockout']) ? json_encode($_SESSION['lockout']) : 'null'; ?>;
    </script>
    <script src="js/login_page.js"></script>


    <script>
        const container = document.getElementById('container');
        const registerBtn = document.getElementById('register');
        const loginBtn = document.getElementById('login');

        const loginAttemptBy = "<?php echo isset($_SESSION['login_attempt_by']) ? $_SESSION['login_attempt_by'] : ''; ?>";

        if (loginAttemptBy === "user") {
            container.classList.add("active");
        } else {
            container.classList.remove("active");
        }

        registerBtn.addEventListener('click', () => {
            container.classList.add("active");
        });

        loginBtn.addEventListener('click', () => {
            container.classList.remove("active");
        });

        document.getElementById("adminLogin").addEventListener("click", function(event) {
            setTimeout(function() {
                event.preventDefault(); // Prevents form submission
            }, 1000);
        });
        document.getElementById("userLogin").addEventListener("click", function(event) {
            setTimeout(function() {
                event.preventDefault(); // Prevents form submission
            }, 1000);
        });
    </script>
</body>

</html>