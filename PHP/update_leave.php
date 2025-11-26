<?php
session_start();
include "connect.php";

// Check if request ID and action are set
if (isset($_POST['request_id']) && isset($_POST['action'])) {
    $request_id = intval($_POST['request_id']);
    $action = $_POST['action'];
    
    // Determine the new status based on the action
    if ($action === 'approve') {
        $new_status = 'Approved';
    } elseif ($action === 'reject') {
        $new_status = 'Rejected';
    } else {
        die("Invalid action");
    }
    
    // Update the leave request status in the database
    $stmt = $conn->prepare("UPDATE leave_requests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $request_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Leave request has been $new_status successfully.'); window.location.href = '../cc_status.php';</script>";
    } else {
        echo "<script>alert('Error updating leave request.'); window.location.href = '../cc_status.php';</script>";
    }
    
    $stmt->close();
} else {
    echo "<script>alert('Invalid request.'); window.location.href = '../cc_status.php';</script>";
}

$conn->close();
?>
