<?php
session_start();
if (!isset($_SESSION['eid_reset_password'])) {
    header('Location: reset_password.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter OTP</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .resend-otp {
            background-color: transparent;
            border: none;
            color: blue;
            cursor: pointer;
        }

        .resend-otp:hover {
            text-decoration: underline !important;
            color: rgb(0, 0, 199);
        }

        .countdown {
            color: grey;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
            <h4 class="text-center mb-4">Enter OTP</h4>
            <form action="verify_otp.php" method="POST">
                <div class="mb-3">
                    <label for="otp" class="form-label">One-Time Password</label>
                    <input type="text" class="form-control" id="otp" name="otp" placeholder="Enter OTP" maxlength="6" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
            <form id="resend-otp-form" method="POST" class="mt-3 text-center">
                <input type="hidden" name="eid" value="<?php echo htmlspecialchars($_SESSION['eid_reset_password']); ?>">
                <button type="button" id="resend-otp" class="resend-otp d-none">Resend OTP</button>
                <span id="countdown-timer" class="countdown"></span>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {
            var mailSentTime = <?php echo isset($_SESSION['time']) ? $_SESSION['time'] : 'null'; ?>;
            var currentTime = Math.floor(Date.now() / 1000); // Get current time in seconds
            var timePassed = currentTime - mailSentTime;
            var remainingTime = 60 - timePassed;

            var $resendOtpButton = $('#resend-otp');
            var $countdownTimer = $('#countdown-timer');

            if (mailSentTime && remainingTime > 0) {
                startCountdown($resendOtpButton, $countdownTimer, remainingTime);
            } else {
                $resendOtpButton.removeClass('d-none');
            }

            $('#resend-otp').click(function () {
                var eid = $("input[name='eid']").val();
                $.ajax({
                    url: 'send_reset_mail.php',
                    type: 'POST',
                    data: { eid: eid },
                    success: function(response) {
                        $_SESSION['time']
                        $.post('store_time.php', { current_time: Math.floor(Date.now() / 1000) }, function() {
                            startCountdown($resendOtpButton, $countdownTimer, 60);
                        });
                    },
                    error: function() {
                        console.error('An error occurred while resending the OTP.');
                    }
                });
            });

            function startCountdown(button, timerElement, countdown) {
                button.addClass('d-none');
                timerElement.removeClass('d-none').text(`Resend available in ${countdown} seconds`);

                var timerInterval = setInterval(function () {
                    countdown--;
                    timerElement.text(`Resend available in ${countdown} seconds`);

                    if (countdown <= 0) {
                        clearInterval(timerInterval);
                        button.removeClass('d-none');
                        timerElement.addClass('d-none');
                    }
                }, 1000);
            }
        });
    </script>
</body>
</html>
