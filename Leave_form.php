<?php
session_start();
include "PHP/navbar.php";

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['name']) || !isset($_SESSION['class']) || !isset($_SESSION['sem'])) {
    header("Location: login.php");
    exit;
}

include "PHP/connect.php";

// Fetch leave status for the current user
$leave_status = [];
if (isset($_SESSION['user_id'])) {
    $student_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM leave_requests WHERE student_id = $student_id ORDER BY created_at DESC";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['leave_from'] = date("d-m-Y", strtotime($row['leave_from']));
            $row['leave_to'] = date("d-m-Y", strtotime($row['leave_to']));
            $leave_status[] = $row;

        }
    }
}

// Initialize message variables
$success_msg = '';
$error_msg = '';

// Process leave form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_SESSION['user_id'];
    $leave_type = $_POST['leave_type'];
    $leave_reason = $_POST['reason'];
    $leave_from = $_POST['leave_from'];
    $leave_to = $_POST['leave_to'];
    $class = $_SESSION['class'];
    $sem = $_SESSION['sem'];

    // Calculate duration (excluding weekends)
    $start = new DateTime($leave_from);
    $end = new DateTime($leave_to);
    $duration = 0;
    $interval = DateInterval::createFromDateString('1 day');
    $period = new DatePeriod($start, $interval, $end->modify('+1 day'));

    foreach ($period as $dt) {
        $curr = $dt->format('N');
        if ($curr < 6) {
            $duration++;
        }
    }

    // Insert leave request into database
    $stmt = $conn->prepare("INSERT INTO leave_requests 
    (student_id, leave_type, leave_reason, leave_from, leave_to, status, created_at, duration, class, semester) 
    VALUES (?, ?, ?, ?, ?, 'Pending', NOW(), ?, ?, ?)");

    // Bind parameters
    $stmt->bind_param("issssiss", $student_id, $leave_type, $leave_reason, $leave_from, $leave_to, $duration, $class, $sem);

    // Execute the statement
    $stmt->execute();

    if ($conn->query($sql)) {
        $success_msg = "Leave request submitted successfully!";
    } else {
        $error_msg = "Error submitting leave request";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request Form | Accura</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/form.css">
    <style>
        body {
            background-image: url('mainbg1.svg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            padding: 2rem;
        }

        .text {
            font-size: 1.875rem;
            font-weight: 600;
            color: #1a2b4b;
            margin-bottom: 2rem;
            text-align: center;
        }

        /* Message Styles */
        .success-msg,
        .error-msg {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            font-weight: 500;
        }

        .success-msg {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .error-msg {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Status Button */
        .status-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #3b82f6;
            color: white;
            padding: 0.75rem 1.25rem;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
        }

        .status-btn:hover {
            background-color: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        .status-btn:active {
            transform: translateY(0);
        }

        /* Status Slider */
        .status-slider {
            position: fixed;
            top: 0;
            right: -400px;
            width: 380px;
            height: 100%;
            background-color: white;
            box-shadow: -4px 0 25px -5px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            overflow-y: auto;
            z-index: 1000;
            transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .status-slider.open {
            right: 0;
        }

        /* Leave Cards */
        .leave-card {
            margin-bottom: 1.5rem;
            padding: 1.25rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .leave-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        .leave-card h3 {
            margin: 0 0 1rem;
            font-size: 1.25rem;
            font-weight: 600;
            color: #1a2b4b;
        }

        .leave-card p {
            margin: 0.5rem 0;
            font-size: 0.875rem;
            color: #4b5563;
            line-height: 1.5;
        }

        .leave-card p strong {
            color: #1f2937;
            font-weight: 500;
        }

        .leave-card p small {
            font-size: 0.75rem;
            color: #6b7280;
        }

        /* Status Colors */
        .status-pending {
            background-color: #fef3c7;
            border: 1px solid #fcd34d;
        }

        .status-approved {
            background-color: #d1fae5;
            border: 1px solid #6ee7b7;
        }

        .status-rejected {
            background-color: #fee2e2;
            border: 1px solid #fca5a5;
            color: #1f2937;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
                margin: 0.5rem;
            }

            .status-btn {
                position: fixed;
                bottom: 1.5rem;
                right: 1.5rem;
                top: auto;
                z-index: 1001;
            }

            .status-slider {
                width: 100%;
                right: -100%;
            }

            .text {
                font-size: 1.5rem;
                margin-bottom: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- View Leave Status Button -->
        <button type="button" id="showStatusBtn" class="status-btn"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" class="icon">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12 6 12 12 16 14" />
            </svg></button>

        <!-- Leave Status Slider -->
        <div id="statusSlider" class="status-slider">
            <?php if (!empty($leave_status)): ?>
                <?php foreach ($leave_status as $leave): ?>
                    <div class="leave-card status-<?php echo strtolower($leave['status']); ?>">
                        <h3><?php echo htmlspecialchars($leave['leave_type']); ?></h3>
                        <p><strong>From:</strong> <?php echo htmlspecialchars($leave['leave_from']); ?></p>
                        <p><strong>To:</strong> <?php echo htmlspecialchars($leave['leave_to']); ?></p>
                        <p><strong>No. of days:</strong> <?php echo htmlspecialchars($leave['duration']); ?></p>
                        <p><strong>Reason:</strong> <?php echo htmlspecialchars($leave['leave_reason']); ?></p>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($leave['status']); ?></p>
                        <p><small><strong>Requested On:</strong> <?php echo htmlspecialchars($leave['created_at']); ?></small>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No leave requests found.</p>
            <?php endif; ?>
        </div>

        <div class="text">Leave Request Form</div>

        <?php if ($success_msg): ?>
            <div class="success-msg"><?php echo htmlspecialchars($success_msg); ?></div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error_msg); ?></div>
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
                    <input type="date" name="leave_from" id="leave_from" required>
                    <label>Leave From</label>
                </div>
                <div class="input-data">
                    <input type="date" name="leave_to" id="leave_to" required>
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
        const showStatusBtn = document.getElementById('showStatusBtn');
        const statusSlider = document.getElementById('statusSlider');

        // Function to open slider
        const openSlider = () => {
            statusSlider.classList.add('open');
        };

        // Function to close slider
        const closeSlider = () => {
            statusSlider.classList.remove('open');
        };

        // Toggle slider when button is clicked
        showStatusBtn.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevent event from bubbling to document
            if (statusSlider.classList.contains('open')) {
                closeSlider();
            } else {
                openSlider();
            }
        });

        // Close slider when clicking outside
        document.addEventListener('click', (e) => {
            if (statusSlider.classList.contains('open') &&
                !statusSlider.contains(e.target) &&
                e.target !== showStatusBtn) {
                closeSlider();
            }
        });

        // Prevent clicks inside slider from closing it
        statusSlider.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        const today = new Date().toISOString().split('T')[0];

        // Set the min attribute for leave_from and leave_to
        const leaveFromInput = document.getElementById('leave_from');
        const leaveToInput = document.getElementById('leave_to');

        leaveFromInput.setAttribute('min', today);
        leaveToInput.setAttribute('min', today);

        // Optional: Ensure leave_to cannot be before leave_from
        leaveFromInput.addEventListener('change', () => {
            leaveToInput.setAttribute('min', leaveFromInput.value);
        });
    </script>
</body>

</html>