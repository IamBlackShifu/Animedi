<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor') {
    header("Location: ../login/index.php");
    exit();
}
include("../includes/dbconn.php");

$doctor_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['notesSubmit'])) {
    $appointment_id = $_POST['appointment_id'];
    $history = $_POST['history'];
    $tpr = $_POST['tpr'];
    $physical_exam = $_POST['physical_exam'];
    $diagnosis = $_POST['diagnosis'];
    $treatment = $_POST['treatment'];
    $prescription = $_POST['prescription'];
    $outcome = $_POST['outcome'];

    $sql = "INSERT INTO records (appointment_id, history, tpr, physical_exam, diagnosis, treatment, prescription, outcome)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssss", $appointment_id, $history, $tpr, $physical_exam, $diagnosis, $treatment, $prescription, $outcome);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Medical notes saved successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error saving notes: " . $conn->error . "</div>";
    }
    $stmt->close();
}

// Get appointments for this doctor
$appointments_result = $conn->query("
    SELECT a.*, p.name as patient_name, p.species, p.breed, p.age, o.name as owner_name, o.contact
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN owners o ON p.owner_id = o.id
    WHERE a.doctor_id = $doctor_id AND a.status IN ('scheduled', 'in_progress')
    ORDER BY a.date, a.time
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VetCare Pro - Doctor Notes</title>
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
        .form-section { margin-bottom: 30px; }
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
                        <a class="nav-link" href="history.php"><i class="fas fa-history me-2"></i>Patient History</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="doctorsnotes.php"><i class="fas fa-sticky-note me-2"></i>Doctor Notes</a>
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
                    <h2><i class="fas fa-sticky-note me-2"></i>Doctor Notes</h2>
                </div>

                <?php echo $message; ?>

                <!-- Select Appointment -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-calendar-check me-2"></i>Select Appointment</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="mb-3">
                            <div class="row">
                                <div class="col-md-8">
                                    <select name="appointment_id" class="form-select" onchange="this.form.submit()">
                                        <option value="">Choose an appointment...</option>
                                        <?php while ($appointment = $appointments_result->fetch_assoc()): ?>
                                            <option value="<?php echo $appointment['id']; ?>"
                                                    <?php echo (isset($_GET['appointment_id']) && $_GET['appointment_id'] == $appointment['id']) ? 'selected' : ''; ?>>
                                                <?php echo $appointment['patient_name']; ?> (<?php echo $appointment['species']; ?>) -
                                                <?php echo date('M d, Y', strtotime($appointment['date'])); ?> at <?php echo $appointment['time']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if (isset($_GET['appointment_id']) && $_GET['appointment_id']): ?>
                    <?php
                    $appointment_id = $_GET['appointment_id'];
                    $appointment_result = $conn->query("
                        SELECT a.*, p.name as patient_name, p.species, p.breed, p.age, p.notes, o.name as owner_name, o.contact
                        FROM appointments a
                        JOIN patients p ON a.patient_id = p.id
                        JOIN owners o ON p.owner_id = o.id
                        WHERE a.id = $appointment_id AND a.doctor_id = $doctor_id
                    ");
                    $appointment = $appointment_result->fetch_assoc();
                    ?>

                    <!-- Patient Info -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5><i class="fas fa-paw me-2"></i>Patient Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Name:</strong> <?php echo $appointment['patient_name']; ?></p>
                                    <p><strong>Species:</strong> <?php echo $appointment['species']; ?></p>
                                    <p><strong>Breed:</strong> <?php echo $appointment['breed'] ?: 'N/A'; ?></p>
                                    <p><strong>Age:</strong> <?php echo $appointment['age'] ?: 'N/A'; ?> years</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Owner:</strong> <?php echo $appointment['owner_name']; ?></p>
                                    <p><strong>Contact:</strong> <?php echo $appointment['contact']; ?></p>
                                    <p><strong>Appointment:</strong> <?php echo date('F d, Y', strtotime($appointment['date'])); ?> at <?php echo $appointment['time']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Medical Notes Form -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-notes-medical me-2"></i>Medical Notes</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">

                                <div class="form-section">
                                    <h6>History</h6>
                                    <textarea name="history" class="form-control" rows="3" placeholder="Patient history, presenting complaint, etc."></textarea>
                                </div>

                                <div class="form-section">
                                    <h6>TPR (Temperature, Pulse, Respiration)</h6>
                                    <textarea name="tpr" class="form-control" rows="2" placeholder="Temperature: , Pulse: , Respiration: "></textarea>
                                </div>

                                <div class="form-section">
                                    <h6>Physical Exam</h6>
                                    <textarea name="physical_exam" class="form-control" rows="4" placeholder="Physical examination findings"></textarea>
                                </div>

                                <div class="form-section">
                                    <h6>Diagnosis</h6>
                                    <textarea name="diagnosis" class="form-control" rows="3" placeholder="Diagnosis and differential diagnosis"></textarea>
                                </div>

                                <div class="form-section">
                                    <h6>Treatment</h6>
                                    <textarea name="treatment" class="form-control" rows="3" placeholder="Treatment plan and procedures"></textarea>
                                </div>

                                <div class="form-section">
                                    <h6>Prescription</h6>
                                    <textarea name="prescription" class="form-control" rows="3" placeholder="Medications and dosages"></textarea>
                                </div>

                                <div class="form-section">
                                    <h6>Outcome</h6>
                                    <textarea name="outcome" class="form-control" rows="2" placeholder="Outcome and follow-up instructions"></textarea>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" name="notesSubmit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Notes
                                    </button>
                                    <a href="records.php" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
