<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor') {
    header("Location: ../login/index.php");
    exit();
}
include("../includes/dbconn.php");

$appointment_id = $_GET['id'] ?? 0;

// Get appointment details
$stmt = $conn->prepare("
    SELECT a.*, p.name as patient_name, p.species, p.breed, p.age, p.notes, o.name as owner_name, o.contact
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN owners o ON p.owner_id = o.id
    WHERE a.id = ? AND a.doctor_id = ?
");
$stmt->bind_param("ii", $appointment_id, $_SESSION['user_id']);
$stmt->execute();
$appointment = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$appointment) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $diagnosis = $_POST['diagnosis'];
    $treatment = $_POST['treatment'];
    $prescription = $_POST['prescription'];

    // Insert record
    $stmt = $conn->prepare("INSERT INTO records (appointment_id, diagnosis, treatment, prescription) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $appointment_id, $diagnosis, $treatment, $prescription);
    $stmt->execute();
    $stmt->close();

    // Update appointment status
    $stmt = $conn->prepare("UPDATE appointments SET status = 'completed' WHERE id = ?");
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $stmt->close();

    // Create billing record
    $amount = $_POST['amount'] ?? 0;
    if ($amount > 0) {
        $stmt = $conn->prepare("INSERT INTO billing (appointment_id, amount) VALUES (?, ?)");
        $stmt->bind_param("id", $appointment_id, $amount);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: dashboard.php?success=Record saved successfully");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VetCare Pro - Medical Record</title>
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
                    <h2>Medical Record - <?php echo $appointment['patient_name']; ?></h2>
                    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                </div>

                <!-- Patient Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Patient Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Patient:</strong> <?php echo $appointment['patient_name']; ?></p>
                                <p><strong>Species:</strong> <?php echo $appointment['species']; ?></p>
                                <p><strong>Breed:</strong> <?php echo $appointment['breed']; ?></p>
                                <p><strong>Age:</strong> <?php echo $appointment['age']; ?> years</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Owner:</strong> <?php echo $appointment['owner_name']; ?></p>
                                <p><strong>Contact:</strong> <?php echo $appointment['contact']; ?></p>
                                <p><strong>Appointment:</strong> <?php echo $appointment['date'] . ' ' . $appointment['time']; ?></p>
                                <p><strong>Notes:</strong> <?php echo $appointment['notes']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Medical Record Form -->
                <div class="card">
                    <div class="card-header">
                        <h5>Medical Record</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Diagnosis</label>
                                <textarea class="form-control" name="diagnosis" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Treatment</label>
                                <textarea class="form-control" name="treatment" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Prescription</label>
                                <textarea class="form-control" name="prescription" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Consultation Fee</label>
                                <input type="number" class="form-control" name="amount" step="0.01" placeholder="0.00">
                            </div>
                            <button type="submit" class="btn btn-success">Save Record & Complete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>