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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/theme-infinityloc.css">
    <style>
        body { background-color: #ecf0f1; font-family: 'Montserrat', sans-serif; }
        .sidebar { position: fixed; left: 0; top: 0; width: 250px; height: 100vh; background: #2c3e50; color: white; transition: width 0.3s; z-index: 1000; overflow-y: auto; }
        .sidebar.collapsed { width: 70px; }
        .sidebar .nav-link { color: #bdc3c7; transition: all 0.3s; padding: 10px 15px; }
        .sidebar .nav-link:hover { color: white; background: #34495e; }
        .sidebar .nav-link.active { color: white; background: #3498db; }
        .sidebar .nav-link span { display: inline; }
        .sidebar.collapsed .nav-link span { display: none; }
        .sidebar .logout-btn { position: absolute; bottom: 20px; left: 15px; right: 15px; }
        .sidebar.collapsed .logout-btn { left: 10px; right: 10px; }
        .main-content { margin-left: 250px; transition: margin-left 0.3s; padding: 20px; }
        .main-content.collapsed { margin-left: 70px; }
        .navbar { background: #34495e; color: white; margin-left: 250px; transition: margin-left 0.3s; }
        .navbar.collapsed { margin-left: 70px; }
        .card { border: none; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); background: #ffffff; }
        .table { border-radius: 10px; overflow: hidden; background: #ffffff; }
        .table thead th { background: #34495e; color: white; border: none; }
        .table tbody tr:hover { background: #f8f9fa; }
        .btn { border-radius: 25px; }
        .btn-primary { background: #3498db; border-color: #3498db; }
        .btn-primary:hover { background: #2980b9; }
        .btn-success { background: #27ae60; border-color: #27ae60; }
        .btn-success:hover { background: #229954; }
        .btn-warning { background: #f39c12; border-color: #f39c12; }
        .btn-warning:hover { background: #e67e22; }
        .btn-danger { background: #e74c3c; border-color: #e74c3c; }
        .btn-danger:hover { background: #c0392b; }
        .status-scheduled { background-color: #fff3cd; border-left: 4px solid #f39c12; }
        .status-in_progress { background-color: #cce5ff; border-left: 4px solid #3498db; }
        .status-completed { background-color: #d1ecf1; border-left: 4px solid #27ae60; }
        .queue-item { border-radius: 8px; margin-bottom: 10px; padding: 15px; transition: all 0.3s; }
        .queue-item:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .form-section { margin-bottom: 30px; }
        .attribution { text-align: center; color: #7f8c8d; }
    </style>
</head>
<body>
<!-- Fixed Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="p-3">
        <h5 class="text-center mb-4">VetCare Pro</h5>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i><span>Dashboard</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="patients.php"><i class="fas fa-paw me-2"></i><span>Patients</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="appointments.php"><i class="fas fa-calendar me-2"></i><span>Appointments</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="queue.php"><i class="fas fa-list me-2"></i><span>Queue</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="billing.php"><i class="fas fa-dollar-sign me-2"></i><span>Billing</span></a>
            </li>
        </ul>
        <div class="logout-btn">
            <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i><span>Logout</span></a>
        </div>
    </div>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark" id="navbar">
    <div class="container-fluid">
        <button class="btn btn-outline-light me-3" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <h3 class="navbar-brand mb-0">Queue Management - <?php echo $_SESSION['user_name']; ?> (Reception)</h3>
    </div>
</nav>

<!-- Main Content -->
<div class="main-content" id="main-content">
    <div class="container-fluid">
        <div class="row">
            <!-- Waiting Queue -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header text-white" style="background: linear-gradient(135deg, #f39c12, #e67e22);">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Waiting</h5>
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
                                echo "<div class='queue-item status-scheduled'>
                                    <div class='d-flex justify-content-between align-items-start'>
                                        <div>
                                            <strong class='text-dark'>{$row['patient_name']}</strong><br>
                                            <small class='text-muted'>Doctor: {$row['doctor_name']}</small><br>
                                            <small class='text-muted'>Time: {$row['time']}</small>
                                        </div>
                                        <a href='start_appointment.php?id={$row['id']}' class='btn btn-sm btn-success'>
                                            <i class='fas fa-play me-1'></i>Start
                                        </a>
                                    </div>
                                </div>";
                            }
                        } else {
                            echo "<div class='text-center text-muted py-4'>
                                <i class='fas fa-check-circle fa-3x mb-3 text-success'></i>
                                <p>No patients waiting</p>
                            </div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- In Progress -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header text-white" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                        <h5 class="mb-0"><i class="fas fa-play me-2"></i>In Progress</h5>
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
                                echo "<div class='queue-item status-in_progress'>
                                    <div class='d-flex justify-content-between align-items-start'>
                                        <div>
                                            <strong class='text-dark'>{$row['patient_name']}</strong><br>
                                            <small class='text-muted'>Doctor: {$row['doctor_name']}</small><br>
                                            <small class='text-muted'>Started: {$row['time']}</small>
                                        </div>
                                        <span class='badge bg-primary'><i class='fas fa-spinner fa-spin me-1'></i>In Progress</span>
                                    </div>
                                </div>";
                            }
                        } else {
                            echo "<div class='text-center text-muted py-4'>
                                <i class='fas fa-pause-circle fa-3x mb-3 text-secondary'></i>
                                <p>No appointments in progress</p>
                            </div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Completed -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header text-white" style="background: linear-gradient(135deg, #27ae60, #229954);">
                        <h5 class="mb-0"><i class="fas fa-check me-2"></i>Completed Today</h5>
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
                                echo "<div class='queue-item status-completed'>
                                    <div class='d-flex justify-content-between align-items-start'>
                                        <div>
                                            <strong class='text-dark'>{$row['patient_name']}</strong><br>
                                            <small class='text-muted'>Doctor: {$row['doctor_name']}</small><br>
                                            <small class='text-muted'>Completed: {$row['time']}</small>
                                        </div>
                                        <span class='badge bg-success'><i class='fas fa-check me-1'></i>Done</span>
                                    </div>
                                </div>";
                            }
                        } else {
                            echo "<div class='text-center text-muted py-4'>
                                <i class='fas fa-list fa-3x mb-3 text-muted'></i>
                                <p>No completed appointments today</p>
                            </div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        const navbar = document.getElementById('navbar');
        const mainContent = document.getElementById('main-content');

        sidebar.classList.toggle('collapsed');
        navbar.classList.toggle('collapsed');
        mainContent.classList.toggle('collapsed');
    });
</script>
</body>
</html>