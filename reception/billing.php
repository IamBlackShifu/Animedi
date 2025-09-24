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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { background: #343a40; color: white; min-height: 100vh; }
        .sidebar .nav-link { color: #adb5bd; }
        .sidebar .nav-link:hover { color: white; }
        .card { border: none; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
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
                        <a class="nav-link" href="queue.php"><i class="fas fa-list me-2"></i>Queue</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="billing.php"><i class="fas fa-dollar-sign me-2"></i>Billing</a>
                    </li>
                    <li class="nav-item mt-4">
                        <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <h2>Billing & Records</h2>

                <!-- Search -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="patient" placeholder="Search by patient name">
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" name="date_from">
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" name="date_to">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Search</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Records Table -->
                <div class="card">
                    <div class="card-header">
                        <h5>Medical Records & Billing</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Patient</th>
                                        <th>Doctor</th>
                                        <th>Diagnosis</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
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
                                                $actions = "<a href='mark_paid.php?id={$row['billing_id']}' class='btn btn-sm btn-success'>Mark Paid</a>";
                                            }
                                            $actions .= " <a href='print_invoice.php?id={$row['appointment_id']}' class='btn btn-sm btn-primary' target='_blank'>Print Invoice</a>";
                                        }
                                        echo "<tr>
                                            <td>{$row['date']}</td>
                                            <td>{$row['patient_name']}</td>
                                            <td>{$row['doctor_name']}</td>
                                            <td>" . substr($row['diagnosis'], 0, 50) . "...</td>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>