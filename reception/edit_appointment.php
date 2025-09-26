<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'reception') {
    header("Location: ../login/index.php");
    exit();
}
include("../includes/dbconn.php");

$appointment_id = $_GET['id'] ?? 0;

// Get appointment details
$stmt = $conn->prepare("
    SELECT a.*, p.name as patient_name, p.species, p.breed, o.name as owner_name,
           u.name as doctor_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN owners o ON p.owner_id = o.id
    JOIN users u ON a.doctor_id = u.id
    WHERE a.id = ?
");
$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$appointment = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$appointment) {
    header("Location: appointments.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_id = $_POST['doctor_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $reason = $_POST['reason'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE appointments SET doctor_id = ?, date = ?, time = ?, reason = ?, status = ? WHERE id = ?");
    $stmt->bind_param("issssi", $doctor_id, $date, $time, $reason, $status, $appointment_id);
    $stmt->execute();
    $stmt->close();

    header("Location: appointments.php?success=Appointment updated successfully");
    exit();
}

// Get all doctors
$doctors_result = $conn->query("SELECT id, name FROM users WHERE role = 'doctor' ORDER BY name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VetCare Pro - Edit Appointment</title>
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
        .btn-secondary { background: #95a5a6; border-color: #95a5a6; }
        .btn-secondary:hover { background: #7f8c8d; }
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
        <h3 class="navbar-brand mb-0">Edit Appointment - <?php echo $_SESSION['user_name']; ?> (Reception)</h3>
    </div>
</nav>

<!-- Main Content -->
<div class="main-content" id="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-edit me-2"></i>Edit Appointment</h2>
            <a href="appointments.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Appointments</a>
        </div>

        <!-- Patient Info -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-paw me-2"></i>Patient Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Patient:</strong> <?php echo $appointment['patient_name']; ?></p>
                        <p><strong>Species:</strong> <?php echo $appointment['species']; ?></p>
                        <p><strong>Breed:</strong> <?php echo $appointment['breed']; ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Owner:</strong> <?php echo $appointment['owner_name']; ?></p>
                        <p><strong>Current Doctor:</strong> Dr. <?php echo $appointment['doctor_name']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0"><i class="fas fa-calendar-edit me-2"></i>Appointment Details</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Doctor</label>
                                <select class="form-control" name="doctor_id" required>
                                    <option value="">Select Doctor</option>
                                    <?php while ($doctor = $doctors_result->fetch_assoc()): ?>
                                        <option value="<?php echo $doctor['id']; ?>" <?php echo ($doctor['id'] == $appointment['doctor_id']) ? 'selected' : ''; ?>>
                                            Dr. <?php echo $doctor['name']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <select class="form-control" name="status" required>
                                    <option value="pending" <?php echo ($appointment['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo ($appointment['status'] == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="completed" <?php echo ($appointment['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo ($appointment['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Date</label>
                                <input type="date" class="form-control" name="date" value="<?php echo $appointment['date']; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Time</label>
                                <input type="time" class="form-control" name="time" value="<?php echo $appointment['time']; ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Reason for Visit</label>
                        <textarea class="form-control" name="reason" rows="3" placeholder="Describe the reason for the appointment"><?php echo $appointment['reason'] ?? ''; ?></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save me-2"></i>Update Appointment
                        </button>
                        <a href="appointments.php" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar toggle functionality
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const navbar = document.getElementById('navbar');

        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('collapsed');
        navbar.classList.toggle('collapsed');
    });
</script>
</body>
</html>