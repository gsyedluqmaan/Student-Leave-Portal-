<?php
session_start();
include "PHP/connect.php";  // Include your database connection file

// Fetch class coordinators from users table for approver dropdown
$approverQuery = "SELECT id, name FROM users WHERE role = 'class_coordinator'";
$approverResult = $conn->query($approverQuery);
$approvers = [];
if ($approverResult->num_rows > 0) {
    while ($row = $approverResult->fetch_assoc()) {
        $approvers[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $id = $_POST['id'];
    $name = $_POST['name'];
    $department = $_POST['department'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone_no = $_POST['phone_no'];
    $dob = $_POST['dob'];
    $role = $_POST['role'];
    $class = $_POST['class'] ?? null;
    $approver = $_POST['approver'] ?? null;
    $semester = $_POST['semester'] ?? null;

    // Hash the password before storing it

    // SQL query to insert the data into the database
    $sql = "INSERT INTO users (id, name, department, email, password, phone_no, dob, role, class, approver, semester) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssssss", $id, $name, $department, $email, $password, $phone_no, $dob, $role, $class, $approver, $semester);

    if ($stmt->execute()) {
        echo "<script>alert('User registered successfully!'); window.location.href = 'login.php';</script>";
    } else {
        echo "<script>alert('Error registering user. Please try again.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="CSS/signup.css">

</head>
<style>
    body {
        overflow: hidden;
        background-image: url('mainbg.svg');
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-position: center;
    }
</style>

<body>
    <div class="form-container">
        <form class="form" method="POST" action="">
            <div class="form-header">
                <p class="title">Register</p>
                <p class="message">Signup now and get full access to our app.</p>
            </div>

            <div class="form-fields">
                <!-- Left Column -->
                <div class="form-column">
                    <label>
                        <input type="text" name="id" required class="input">
                        <span>ID</span>
                    </label>

                    <label>
                        <input type="text" name="name" required class="input">
                        <span>Name</span>
                    </label>

                    <label>
                        <select name="department" required class="input">
                            <option value="">Select Department</option>
                            <option value="CSE">CSE</option>
                            <option value="ISE">ISE</option>
                            <option value="ECE">ECE</option>
                            <option value="EEE">EEE</option>
                            <option value="CIVIL">CIVIL</option>
                        </select>
                        <span>Department</span>
                    </label>

                    <label>
                        <input type="email" name="email" required class="input">
                        <span>Email</span>
                    </label>

                    <label>
                        <input type="password" name="password" required class="input">
                        <span>Password</span>
                    </label>

                    <label>
                        <input type="password" name="confirm_password" required class="input">
                        <span>Confirm Password</span>
                    </label>
                </div>

                <!-- Right Column -->
                <div class="form-column">
                    <label>
                        <input type="tel" name="phone_no" required class="input" pattern="[0-9]{10}">
                        <span>Phone Number</span>
                    </label>

                    <label>
                        <input type="date" name="dob" required class="input">
                        <span>Date of Birth</span>
                    </label>

                    <label>
                        <select name="role" id="role" required class="input" onchange="toggleApproverVisibility()">
                            <option value="">Select Role</option>
                            <option value="student">Student</option>
                            <option value="class_coordinator">Class Coordinator</option>
                            <option value="hod">HOD</option>
                        </select>
                        <span>Role</span>
                    </label>

                    <label>
                        <input type="text" name="class" class="input">
                        <span>Class (Optional)</span>
                    </label>

                    <label id="approverLabel">
                        <select name="approver" class="input">
                            <option value="">Select Approver</option>
                            <?php foreach ($approvers as $approver): ?>
                                <option value="<?php echo htmlspecialchars($approver['id']); ?>">
                                    <?php echo htmlspecialchars($approver['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span>Approver (optional)</span>
                    </label>

                    <label>
                        <input type="text" name="semester" class="input">
                        <span>Semester (Optional)</span>
                    </label>
                </div>
            </div>

            <div class="form-footer">
                <button class="submit" type="submit">Submit</button>
                <p class="signin">Already have an account? <a href="login.php">Signin</a></p>
            </div>
        </form>
    </div>
</body>

</html>