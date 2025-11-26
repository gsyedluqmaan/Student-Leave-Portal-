<?php
session_start();
include "PHP/connect.php";
include "PHP/navbar.php";

// Get current user's role/id
$current_user = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Fetch requests based on user role and class
if ($current_user == 389) {  // HOD
    $sql = "SELECT lr.*, s.name as student_name, s.department, s.semester 
            FROM leave_requests lr 
            JOIN users s ON lr.student_id = s.id 
            WHERE lr.status = 'Pending' 
            AND lr.duration > 3
            ORDER BY lr.created_at DESC";
} elseif ($current_user == 390) {  // CC for Class A
    $sql = "SELECT lr.*, s.name as student_name, s.department, s.semester 
            FROM leave_requests lr 
            JOIN users s ON lr.student_id = s.id 
            WHERE lr.status = 'Pending' 
            AND lr.duration <= 3
            AND lr.class = 'A' 
            ORDER BY lr.created_at DESC";
} elseif ($current_user == 320) {  // CC for Class B
    $sql = "SELECT lr.*, s.name as student_name, s.department, s.semester 
            FROM leave_requests lr 
            JOIN users s ON lr.student_id = s.id 
            WHERE lr.status = 'Pending' 
            AND lr.duration <= 3
            AND lr.class = 'B' 
            ORDER BY lr.created_at DESC";
} else {
    die("Unauthorized access");
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Requests | Accura</title>
    <script src="https://unpkg.com/feather-icons"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-image: url('mainbg.svg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center;
            color: #334155;
            padding: 2rem;
            min-height: 100vh;
        }

        .dashboard-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .dashboard-header h1 {
            font-size: 3rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 1.5rem;
            max-width: 1400px;
            margin: 0 0 0 120px;

        }

        .card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .card-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin-left: 0.75rem;
        }

        .info-row {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            font-size: 0.875rem;
        }

        .info-row i {
            width: 16px;
            height: 16px;
            margin-right: 0.5rem;
            color: #64748b;
        }

        .info-label {
            font-weight: 500;
            min-width: 100px;
            color: #64748b;
        }

        .info-value {
            color: #334155;
            font-weight: 400;
        }

        .card-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.875rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            flex: 1;
        }

        .btn i {
            margin-right: 0.375rem;
            width: 16px;
            height: 16px;
        }

        .btn-approve {
            background-color: #dcfce7;
            color: #166534;
        }

        .btn-approve:hover {
            background-color: #bbf7d0;
        }

        .btn-reject {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .btn-reject:hover {
            background-color: #fecaca;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            color: #64748b;
            grid-column: 1 / -1;
        }

        @media (max-width: 640px) {
            body {
                padding: 1rem;
            }

            .card-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-header">
        <h1>Leave Requests</h1>
    </div>

    <div class="card-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Format the leave_from and leave_to fields
                $leave_from = date("d-m-Y", strtotime($row['leave_from']));
                $leave_to = date("d-m-Y", strtotime($row['leave_to']));

                echo "<div class='card'>";
                echo "<div class='card-header'>";
                echo "<i data-feather='user'></i>";
                echo "<h3>" . htmlspecialchars($row['student_name']) . "</h3>";
                echo "</div>";

                echo "<div class='info-row'>";
                echo "<i data-feather='briefcase'></i>";
                echo "<span class='info-label'>Department:</span>";
                echo "<span class='info-value'>" . htmlspecialchars($row['department']) . "</span>";
                echo "</div>";

                echo "<div class='info-row'>";
                echo "<i data-feather='book'></i>";
                echo "<span class='info-label'>Semester:</span>";
                echo "<span class='info-value'>" . htmlspecialchars($row['semester']) . "</span>";
                echo "</div>";

                echo "<div class='info-row'>";
                echo "<i data-feather='tag'></i>";
                echo "<span class='info-label'>Leave Type:</span>";
                echo "<span class='info-value'>" . htmlspecialchars($row['leave_type']) . "</span>";
                echo "</div>";

                echo "<div class='info-row'>";
                echo "<i data-feather='calendar'></i>";
                echo "<span class='info-label'>From:</span>";
                echo "<span class='info-value'>" . htmlspecialchars($leave_from) . "</span>";
                echo "</div>";

                echo "<div class='info-row'>";
                echo "<i data-feather='calendar'></i>";
                echo "<span class='info-label'>To:</span>";
                echo "<span class='info-value'>" . htmlspecialchars($leave_to) . "</span>";
                echo "</div>";

                echo "<div class='info-row'>";
                echo "<i data-feather='clock'></i>";
                echo "<span class='info-label'>Duration:</span>";
                echo "<span class='info-value'>" . htmlspecialchars($row['duration']) . " days</span>";
                echo "</div>";

                echo "<div class='info-row'>";
                echo "<i data-feather='file-text'></i>";
                echo "<span class='info-label'>Reason:</span>";
                echo "<span class='info-value'>" . htmlspecialchars($row['leave_reason']) . "</span>";
                echo "</div>";

                echo "<div class='info-row'>";
                echo "<i data-feather='clock'></i>";
                echo "<span class='info-label'>Submitted:</span>";
                echo "<span class='info-value'>" . htmlspecialchars($row['created_at']) . "</span>";
                echo "</div>";

                echo "<form action='PHP/update_leave.php' method='post' class='card-actions'>";
                echo "<input type='hidden' name='request_id' value='" . $row['id'] . "'>";
                echo "<button type='submit' name='action' value='approve' class='btn btn-approve'>";
                echo "<i data-feather='check'></i>Approve</button>";
                echo "<button type='submit' name='action' value='reject' class='btn btn-reject'>";
                echo "<i data-feather='x'></i>Reject</button>";
                echo "</form>";
                echo "</div>";
            }
        } else {
            echo "<div class='empty-state'>";
            echo "<i data-feather='inbox' style='width: 48px; height: 48px; margin-bottom: 1rem;'></i>";
            echo "<p>No pending leave requests found.</p>";
            echo "</div>";
        }
        ?>
    </div>

    <script>
        feather.replace();
    </script>
</body>

</html>