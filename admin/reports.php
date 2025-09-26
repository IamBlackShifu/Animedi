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
    <title>VetCare Pro - Reports</title>
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
                <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i><span>Dashboard</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="staff.php"><i class="fas fa-users me-2"></i><span>Manage Staff</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="reports.php"><i class="fas fa-chart-bar me-2"></i><span>Reports</span></a>
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
        <h3 class="navbar-brand mb-0">Reports & Analytics - <?php echo $_SESSION['user_name']; ?></h3>
    </div>
</nav>

<!-- Main Content -->
<div class="main-content" id="main-content">
                <h2>Reports & Analytics</h2>

                <div class="row mb-4">
                    <!-- Appointments Report -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Appointments This Month</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="appointmentsChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Report -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Revenue This Month</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5>Total Appointments</h5>
                                <h3 class="text-primary">
                                    <?php
                                    $result = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE MONTH(date) = MONTH(CURDATE())");
                                    echo $result->fetch_assoc()['count'];
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5>Completed Appointments</h5>
                                <h3 class="text-success">
                                    <?php
                                    $result = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'completed' AND MONTH(date) = MONTH(CURDATE())");
                                    echo $result->fetch_assoc()['count'];
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5>Total Revenue</h5>
                                <h3 class="text-warning">
                                    $<?php
                                    $result = $conn->query("SELECT SUM(amount) as total FROM billing WHERE status = 'paid' AND MONTH(payment_date) = MONTH(CURDATE())");
                                    $total = $result->fetch_assoc()['total'] ?? 0;
                                    echo number_format($total, 2);
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5>Most Popular Species</h5>
                                <h3 class="text-info">
                                    <?php
                                    $result = $conn->query("SELECT species, COUNT(*) as count FROM patients GROUP BY species ORDER BY count DESC LIMIT 1");
                                    $row = $result->fetch_assoc();
                                    echo $row ? $row['species'] : 'N/A';
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Patients -->
                <div class="card">
                    <div class="card-header">
                        <h5>Top Patients (Most Visits)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Owner</th>
                                        <th>Species</th>
                                        <th>Total Visits</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = $conn->query("
                                        SELECT p.name as patient_name, o.name as owner_name, p.species, COUNT(a.id) as visits
                                        FROM patients p
                                        JOIN owners o ON p.owner_id = o.id
                                        LEFT JOIN appointments a ON p.id = a.patient_id
                                        GROUP BY p.id
                                        ORDER BY visits DESC LIMIT 10
                                    ");
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                            <td>{$row['patient_name']}</td>
                                            <td>{$row['owner_name']}</td>
                                            <td>{$row['species']}</td>
                                            <td>{$row['visits']}</td>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('navbar').classList.toggle('collapsed');
            document.getElementById('main-content').classList.toggle('collapsed');
        });
        // Appointments Chart
        const appointmentsCtx = document.getElementById('appointmentsChart').getContext('2d');
        new Chart(appointmentsCtx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Appointments',
                    data: [12, 19, 15, 25],
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            }
        });

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Revenue ($)',
                    data: [300, 450, 380, 520],
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            }
        });
    </script>
</body>
</html>