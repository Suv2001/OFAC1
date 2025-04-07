<?php
include("../templates/session_management.php");
include("db_connection.php"); // Ensure this file initializes $conn

$token = $_GET['token'] ?? '';

$message = '';
$showOtpForm = false; 
$showCloseButton = false; // Initialize button visibility
$showCountdown = false;   // Initialize countdown visibility

$query = "SELECT * FROM otp WHERE verification_token = ? AND FLAG = 0";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $message = "Token verified successfully. Please enter your OTP.";
    $showOtpForm = true;
} else {
    $message = "Invalid or expired token.";
    header("refresh:5;url=forgot_pass.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $otp = $_POST['otp'] ?? '';

    $query = "SELECT otp_code, otp_expiry, eid FROM otp WHERE verification_token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if ($otp === $row['otp_code'] && strtotime($row['otp_expiry']) > time()) {
            $updateQuery = "UPDATE otp SET FLAG = 1 WHERE verification_token = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("s", $token);
            $updateStmt->execute();

            // Success message with countdown
            $message = "OTP verified successfully. Have a good day!";
            $showCloseButton = true;
            $showCountdown = true;  // Show countdown message
            $showOtpForm = false;
        } else {
            $message = "Invalid OTP or OTP has expired.";
        }
    } else {
        $message = "Invalid or expired token. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Verify OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to left, #cdd1df, #354fb7);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            text-align: center;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h3 {
            color: #333;
        }
        input {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px 20px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>

    <script>
        function startCountdown() {
            let timeLeft = 5;
            let countdownElement = document.getElementById("countdown");

            let countdownInterval = setInterval(function () {
                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    window.close(); // Close the tab after 5 seconds
                } else {
                    countdownElement.innerText = `Instead, the browser tab will automatically close in ${timeLeft} sec(s).`;
                    timeLeft--;
                }
            }, 1000);
        }

        // Start countdown when the page loads if applicable
        window.onload = function() {
            <?php if ($showCountdown): ?>
                startCountdown();
            <?php endif; ?>
        };
    </script>
</head>
<body>
    <div class="container">
        <h3><?php echo $message; ?></h3>

        <?php if ($showOtpForm): ?>
            <form method="POST" action="verify_otp.php?token=<?php echo htmlspecialchars($token); ?>">
                <input type="text" name="otp" placeholder="Enter OTP" required>
                <button type="submit">Verify OTP</button>
            </form>
        <?php endif; ?>

        <?php if ($showCloseButton): ?>
            <button onclick="window.close()">Close Tab</button>
            <p id="countdown"></p>

<script>
    function startCountdown() {
        let timeLeft = 5; // Set the countdown time in seconds
        let countdownElement = document.getElementById("countdown");

        function updateCountdown() {
            countdownElement.innerText = `Instead, the browser tab will automatically close in ${timeLeft} sec(s).`;
            timeLeft--;

            if (timeLeft < 0) {
                clearInterval(countdownInterval);
                window.close(); // Close the tab after 5 seconds
            }
        }

        // Initial call to display the first message immediately
        updateCountdown();

        // Start the interval to update countdown every second
        let countdownInterval = setInterval(updateCountdown, 1000);
    }

    // Start countdown when the page loads
    window.onload = function() {
        startCountdown();
    };
</script>

        <?php endif; ?>
    </div>
</body>
</html>
