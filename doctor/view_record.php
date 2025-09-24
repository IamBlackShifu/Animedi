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
                        <a class="nav-link active" href="patients.php"><i class="fas fa-paw me-2"></i>My Patients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="records.php"><i class="fas fa-file-medical me-2"></i>Medical Records</a>
                    </li>
                    <li class="nav-item mt-4">
                        <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Medical Record - <?php echo $record['patient_name']; ?></h2>
                    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                </div>

                <!-- Record Details -->
                <div class="card">
                    <div class="card-header">
                        <h5>Appointment Details</h5>
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

                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Medical Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label"><strong>Diagnosis:</strong></label>
                            <p><?php echo nl2br($record['diagnosis']); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Treatment:</strong></label>
                            <p><?php echo nl2br($record['treatment']); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Prescription:</strong></label>
                            <p><?php echo nl2br($record['prescription']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>