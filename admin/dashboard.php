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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/theme-infinityloc.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { background: #343a40; color: white; min-height: 100vh; }
        .sidebar .nav-link { color: #adb5bd; }
        .sidebar .nav-link:hover { color: white; }
        .card { border: none; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn-primary { background-color: #28a745; border-color: #28a745; }
        .btn-primary:hover { background-color: #218838; border-color: #218838; }
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
                    <a class="nav-link active" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="staff.php"><i class="fas fa-users me-2"></i>Manage Staff</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reports.php"><i class="fas fa-chart-bar me-2"></i>Reports</a>
                </li>
                <li class="nav-item mt-4">
                    <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 p-4 dashboard-anim">
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
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Activity</th>
                                    <th>Details</th>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
