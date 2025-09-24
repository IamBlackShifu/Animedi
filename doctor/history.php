<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor') {
    header("Location: ../login/index.php");
    exit();
}
include("../includes/dbconn.php");

$doctor_id = $_SESSION['user_id'];
$patient_id = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : 0;

// Get patient details
$patient_result = $conn->query("
    SELECT p.*, o.name as owner_name, o.contact
    FROM patients p
    JOIN owners o ON p.owner_id = o.id
    WHERE p.id = $patient_id
");

if ($patient_result->num_rows == 0) {
    header("Location: patients.php");
    exit();
}

$patient = $patient_result->fetch_assoc();

// Get patient medical history
$history_result = $conn->query("
    SELECT r.*, a.date, a.time, u.name as doctor_name
    FROM records r
    JOIN appointments a ON r.appointment_id = a.id
    JOIN users u ON a.doctor_id = u.id
    WHERE a.patient_id = $patient_id AND a.doctor_id = $doctor_id
    ORDER BY r.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VetCare Pro - Patient History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { background: #343a40; color: white; min-height: 100vh; }
        .sidebar .nav-link { color: #adb5bd; }
        .sidebar .nav-link:hover { color: white; }
        .sidebar .nav-link.active { color: white; background-color: #495057; }
        .card { border: none; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn-primary { background-color: #28a745; border-color: #28a745; }
        .btn-primary:hover { background-color: #218838; border-color: #218838; }
        .history-card { margin-bottom: 20px; }
        .history-header { background-color: #f8f9fa; padding: 15px; border-bottom: 1px solid #dee2e6; }
        .history-body { padding: 20px; }
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
                        <a class="nav-link" href="patients.php"><i class="fas fa-paw me-2"></i>My Patients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="records.php"><i class="fas fa-file-medical me-2"></i>Medical Records</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="history.php"><i class="fas fa-history me-2"></i>Patient History</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="doctorsnotes.php"><i class="fas fa-sticky-note me-2"></i>Doctor Notes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="clientdata.php"><i class="fas fa-users me-2"></i>Client Data</a>
                    </li>
                    <li class="nav-item mt-4">
                        <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Patient History - <?php echo $patient['name']; ?></h2>
                    <a href="patients.php" class="btn btn-secondary">Back to Patients</a>
                </div>

                <!-- Patient Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-paw me-2"></i>Patient Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> <?php echo $patient['name']; ?></p>
                                <p><strong>Species:</strong> <?php echo $patient['species']; ?></p>
                                <p><strong>Breed:</strong> <?php echo $patient['breed'] ?: 'N/A'; ?></p>
                                <p><strong>Age:</strong> <?php echo $patient['age'] ?: 'N/A'; ?> years</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Owner:</strong> <?php echo $patient['owner_name']; ?></p>
                                <p><strong>Contact:</strong> <?php echo $patient['contact']; ?></p>
                                <p><strong>Notes:</strong> <?php echo $patient['notes'] ?: 'No additional notes'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Medical History -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-history me-2"></i>Medical History</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($history_result->num_rows > 0): ?>
                            <?php while ($record = $history_result->fetch_assoc()): ?>
                                <div class="history-card card">
                                    <div class="history-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <i class="fas fa-calendar-check me-2"></i>
                                                Visit on <?php echo date('F d, Y', strtotime($record['date'])); ?> at <?php echo $record['time']; ?>
                                            </h6>
                                            <small class="text-muted">
                                                Recorded: <?php echo date('M d, Y H:i', strtotime($record['created_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="history-body">
                                        <div class="row">
                                            <?php if (!empty($record['history'])): ?>
                                                <div class="col-md-6">
                                                    <h6>History</h6>
                                                    <p class="text-muted"><?php echo nl2br($record['history']); ?></p>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (!empty($record['tpr'])): ?>
                                                <div class="col-md-6">
                                                    <h6>TPR (Temperature Pulse Respiration)</h6>
                                                    <p class="text-muted"><?php echo nl2br($record['tpr']); ?></p>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <?php if (!empty($record['physical_exam'])): ?>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h6>Physical Exam</h6>
                                                    <p class="text-muted"><?php echo nl2br($record['physical_exam']); ?></p>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($record['diagnosis']) || !empty($record['treatment'])): ?>
                                            <div class="row">
                                                <?php if (!empty($record['diagnosis'])): ?>
                                                    <div class="col-md-6">
                                                        <h6>Diagnosis</h6>
                                                        <p class="text-muted"><?php echo nl2br($record['diagnosis']); ?></p>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if (!empty($record['treatment'])): ?>
                                                    <div class="col-md-6">
                                                        <h6>Treatment</h6>
                                                        <p class="text-muted"><?php echo nl2br($record['treatment']); ?></p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($record['prescription'])): ?>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h6>Prescription</h6>
                                                    <p class="text-muted"><?php echo nl2br($record['prescription']); ?></p>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($record['outcome'])): ?>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h6>Outcome</h6>
                                                    <p class="text-muted"><?php echo nl2br($record['outcome']); ?></p>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                <h5>No Medical History Found</h5>
                                <p class="text-muted">This patient doesn't have any medical records yet.</p>
                                <a href="doctorsnotes.php?patient_id=<?php echo $patient_id; ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Add Medical Notes
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
