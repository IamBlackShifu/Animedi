<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'reception') {
    header("Location: ../login/index.php");
    exit();
}
include("../includes/dbconn.php");

$appointment_id = $_GET['id'] ?? 0;

if ($appointment_id > 0) {
    // Update appointment status to cancelled
    $stmt = $conn->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ?");
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $stmt->close();

    header("Location: appointments.php?success=Appointment cancelled successfully");
    exit();
} else {
    header("Location: appointments.php?error=Invalid appointment ID");
    exit();
}
?>