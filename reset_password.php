<?php
session_start();
include "PHP/connect.php";

// Check if user is verified via OTP and user_id exists in session
if (!isset($_SESSION['user_id'])) {
    header("Location: forgot_pass.php");
    exit;
}

// Debugging: Check if user_id is set in the session
// echo "User ID from session: " . $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Use correct session variable for user_id
    $user_id = $_SESSION['user_id']; // Make sure you're using the correct session variable
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // First, get the current password from database using user_id
    $sql = "SELECT password FROM users WHERE id = ?";  // Ensure correct column name
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);  // "i" for integer user_id
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $stored_password = $user['password'];

        // Verify if current password matches
        if ($current_password == $stored_password) {
            // Check if new password and confirm password match
            if ($new_password == $confirm_password) {
                // Update password in database using user_id
                $update_sql = "UPDATE users SET password = ? WHERE id = ?";  // Ensure correct column name
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("si", $new_password, $user_id);  // "si" for string password and integer user_id
                
                if ($update_stmt->execute()) {
                    // Clear the OTP verification session
                    unset($_SESSION['otp_verified']);
                    echo "<script>
                        alert('Password updated successfully!');
                        window.location.href = 'login.php';
                    </script>";
                    exit;
                } else {
                    echo "<script>alert('Error updating password. Please try again.');</script>";
                }
            } else {
                echo "<script>alert('New password and confirm password do not match!');</script>";
            }
        } else {
            echo "<script>alert('Current password is incorrect!');</script>";
        }
    } else {
        echo "<script>alert('User not found!');</script>";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="CSS/forgot_pass.css">
    <style>
        .password-requirements {
            font-size: 0.8em;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <div class="logo-container">
            Reset Password
        </div>

        <form class="form" method="POST" action="" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input 
                    type="password" 
                    id="current_password" 
                    name="current_password" 
                    required=""
                >
            </div>

            <div class="form-group">
                <label for="new_password">New Password</label>
                <input 
                    type="password" 
                    id="new_password" 
                    name="new_password" 
                    required=""
                    pattern=".{8,}"  >
                <div class="password-requirements">
                    Password must contain:
                    <ul>
                        <li>At least 8 characters</li>
                    </ul>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    required=""
                >
            </div>

            <button class="form-submit-btn" type="submit">Reset Password</button>
        </form>

        <!-- Added debug information -->
        <?php if(isset($_SESSION['user_id'])): ?>
            <div style="display:none;">User ID: <?php echo $_SESSION['user_id']; ?></div>
        <?php endif; ?>
    </div>

    <script>
        function validateForm() {
            var newPass = document.getElementById('new_password').value;
            var confirmPass = document.getElementById('confirm_password').value;
            
            // Check if passwords match
            if (newPass !== confirmPass) {
                alert("New password and confirm password do not match!");
                return false;
            }

            // Check password requirements (just length check now)
            if (newPass.length < 8) {
                alert("Password must be at least 8 characters long!");
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
