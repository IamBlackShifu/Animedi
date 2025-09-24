<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'reception') {
    header("Location: ../login/index.php");
    exit();
}
include("../includes/dbconn.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VetCare Pro - Queue Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { background: #343a40; color: white; min-height: 100vh; }
        .sidebar .nav-link { color: #adb5bd; }
        .sidebar .nav-link:hover { color: white; }
        .card { border: none; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .status-scheduled { background-color: #fff3cd; }
        .status-in_progress { background-color: #cce5ff; }
        .status-completed { background-color: #d1ecf1; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-3">
                <h4 class="text-center mb-4">VetCare Pro</h4>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="patients.php"><i class="fas fa-paw me-2"></i>Patients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="appointments.php"><i class="fas fa-calendar me-2"></i>Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="queue.php"><i class="fas fa-list me-2"></i>Queue</a>
                    </li>
                    <li class="nav-item mt-4">
                        <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <h2>Queue Management</h2>

                <div class="row">
                    <!-- Waiting Queue -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-warning text-white">
                                <h5><i class="fas fa-clock me-2"></i>Waiting</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $result = $conn->query("
                                    SELECT a.*, p.name as patient_name, u.name as doctor_name
                                    FROM appointments a
                                    JOIN patients p ON a.patient_id = p.id
                                    JOIN users u ON a.doctor_id = u.id
                                    WHERE a.status = 'scheduled' AND a.date = CURDATE()
                                    ORDER BY a.time ASC
                                ");
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<div class='alert alert-warning mb-2'>
                                            <strong>{$row['patient_name']}</strong><br>
                                            Doctor: {$row['doctor_name']}<br>
                                            Time: {$row['time']}<br>
                                            <a href='start_appointment.php?id={$row['id']}' class='btn btn-sm btn-primary mt-1'>Start</a>
                                        </div>";
                                    }
                                } else {
                                    echo "<p class='text-muted'>No patients waiting</p>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- In Progress -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5><i class="fas fa-play me-2"></i>In Progress</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $result = $conn->query("
                                    SELECT a.*, p.name as patient_name, u.name as doctor_name
                                    FROM appointments a
                                    JOIN patients p ON a.patient_id = p.id
                                    JOIN users u ON a.doctor_id = u.id
                                    WHERE a.status = 'in_progress'
                                    ORDER BY a.time ASC
                                ");
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<div class='alert alert-primary mb-2'>
                                            <strong>{$row['patient_name']}</strong><br>
                                            Doctor: {$row['doctor_name']}<br>
                                            Started: {$row['time']}
                                        </div>";
                                    }
                                } else {
                                    echo "<p class='text-muted'>No appointments in progress</p>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Completed -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5><i class="fas fa-check me-2"></i>Completed Today</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $result = $conn->query("
                                    SELECT a.*, p.name as patient_name, u.name as doctor_name
                                    FROM appointments a
                                    JOIN patients p ON a.patient_id = p.id
                                    JOIN users u ON a.doctor_id = u.id
                                    WHERE a.status = 'completed' AND a.date = CURDATE()
                                    ORDER BY a.time DESC
                                ");
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<div class='alert alert-success mb-2'>
                                            <strong>{$row['patient_name']}</strong><br>
                                            Doctor: {$row['doctor_name']}<br>
                                            Completed: {$row['time']}
                                        </div>";
                                    }
                                } else {
                                    echo "<p class='text-muted'>No completed appointments today</p>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>