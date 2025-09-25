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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { background: #343a40; color: white; min-height: 100vh; }
        .sidebar .nav-link { color: #adb5bd; }
        .sidebar .nav-link:hover { color: white; }
        .card { border: none; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn-primary { background-color: #28a745; border-color: #28a745; }
        .btn-primary:hover { background-color: #218838; border-color: #218838; }
        .status-pending { color: #ffc107; }
        .status-confirmed { color: #28a745; }
        .status-completed { color: #6c757d; }
        .status-cancelled { color: #dc3545; }
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
                        <a class="nav-link active" href="appointments.php"><i class="fas fa-calendar me-2"></i>Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="queue.php"><i class="fas fa-list me-2"></i>Queue</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="billing.php"><i class="fas fa-dollar-sign me-2"></i>Billing</a>
                    </li>
                    <li class="nav-item mt-4">
                        <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Appointments Management</h2>
                    <a href="add_appointment.php" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add New Appointment
                    </a>
                </div>

                <!-- Appointments Table -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-calendar-check me-2"></i>All Appointments</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Patient</th>
                                        <th>Owner</th>
                                        <th>Doctor</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Actions</th>
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
                                                    <a href='edit_appointment.php?id={$row['id']}' class='btn btn-sm btn-warning'>Edit</a>
                                                    <button class='btn btn-sm btn-danger' onclick='cancelAppointment({$row['id']})'>Cancel</button>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cancelAppointment(id) {
            if (confirm('Are you sure you want to cancel this appointment?')) {
                window.location.href = 'cancel_appointment.php?id=' + id;
            }
        }
    </script>
</body>
</html>