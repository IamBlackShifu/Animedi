<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'reception') {
    header("Location: ../login/index.php");
    exit();
}
include("../includes/dbconn.php");

if (isset($_GET['id'])) {
    $billing_id = $_GET['id'];
    $stmt = $conn->prepare("UPDATE billing SET status = 'paid', payment_date = NOW() WHERE id = ?");
    $stmt->bind_param("i", $billing_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: billing.php");
exit();
?>