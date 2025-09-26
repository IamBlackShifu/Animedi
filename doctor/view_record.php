<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor') {
    header("Location: ../login/index.php");
    exit();
}
include("../includes/dbconn.php");

$appointment_id = $_GET['id'] ?? 0;

// Get record details
$stmt = $conn->prepare("
    SELECT r.*, a.date, a.time, p.name as patient_name, p.species, p.breed, o.name as owner_name
    FROM records r
    JOIN appointments a ON r.appointment_id = a.id
    JOIN patients p ON a.patient_id = p.id
    JOIN owners o ON p.owner_id = o.id
    WHERE r.appointment_id = ? AND a.doctor_id = ?
");
$stmt->bind_param("ii", $appointment_id, $_SESSION['user_id']);
$stmt->execute();
$record = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$record) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VetCare Pro - View Record</title>
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
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i><span>Dashboard</span></a></li>
            <li class="nav-item"><a class="nav-link" href="patients.php"><i class="fas fa-paw me-2"></i><span>My Patients</span></a></li>
            <li class="nav-item"><a class="nav-link active" href="records.php"><i class="fas fa-file-medical me-2"></i><span>Medical Records</span></a></li>
            <li class="nav-item"><a class="nav-link" href="history.php"><i class="fas fa-history me-2"></i><span>Patient History</span></a></li>
            <li class="nav-item"><a class="nav-link" href="doctorsnotes.php"><i class="fas fa-sticky-note me-2"></i><span>Doctor Notes</span></a></li>
            <li class="nav-item"><a class="nav-link" href="clientdata.php"><i class="fas fa-users me-2"></i><span>Client Data</span></a></li>
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
        <h3 class="navbar-brand mb-0">Medical Record - <?php echo $record['patient_name']; ?></h3>
    </div>
</nav>

<!-- Main Content -->
<div class="main-content" id="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Medical Record - <?php echo $record['patient_name']; ?></h2>
                    <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Dashboard</a>
                </div>

                <!-- Appointment Details -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-calendar me-2"></i>Appointment Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Patient:</strong> <?php echo $record['patient_name']; ?> (<?php echo $record['species']; ?>)</p>
                                <p><strong>Owner:</strong> <?php echo $record['owner_name']; ?></p>
                                <p><strong>Date:</strong> <?php echo $record['date']; ?></p>
                                <p><strong>Time:</strong> <?php echo $record['time']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Medical Details -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-file-medical me-2"></i>Medical Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">TPR (Temperature/Pulse/Respiration):</label>
                            <p><?php echo $record['tpr'] ?: 'Not recorded'; ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Clinical Notes:</label>
                            <p><?php echo nl2br($record['physical_exam']) ?: 'Not recorded'; ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Diagnosis:</label>
                            <p><?php echo nl2br($record['diagnosis']); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Treatment:</label>
                            <p><?php echo nl2br($record['treatment']) ?: 'Not recorded'; ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Prescription:</label>
                            <p><?php echo nl2br($record['prescription']) ?: 'Not recorded'; ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Lab Work:</label>
                            <p><?php echo nl2br($record['lab_work']) ?: 'Not recorded'; ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Diagnostic Imaging:</label>
                            <p><?php echo nl2br($record['diagnostic_imaging']) ?: 'Not recorded'; ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Outcome/Follow-up:</label>
                            <p><?php echo nl2br($record['outcome']) ?: 'Not recorded'; ?></p>
                        </div>
                        <?php if ($record['files']): ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Attached Files:</label>
                            <p><a href="<?php echo $record['files']; ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-download me-1"></i>View Attachment</a></p>
                        </div>
                        <?php endif; ?>
                    </div>
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