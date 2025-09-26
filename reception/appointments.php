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
    <title>VetCare Pro - Appointments</title>
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
        .status-pending { color: #f39c12; font-weight: bold; }
        .status-confirmed { color: #27ae60; font-weight: bold; }
        .status-completed { color: #7f8c8d; font-weight: bold; }
        .status-cancelled { color: #e74c3c; font-weight: bold; }
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
                <a class="nav-link active" href="appointments.php"><i class="fas fa-calendar me-2"></i><span>Appointments</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="queue.php"><i class="fas fa-list me-2"></i><span>Queue</span></a>
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
        <h3 class="navbar-brand mb-0">Appointments Management - <?php echo $_SESSION['user_name']; ?> (Reception)</h3>
        <div>
            <a href="add_appointment.php" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Add New Appointment
            </a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="main-content" id="main-content">
    <div class="container-fluid">
        <!-- Appointments Table -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-calendar-check me-2"></i>All Appointments</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><i class="fas fa-calendar-day me-1"></i>Date & Time</th>
                                <th><i class="fas fa-paw me-1"></i>Patient</th>
                                <th><i class="fas fa-user me-1"></i>Owner</th>
                                <th><i class="fas fa-user-md me-1"></i>Doctor</th>
                                <th><i class="fas fa-comment me-1"></i>Reason</th>
                                <th><i class="fas fa-info-circle me-1"></i>Status</th>
                                <th><i class="fas fa-cogs me-1"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $conn->query("
                                SELECT a.*, p.name as patient_name, p.species, o.name as owner_name,
                                       u.name as doctor_name
                                FROM appointments a
                                JOIN patients p ON a.patient_id = p.id
                                JOIN owners o ON p.owner_id = o.id
                                JOIN users u ON a.doctor_id = u.id
                                ORDER BY a.date DESC, a.time DESC
                            ");

                            while ($row = $result->fetch_assoc()) {
                                $status_class = match($row['status']) {
                                    'pending' => 'status-pending',
                                    'confirmed' => 'status-confirmed',
                                    'completed' => 'status-completed',
                                    'cancelled' => 'status-cancelled',
                                    default => ''
                                };

                                echo "<tr>
                                    <td>{$row['date']} {$row['time']}</td>
                                    <td>{$row['patient_name']}<br><small class='text-muted'>{$row['species']}</small></td>
                                    <td>{$row['owner_name']}</td>
                                    <td>Dr. {$row['doctor_name']}</td>
                                    <td>" . (isset($row['reason']) ? $row['reason'] : 'N/A') . "</td>
                                    <td><span class='{$status_class}'>{$row['status']}</span></td>
                                    <td>
                                        <div class='btn-group' role='group'>
                                            <a href='edit_appointment.php?id={$row['id']}' class='btn btn-sm btn-warning me-1'>
                                                <i class='fas fa-edit me-1'></i>Edit
                                            </a>
                                            <button class='btn btn-sm btn-danger' onclick='cancelAppointment({$row['id']})'>
                                                <i class='fas fa-times me-1'></i>Cancel
                                            </button>
                                        </div>
                                    </td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
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

    function cancelAppointment(id) {
        if (confirm('Are you sure you want to cancel this appointment?')) {
            window.location.href = 'cancel_appointment.php?id=' + id;
        }
    }
</script>
</body>
</html>