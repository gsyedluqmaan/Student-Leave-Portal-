<?php
session_start();
include "PHP/navbar.php";
include "PHP/connect.php";
global $conn;
// Base class for common database operations
class LeaveRequestBase {
    protected $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    protected function executeQuery($sql, $params) {
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }
}

// Child class to handle leave requests
class LeaveRequestHandler extends LeaveRequestBase {
    public $success_msg = '';
    public $error_msg = '';

    public function handleRequest() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $student_id = $_SESSION['user_id'];
            $leave_type = $_POST['leave_type'];
            $leave_reason = $_POST['reason'];
            $leave_from = $_POST['leave_from'];
            $leave_to = $_POST['leave_to'];
            $class = $_SESSION['class'];

            // Calculate leave duration
            $duration = $this->calculateDuration($leave_from, $leave_to);

            // Insert leave request
            $sql = "INSERT INTO leave_requests (student_id, leave_type, leave_reason, leave_from, leave_to, status, created_at, duration, class) 
                    VALUES (?, ?, ?, ?, ?, 'Pending', NOW(), ?, ?)";

            if ($this->executeQuery($sql, [$student_id, $leave_type, $leave_reason, $leave_from, $leave_to, $duration, $class])) {
                $this->success_msg = "Leave request submitted successfully!";
            } else {
                $this->error_msg = "Error submitting leave request.";
            }
        }
    }

    private function calculateDuration($leave_from, $leave_to) {
        $start = new DateTime($leave_from);
        $end = new DateTime($leave_to);
        $duration = 0;

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($start, $interval, $end->modify('+1 day'));

        foreach ($period as $dt) {
            $curr = $dt->format('N'); // 1 = Monday, ..., 7 = Sunday
            if ($curr < 6) { // Exclude Saturday (6) and Sunday (7)
                $duration++;
            }
        }

        return $duration;
    }
}

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['name']) || !isset($_SESSION['class'])) {
    header("Location: login.php");
    exit();
}

// Create an instance of LeaveRequestHandler and process the form submission
$leaveRequestHandler = new LeaveRequestHandler($conn);
$leaveRequestHandler->handleRequest();
?>




<?php if (!empty($leaveRequestHandler->error_msg)) { ?>
    <div class="error-message"><?= $leaveRequestHandler->error_msg; ?></div>
<?php } ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request Form | Accura</title>
    <link rel="stylesheet" href="CSS/form.css">
    <style>
        .success-msg {
            color: green;
            margin-bottom: 15px;
        }

        .error-msg {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php if ($leaveRequestHandler->success_msg): ?>
        <script>
            alert("<?php echo htmlspecialchars($leaveRequestHandler->success_msg); ?>");
        </script>
    <?php endif; ?>

    <div class="container">
        <div class="text">Leave Request Form</div>

        <?php if ($leaveRequestHandler->success_msg): ?>
            <div class="success-msg"><?php echo htmlspecialchars($leaveRequestHandler->success_msg); ?></div>
        <?php endif; ?>

        <?php if ($leaveRequestHandler->error_msg): ?>
            <div class="error-msg"><?php echo htmlspecialchars($leaveRequestHandler->error_msg); ?></div>
        <?php endif; ?>

        <form id="leaveRequestForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-row">
                <div class="input-data">
                    <select name="leave_type" required>
                        <option value="" disabled selected hidden></option>
                        <option value="Casual Leave">Casual Leave</option>
                        <option value="Sick Leave">Sick Leave</option>
                        <option value="Transport Leave">Transport Leave</option>
                    </select>
                    <label>Leave Type</label>
                </div>
            </div>

            <div class="form-row">
                <div class="input-data">
                    <input type="date" name="leave_from" required>
                    <label>Leave From</label>
                </div>
                <div class="input-data">
                    <input type="date" name="leave_to" required>
                    <label>Leave To</label>
                </div>
            </div>

            <div class="form-row">
                <div class="input-data textarea">
                    <textarea name="reason" required></textarea>
                    <label>Reason for Leave</label>
                </div>
            </div>

            <div class="submit-btn">
                <button type="submit" class="Btn">Submit Leave Request</button>
            </div>
        </form>
    </div>

    <script>
        // Simple client-side date validation
        document.getElementById('leaveRequestForm').onsubmit = function(e) {
            const leaveFrom = new Date(document.querySelector('input[name="leave_from"]').value);
            const leaveTo = new Date(document.querySelector('input[name="leave_to"]').value);

            if (leaveTo < leaveFrom) {
                e.preventDefault();
                alert('Leave To date cannot be before Leave From date');
            }
        };

        // Set minimum date as today
        const today = new Date().toISOString().split('T')[0];
        document.querySelector('input[name="leave_from"]').setAttribute('min', today);
        document.querySelector('input[name="leave_to"]').setAttribute('min', today);
    </script>
</body>
</html>
