<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor') {
    header("Location: ../login/index.php");
    exit();
}
include("../includes/dbconn.php");

$doctor_id = $_SESSION['user_id'];

// Get all medical records for this doctor's patients
$records_result = $conn->query("
    SELECT r.*, a.date, a.time, p.name as patient_name, p.species, p.breed, o.name as owner_name
    FROM records r
    JOIN appointments a ON r.appointment_id = a.id
    JOIN patients p ON a.patient_id = p.id
    JOIN owners o ON p.owner_id = o.id
    WHERE a.doctor_id = $doctor_id
    ORDER BY r.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VetCare Pro - Medical Records</title>
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
        .record-card { margin-bottom: 15px; }
        .record-header { background-color: #f8f9fa; padding: 10px 15px; border-bottom: 1px solid #dee2e6; }
        .record-body { padding: 15px; }
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
        <h3 class="navbar-brand mb-0">Medical Records - Dr. <?php echo $_SESSION['user_name']; ?></h3>
    </div>
</nav>

<!-- Main Content -->
<div class="main-content" id="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Medical Records</h2>
                </div>

                <!-- Records List -->
                <div class="row">
                    <?php if ($records_result->num_rows > 0): ?>
                        <?php while ($record = $records_result->fetch_assoc()): ?>
                            <div class="col-md-12">
                                <div class="card record-card">
                                    <div class="record-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <i class="fas fa-file-medical me-2"></i>
                                                <?php echo $record['patient_name']; ?> (<?php echo $record['species']; ?>)
                                                - <?php echo date('M d, Y', strtotime($record['date'])); ?>
                                            </h6>
                                            <small class="text-muted">
                                                Recorded: <?php echo date('M d, Y H:i', strtotime($record['created_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="record-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Patient:</strong> <?php echo $record['patient_name']; ?></p>
                                                <p><strong>Species:</strong> <?php echo $record['species']; ?></p>
                                                <p><strong>Breed:</strong> <?php echo $record['breed'] ?: 'N/A'; ?></p>
                                                <p><strong>Owner:</strong> <?php echo $record['owner_name']; ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Appointment Date:</strong> <?php echo $record['date']; ?></p>
                                                <p><strong>Time:</strong> <?php echo $record['time']; ?></p>
                                                <?php if (!empty($record['diagnosis'])): ?>
                                                    <p><strong>Diagnosis:</strong> <?php echo nl2br($record['diagnosis']); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <?php if (!empty($record['history']) || !empty($record['tpr']) || !empty($record['physical_exam'])): ?>
                                            <hr>
                                            <div class="row">
                                                <?php if (!empty($record['history'])): ?>
                                                    <div class="col-md-4">
                                                        <h6>History</h6>
                                                        <p class="text-muted small"><?php echo nl2br($record['history']); ?></p>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($record['tpr'])): ?>
                                                    <div class="col-md-4">
                                                        <h6>TPR</h6>
                                                        <p class="text-muted small"><?php echo nl2br($record['tpr']); ?></p>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($record['physical_exam'])): ?>
                                                    <div class="col-md-4">
                                                        <h6>Physical Exam</h6>
                                                        <p class="text-muted small"><?php echo nl2br($record['physical_exam']); ?></p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($record['ddx']) || !empty($record['dx_plan'])): ?>
                                            <hr>
                                            <div class="row">
                                                <?php if (!empty($record['ddx'])): ?>
                                                    <div class="col-md-6">
                                                        <h6>Differential Diagnosis</h6>
                                                        <p class="text-muted small"><?php echo nl2br($record['ddx']); ?></p>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($record['dx_plan'])): ?>
                                                    <div class="col-md-6">
                                                        <h6>Diagnosis Plan</h6>
                                                        <p class="text-muted small"><?php echo nl2br($record['dx_plan']); ?></p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($record['treatment']) || !empty($record['rx_plan'])): ?>
                                            <hr>
                                            <div class="row">
                                                <?php if (!empty($record['treatment'])): ?>
                                                    <div class="col-md-6">
                                                        <h6>Treatment</h6>
                                                        <p class="text-muted small"><?php echo nl2br($record['treatment']); ?></p>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($record['rx_plan'])): ?>
                                                    <div class="col-md-6">
                                                        <h6>Treatment Plan</h6>
                                                        <p class="text-muted small"><?php echo nl2br($record['rx_plan']); ?></p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($record['prescription']) || !empty($record['outcome'])): ?>
                                            <hr>
                                            <div class="row">
                                                <?php if (!empty($record['prescription'])): ?>
                                                    <div class="col-md-6">
                                                        <h6>Prescription</h6>
                                                        <p class="text-muted small"><?php echo nl2br($record['prescription']); ?></p>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($record['outcome'])): ?>
                                                    <div class="col-md-6">
                                                        <h6>Outcome</h6>
                                                        <p class="text-muted small"><?php echo nl2br($record['outcome']); ?></p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-medical fa-3x text-muted mb-3"></i>
                                    <h5>No Medical Records Found</h5>
                                    <p class="text-muted">You haven't created any medical records yet.</p>
                                    <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
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