<?php
session_start();
include "PHP/connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];
    $approver_name = $_SESSION['name'];

    // First check if this request should be handled by this approver
    $check_sql = "SELECT duration FROM leave_requests WHERE id = $request_id";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        $row = $check_result->fetch_assoc();
        $duration = $row['duration'];

        $can_approve = false;
        if ($approver_id == 389 && $duration > 2) {
            $can_approve = true;  // HOD can approve >2 days
        } elseif (($approver_id == 320 || $approver_id == 390) && $duration <= 2) {
            $can_approve = true;  // CC can approve <=2 days
        }

        if ($can_approve) {
            $status = ($action == 'approve') ? 'Approved' : 'Rejected';

            $sql = "UPDATE leave_requests 
                    SET status = '$status', 
                        reviewed_by = $approver_name, 
                        approved_at = NOW() 
                    WHERE id = $request_id";

            if ($conn->query($sql)) {
                header("Location: view_requests.php?msg=success");
            } else {
                header("Location: view_requests.php?msg=error");
            }
        } else {
            header("Location: view_requests.php?msg=unauthorized");
        }
    }
    exit();
}
?>