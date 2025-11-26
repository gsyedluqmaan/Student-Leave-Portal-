<?php
session_start();
include "PHP/connect.php"; // Include your database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: forgot_pass.php");
    exit;
}

// If the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch OTP input from form
    $entered_otp = $_POST['otp'];
    $user_id = $_SESSION['user_id']; // Get user_id from session
    
    // Query the database to fetch the OTP and created_at timestamp for the user
    $sql = "SELECT otp, created_at FROM user_actions WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($stored_otp, $created_at);

    if ($stmt->num_rows > 0) {
        // If OTP exists, get its value and creation time
        $stmt->fetch();
        
        // Convert the created_at timestamp to a DateTime object
        $otp_creation_time = new DateTime($created_at);
        $current_time = new DateTime();
        
        // Calculate time difference in minutes
        $time_diff = ($current_time->getTimestamp() - $otp_creation_time->getTimestamp()) / 60;
        
        // Debug information
        echo "<script>console.log('OTP Creation Time: " . $otp_creation_time->format('Y-m-d H:i:s') . "');</script>";
        echo "<script>console.log('Current Time: " . $current_time->format('Y-m-d H:i:s') . "');</script>";
        echo "<script>console.log('Time Difference (minutes): " . $time_diff . "');</script>";
        echo "<script>console.log('Stored OTP: " . $stored_otp . "');</script>";
        echo "<script>console.log('Entered OTP: " . $entered_otp . "');</script>";
        
        // If the OTP is older than 5 minutes, do not allow reset
        if ($time_diff > 5) {
            echo "<script>alert('OTP has expired. Please request a new one.');</script>";
        } else {
            // Verify if entered OTP matches stored OTP
            if ($entered_otp == $stored_otp) {
                $_SESSION['otp_verified'] = true;
                header("Location: reset_password.php");
                exit;
            } else {
                echo "<script>alert('Invalid OTP. Please try again.');</script>";
            }
        }
    } else {
        echo "<script>alert('No OTP found. Please request a new OTP.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter OTP</title>
    <link rel="stylesheet" href="CSS/forgot_pass.css">
</head>

<body>
    <div class="form-container">
        <div class="logo-container">
            Enter OTP
        </div>

        <form class="form" method="POST" action="">
            <div class="form-group">
                <label for="otp">Enter OTP</label>
                <input
                    type="text"
                    id="otp"
                    name="otp"
                    placeholder="Enter 6-digit OTP"
                    pattern="[0-9]{6}"
                    maxlength="6"
                    required=""
                >
            </div>

            <button class="form-submit-btn" type="submit">Verify OTP</button>
        </form>

        <p class="signup-link">
            Didn't receive OTP?
            <a href="forgot_pass.php" class="signup-link link">Request new OTP</a>
        </p>
    </div>
</body>
</html>