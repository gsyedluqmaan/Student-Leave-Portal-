<?php
session_start();
include "PHP/connect.php"; // Ensure this connects to your database

// Initialize error message variable
$error_msg = '';

// Function to get demo credentials
function getCredentials($conn)
{
    $credentials = [];
    $query = "SELECT id, name, role, password, class FROM users";
    $result = $conn->query($query);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $credentials[] = $row;
        }
    }
    return $credentials;
}

// Get credentials for the tooltip
$demo_credentials = getCredentials($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id']) && isset($_POST['password'])) {
        $id = trim($_POST['id']);
        $password = trim($_POST['password']);

        if (!empty($id) && !empty($password)) {
            // Prepare the SQL query
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            if ($stmt === false) {
                $error_msg = 'Database error: ' . htmlspecialchars($conn->error);
            } else {
                // Bind parameters and execute
                $stmt->bind_param("s", $id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    if ($password === $row['password']) {
                        $_SESSION['user_id'] = $id;
                        $_SESSION['class'] = $row['class'];
                        $_SESSION['role'] = $row['role'];
                        $_SESSION['name'] = $row['name'];
                        $_SESSION['sem'] = $row['semester'];

                        switch ($row['role']) {
                            case 'student':
                                header("Location: student_dashboard.php");
                                exit();
                            case 'class_coordinator':
                                header("Location: student_dashboard.php");
                                exit();
                            case 'HOD':
                                header("Location: student_dashboard.php");
                                exit();
                            default:
                                $error_msg = "Invalid role assigned";
                        }
                    } else {
                        $error_msg = "Invalid password";
                    }
                } else {
                    $error_msg = "Invalid ID";
                }
                $stmt->close();
            }
        } else {
            $error_msg = "Please fill in all fields";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login System</title>
    <link rel="stylesheet" href="CSS/Login.css">
    <style>
        /* Your existing Login.css styles can go here or be linked externally */

        /* Demo Tooltip Styles */
        .demo-tooltip {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            font-family: Arial, sans-serif;
        }

        .demo-tooltip-trigger {
            background: #9e80df;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .demo-tooltip-trigger:hover {
            background: #8a6cd4;
        }

        .demo-tooltip-content {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            width: 300px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            margin-top: 10px;
            padding: 15px;
            max-height: 400px;
            overflow-y: auto;
        }

        .demo-tooltip-content h3 {
            margin: 0 0 15px 0;
            color: #333;
            text-align: center;
        }

        .credential-item {
            margin-bottom: 15px;
        }

        .credential-item p {
            margin: 5px 0;
            font-size: 14px;
        }

        .credential-item hr {
            border: none;
            border-top: 1px solid #eee;
            margin: 10px 0;
        }

        .demo-tooltip:hover .demo-tooltip-content {
            display: block;
        }

        /* Additional styles for the login form */
        .modal {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .modal-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            max-width: 900px;
            width: 100%;
        }

        .modal-left {
            padding: 40px;
            flex: 1;
        }

        .modal-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f8f8;
        }

        .modal-right img {
            max-width: 100%;
            height: auto;
        }

        .input-block {
            margin-bottom: 20px;
        }

        .input-block label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        .input-block input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .button {
            background: #9e80df;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .button:hover {
            background: #8a6cd4;
        }

        .error-message {
            color: red;
            margin-bottom: 10px;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #9e80df;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #7a5ec0;
            /* Darker version of #9e80df */
        }

        /* For Firefox */
        * {
            scrollbar-width: thin;
            scrollbar-color: #9e80df #f1f1f1;
        }

        /* For Edge and other browsers */
        ::-ms-scrollbar {
            width: 8px;
        }

        ::-ms-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-ms-scrollbar-thumb {
            background: #9e80df;
            border-radius: 4px;
        }

        ::-ms-scrollbar-thumb:hover {
            background: #7a5ec0;
        }
    </style>
    <link rel="stylesheet" href="CSS/Login.css">
</head>

<body>
    <!-- Demo Credentials Tooltip -->
    <div class="demo-tooltip">
        <div class="demo-tooltip-trigger">👤</div>
        <div class="demo-tooltip-content">
            <h3>Available Logins</h3>
            <div class="credentials-list">
                <?php foreach ($demo_credentials as $cred): ?>
                    <div class="credential-item">
                        <p><strong>ID:</strong> <?php echo htmlspecialchars($cred['id']); ?></p>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($cred['name']); ?></p>
                        <p><strong>Role:</strong> <?php echo htmlspecialchars($cred['role']); ?></p>
                        <p><strong>Password:</strong> <?php echo htmlspecialchars($cred['password']); ?></p>
                        <p><strong>Class:</strong> <?php echo htmlspecialchars($cred['class']); ?></p>
                        <hr>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="scroll-down">
    </div>

    <div class="modal">
        <div class="modal-container">
            <div class="modal-left">
                <h1 class="modal-title">Home!</h1>
                <p class="modal-desc">Please login with your credentials</p>
                <?php if (!empty($error_msg)): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error_msg); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="input-block">
                        <label for="id" class="input-label">ID</label>
                        <input type="text" name="id" id="id" placeholder="Enter your ID" required>
                    </div>
                    <div class="input-block">
                        <label for="password" class="input-label">Password</label>
                        <input type="password" name="password" id="password" placeholder="Password" required>
                    </div>
                    <div class="modal-buttons">
                        <a href="forgot_pass.php" style="font-size:14px">Forgot your password?</a>
                        <button class="button" style="--clr: #9e80df">
                            <span class="button__icon-wrapper">
                                <svg viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg"
                                    class="button__icon-svg" width="10">
                                    <path
                                        d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"
                                        fill="currentColor"></path>
                                </svg>

                                <svg viewBox="0 0 14 15" fill="none" width="10" xmlns="http://www.w3.org/2000/svg"
                                    class="button__icon-svg button__icon-svg--copy">
                                    <path
                                        d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"
                                        fill="currentColor"></path>
                                </svg>
                            </span>
                            Login
                        </button>
                    </div>
                </form>
                <p class="sign-up" style="font-size:14px; margin-top:10px;">Don't have an account? <a
                        href="signup.php">Sign up now</a></p>
            </div>
            <div class="modal-right">
                <img src="side.jpg" alt="placeholder">
            </div>
        </div>
    </div>

    <script>
        const scrollDown = document.querySelector(".scroll-down");
        const modal = document.querySelector(".modal");
        let modalTriggered = false;

        window.addEventListener("scroll", () => {
            const scrollPosition = window.scrollY;
            const triggerHeight = window.innerHeight / 3;

            if (scrollPosition > triggerHeight && !modalTriggered) {
                scrollDown.style.opacity = "0";
                scrollDown.style.visibility = "hidden";
                modal.classList.add("is-open");
                modalTriggered = true;
            }

            if (scrollPosition <= triggerHeight && modalTriggered) {
                scrollDown.style.opacity = "1";
                scrollDown.style.visibility = "visible";
                modal.classList.remove("is-open");
                modalTriggered = false;
            }
        });
    </script>
</body>

</html>