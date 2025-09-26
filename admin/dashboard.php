<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
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
    <title>VetCare Pro - Admin Dashboard</title>
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
                <a class="nav-link active" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i><span>Dashboard</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="staff.php"><i class="fas fa-users me-2"></i><span>Manage Staff</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="reports.php"><i class="fas fa-chart-bar me-2"></i><span>Reports</span></a>
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
        <h2 class="navbar-brand mb-0">Welcome, <?php echo $_SESSION['user_name']; ?> (Admin)</h2>
    </div>
</nav>

<!-- Main Content -->
<div class="main-content" id="main-content">
    <div class="dashboard-anim">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-user-shield me-2"></i>Welcome, Admin <?php echo $_SESSION['user_name']; ?></h2>
            </div>

            <!-- Dashboard Cards -->
            <div class="row mb-4">
                <!-- Total Staff -->
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-users fa-2x text-primary mb-2"></i>
                            <h5 class="card-title">Total Staff</h5>
                            <h3 class="text-primary">
                                <?php
                                $result = $conn->query("SELECT COUNT(*) as count FROM users");
                                echo $result->fetch_assoc()['count'];
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- Total Patients -->
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-paw fa-2x text-success mb-2"></i>
                            <h5 class="card-title">Total Patients</h5>
                            <h3 class="text-success">
                                <?php
                                $result = $conn->query("SELECT COUNT(*) as count FROM patients");
                                echo $result->fetch_assoc()['count'];
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- Total Appointments -->
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-calendar fa-2x text-info mb-2"></i>
                            <h5 class="card-title">Total Appointments</h5>
                            <h3 class="text-info">
                                <?php
                                $result = $conn->query("SELECT COUNT(*) as count FROM appointments");
                                echo $result->fetch_assoc()['count'];
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-dollar-sign fa-2x text-warning mb-2"></i>
                            <h5 class="card-title">Total Revenue</h5>
                            <h3 class="text-warning">
                                $<?php
                                $result = $conn->query("SELECT SUM(amount) as total FROM billing WHERE status = 'paid'");
                                $total = $result->fetch_assoc()['total'] ?? 0;
                                echo number_format($total, 2);
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-history me-2"></i>Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-calendar me-1"></i>Date</th>
                                    <th><i class="fas fa-bolt me-1"></i>Activity</th>
                                    <th><i class="fas fa-info me-1"></i>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Recent appointments
                                $result = $conn->query("
                                    SELECT 'Appointment' as type, CONCAT(p.name, ' - ', u.name) as details, a.created_at as date
                                    FROM appointments a
                                    JOIN patients p ON a.patient_id = p.id
                                    JOIN users u ON a.doctor_id = u.id
                                    ORDER BY a.created_at DESC LIMIT 5
                                ");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$row['date']}</td>
                                        <td>{$row['type']}</td>
                                        <td>{$row['details']}</td>
                                    </tr>";
                                }

                                // Recent payments
                                $result = $conn->query("
                                    SELECT 'Payment' as type, CONCAT('Amount: $', b.amount) as details, b.payment_date as date
                                    FROM billing b
                                    WHERE b.status = 'paid'
                                    ORDER BY b.payment_date DESC LIMIT 5
                                ");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$row['date']}</td>
                                        <td>{$row['type']}</td>
                                        <td>{$row['details']}</td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Attribution -->
            <div class="mt-4 attribution">
                <strong>Infinity Lines of Code</strong>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('collapsed');
        document.getElementById('navbar').classList.toggle('collapsed');
        document.getElementById('main-content').classList.toggle('collapsed');
    });
</script>
</body>
</html>
