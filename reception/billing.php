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
    <title>VetCare Pro - Billing & Records</title>
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
                <a class="nav-link" href="queue.php"><i class="fas fa-list me-2"></i><span>Queue</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="billing.php"><i class="fas fa-dollar-sign me-2"></i><span>Billing</span></a>
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
        <h3 class="navbar-brand mb-0">Billing & Records - <?php echo $_SESSION['user_name']; ?> (Reception)</h3>
    </div>
</nav>

<!-- Main Content -->
<div class="main-content" id="main-content">
    <div class="container-fluid">
        <!-- Search -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-search me-2"></i>Search Records</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="patient" class="form-label">Patient Name</label>
                        <input type="text" class="form-control" id="patient" name="patient" placeholder="Search by patient name" value="<?php echo isset($_GET['patient']) ? htmlspecialchars($_GET['patient']) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo isset($_GET['date_from']) ? $_GET['date_from'] : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo isset($_GET['date_to']) ? $_GET['date_to'] : ''; ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Records Table -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-file-invoice-dollar me-2"></i>Medical Records & Billing</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><i class="fas fa-calendar me-1"></i>Date</th>
                                <th><i class="fas fa-paw me-1"></i>Patient</th>
                                <th><i class="fas fa-user-md me-1"></i>Doctor</th>
                                <th><i class="fas fa-stethoscope me-1"></i>Diagnosis</th>
                                <th><i class="fas fa-dollar-sign me-1"></i>Amount</th>
                                <th><i class="fas fa-info-circle me-1"></i>Status</th>
                                <th><i class="fas fa-cogs me-1"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $where = "1=1";
                            if (isset($_GET['patient']) && !empty($_GET['patient'])) {
                                $patient = $conn->real_escape_string($_GET['patient']);
                                $where .= " AND p.name LIKE '%$patient%'";
                            }
                            if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
                                $date_from = $_GET['date_from'];
                                $where .= " AND a.date >= '$date_from'";
                            }
                            if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
                                $date_to = $_GET['date_to'];
                                $where .= " AND a.date <= '$date_to'";
                            }

                            $result = $conn->query("
                                SELECT a.date, p.name as patient_name, u.name as doctor_name,
                                       r.diagnosis, b.amount, b.status, b.id as billing_id, a.id as appointment_id
                                FROM appointments a
                                JOIN patients p ON a.patient_id = p.id
                                JOIN users u ON a.doctor_id = u.id
                                LEFT JOIN records r ON a.id = r.appointment_id
                                LEFT JOIN billing b ON a.id = b.appointment_id
                                WHERE $where AND a.status = 'completed'
                                ORDER BY a.date DESC
                            ");
                            while ($row = $result->fetch_assoc()) {
                                $status_class = match($row['status']) {
                                    'pending' => 'warning',
                                    'paid' => 'success',
                                    'cancelled' => 'danger',
                                    default => 'secondary'
                                };
                                $actions = "";
                                if ($row['billing_id']) {
                                    if ($row['status'] == 'pending') {
                                        $actions = "<a href='mark_paid.php?id={$row['billing_id']}' class='btn btn-sm btn-success me-2'>
                                            <i class='fas fa-check me-1'></i>Mark Paid
                                        </a>";
                                    }
                                    $actions .= "<a href='print_invoice.php?id={$row['appointment_id']}' class='btn btn-sm btn-primary' target='_blank'>
                                        <i class='fas fa-print me-1'></i>Print Invoice
                                    </a>";
                                }
                                echo "<tr>
                                    <td>{$row['date']}</td>
                                    <td>{$row['patient_name']}</td>
                                    <td>Dr. {$row['doctor_name']}</td>
                                    <td>" . (strlen($row['diagnosis']) > 50 ? substr($row['diagnosis'], 0, 50) . "..." : $row['diagnosis']) . "</td>
                                    <td>$" . number_format($row['amount'], 2) . "</td>
                                    <td><span class='badge bg-{$status_class}'>{$row['status']}</span></td>
                                    <td>{$actions}</td>
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
</script>
</body>
</html>