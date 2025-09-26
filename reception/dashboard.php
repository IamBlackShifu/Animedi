<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'reception') {
    header("Location: ../login/index.php");
    exit();
}
include("../includes/dbconn.php");
$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VetCare Pro - Reception Dashboard</title>
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
                <a class="nav-link" href="patients.php"><i class="fas fa-paw me-2"></i><span>Patients</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="appointments.php"><i class="fas fa-calendar me-2"></i><span>Appointments</span></a>
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
        <h2 class="navbar-brand mb-0">Welcome, <?php echo $_SESSION['user_name']; ?> (Reception)</h2>
        <div>
            <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#newPatientModal">
                <i class="fas fa-plus me-2"></i>New Patient
            </button>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#newAppointmentModal">
                <i class="fas fa-calendar-plus me-2"></i>New Appointment
            </button>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="main-content" id="main-content">
    <div class="dashboard-anim">

            <!-- Dashboard Cards -->
            <div class="row mb-4">
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

                <!-- Today's Appointments -->
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-calendar fa-2x text-primary mb-2"></i>
                            <h5 class="card-title">Today's Appointments</h5>
                            <h3 class="text-primary">
                                <?php
                                $result = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE date = '$today'");
                                echo $result->fetch_assoc()['count'];
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- Waiting -->
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                            <h5 class="card-title">Waiting</h5>
                            <h3 class="text-warning">
                                <?php
                                $result = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'scheduled'");
                                echo $result->fetch_assoc()['count'];
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- Completed Today -->
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-2x text-info mb-2"></i>
                            <h5 class="card-title">Completed Today</h5>
                            <h3 class="text-info">
                                <?php
                                $result = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE date = '$today' AND status = 'completed'");
                                echo $result->fetch_assoc()['count'];
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Appointments -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-list me-2"></i>Recent Appointments</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-paw me-1"></i>Patient</th>
                                    <th><i class="fas fa-user-md me-1"></i>Doctor</th>
                                    <th><i class="fas fa-calendar me-1"></i>Date & Time</th>
                                    <th><i class="fas fa-info-circle me-1"></i>Status</th>
                                    <th><i class="fas fa-cogs me-1"></i>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = $conn->query("
                                    SELECT a.*, p.name as patient_name, u.name as doctor_name
                                    FROM appointments a
                                    JOIN patients p ON a.patient_id = p.id
                                    JOIN users u ON a.doctor_id = u.id
                                    ORDER BY a.date DESC, a.time DESC LIMIT 10
                                ");
                                while ($row = $result->fetch_assoc()) {
                                    $status_class = match($row['status']) {
                                        'scheduled' => 'warning',
                                        'in_progress' => 'primary',
                                        'completed' => 'success',
                                        'cancelled' => 'danger'
                                    };
                                    echo "<tr>
                                        <td>{$row['patient_name']}</td>
                                        <td>{$row['doctor_name']}</td>
                                        <td>{$row['date']} {$row['time']}</td>
                                        <td><span class='badge bg-{$status_class}'>{$row['status']}</span></td>
                                        <td>
                                            <button class='btn btn-sm btn-outline-primary'>View</button>
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

<!-- New Patient Modal -->
<div class="modal fade" id="newPatientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Register New Patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="add_patient.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Owner Name</label>
                        <input type="text" class="form-control" name="owner_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Owner Contact</label>
                        <input type="text" class="form-control" name="owner_contact" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Patient Name</label>
                        <input type="text" class="form-control" name="patient_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Species</label>
                        <input type="text" class="form-control" name="species" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Breed</label>
                        <input type="text" class="form-control" name="breed">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Age</label>
                        <input type="number" class="form-control" name="age">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Register Patient</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- New Appointment Modal -->
<div class="modal fade" id="newAppointmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Book New Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="add_appointment.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Patient</label>
                        <select class="form-control" name="patient_id" required>
                            <option value="">Select Patient</option>
                            <?php
                            $patients = $conn->query("SELECT id, name FROM patients ORDER BY name");
                            while ($patient = $patients->fetch_assoc()) {
                                echo "<option value='{$patient['id']}'>{$patient['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Doctor</label>
                        <select class="form-control" name="doctor_id" required>
                            <option value="">Select Doctor</option>
                            <?php
                            $doctors = $conn->query("SELECT id, name FROM users WHERE role = 'doctor'");
                            while ($doctor = $doctors->fetch_assoc()) {
                                echo "<option value='{$doctor['id']}'>{$doctor['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Time</label>
                        <input type="time" class="form-control" name="time" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Book Appointment</button>
                </div>
            </form>
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
