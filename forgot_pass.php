<?php
session_start();  // Start the session to store session variables

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include "PHP/connect.php"; // Database connection

    $email = $_POST['email'];
    $student_id = $_POST['studentid'];

    // Store student_id as user_id and email in session
    $_SESSION['user_id'] = $student_id;  // Changed this line to store as user_id
    $_SESSION['email'] = $email;

    // Generate a 6-digit OTP
    $otp = rand(100000, 999999);

    // Store in database
    $sql = "INSERT INTO user_actions (user_id, otp, email) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $student_id, $otp, $email);

    if ($stmt->execute()) {
        // Redirect to the email sending process
        echo "<form id='sendMail' action='PHP/email.php' method='POST'>";
        echo "<input type='hidden' name='email' value='$email'>";
        echo "<input type='hidden' name='otp' value='$otp'>";
        echo "<input type='hidden' name='student_id' value='$student_id'>";
        echo "</form>";
        echo "<script>document.getElementById('sendMail').submit();</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="CSS/forgot_pass.css">
</head>
<body>
<div class="form-container">
    <div class="logo-container">Forgot Password</div>

    <form class="form" method="POST">
        <div class="form-group">
            <label for="studentid">Student ID</label>
            <input 
                type="text" 
                id="studentid" 
                name="studentid" 
                placeholder="Enter your student ID" 
                required
                pattern="[0-9]+"  
            >
            <label for="email">Email</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                placeholder="Enter your email" 
                required
            >
        </div>
        <button class="form-submit-btn" type="submit">Send Email</button>
    </form>

    <!-- Added back to login link -->
    <p class="signup-link">
        Remember your password?
        <a href="login.php" class="signup-link link">Back to Login</a>
    </p>
</div>
</body>
</html>