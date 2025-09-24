<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'reception') {
    header("Location: ../login/index.php");
    exit();
}
include("../includes/dbconn.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $owner_name = $_POST['owner_name'];
    $owner_contact = $_POST['owner_contact'];
    $patient_name = $_POST['patient_name'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $notes = $_POST['notes'];

    // Insert owner
    $stmt = $conn->prepare("INSERT INTO owners (name, contact) VALUES (?, ?)");
    $stmt->bind_param("ss", $owner_name, $owner_contact);
    $stmt->execute();
    $owner_id = $conn->insert_id;
    $stmt->close();

    // Insert patient
    $stmt = $conn->prepare("INSERT INTO patients (owner_id, name, species, breed, age, notes) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssis", $owner_id, $patient_name, $species, $breed, $age, $notes);
    $stmt->execute();
    $stmt->close();

    header("Location: dashboard.php?success=Patient registered successfully");
    exit();
}
?>