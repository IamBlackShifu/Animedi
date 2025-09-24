<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor') {
    header("Location: ../login/index.php");
    exit();
}
include("../includes/dbconn.php");

if (isset($_GET['id'])) {
    $appointment_id = $_GET['id'];
    $stmt = $conn->prepare("UPDATE appointments SET status = 'in_progress' WHERE id = ? AND doctor_id = ?");
    $stmt->bind_param("ii", $appointment_id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
}

header("Location: dashboard.php");
exit();
?>