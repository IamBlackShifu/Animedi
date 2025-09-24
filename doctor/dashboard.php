<?php
session_start();
include("../includes/dbconn.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor') {
    header("Location: ../login/index.php");
    exit();
}

$doctor_id = $_SESSION['user_id'];
$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VetCare Pro - Doctor Dashboard</title>
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
                        <a class="nav-link active" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
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
                    <h2>Welcome, Dr. <?php echo $_SESSION['user_name']; ?></h2>
                </div>

                <!-- Dashboard Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-calendar fa-2x text-primary mb-2"></i>
                                <h5 class="card-title">Today's Appointments</h5>
                                <h3 class="text-primary">
                                    <?php
                                    $result = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE doctor_id = $doctor_id AND date = '$today'");
                                    echo $result->fetch_assoc()['count'];
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-play fa-2x text-warning mb-2"></i>
                                <h5 class="card-title">In Progress</h5>
                                <h3 class="text-warning">
                                    <?php
                                    $result = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE doctor_id = $doctor_id AND status = 'in_progress'");
                                    echo $result->fetch_assoc()['count'];
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <h5 class="card-title">Completed Today</h5>
                                <h3 class="text-success">
                                    <?php
                                    $result = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE doctor_id = $doctor_id AND date = '$today' AND status = 'completed'");
                                    echo $result->fetch_assoc()['count'];
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-file-medical fa-2x text-info mb-2"></i>
                                <h5 class="card-title">Total Records</h5>
                                <h3 class="text-info">
                                    <?php
                                    $result = $conn->query("SELECT COUNT(*) as count FROM records r JOIN appointments a ON r.appointment_id = a.id WHERE a.doctor_id = $doctor_id");
                                    echo $result->fetch_assoc()['count'];
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's Appointments -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-calendar me-2"></i>Today's Appointments</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Patient</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                                                        // Get today's date
                                    $today = date('Y-m-d');

                                    // Get coming Saturday
                                    $saturday = date('Y-m-d', strtotime('saturday this week'));
                                    // Prepare statement
$stmt = $conn->prepare("
    SELECT a.*, p.name as patient_name, p.species, p.breed
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    WHERE a.doctor_id = ?
      AND a.date BETWEEN ? AND ?
    ORDER BY a.time ASC
");

$stmt->bind_param("iss", $doctor_id, $today, $saturday);
$stmt->execute();
                                    $result =  $stmt->get_result();

                                    while ($row = $result->fetch_assoc()) {
                                        $status_class = match($row['status']) {
                                            'scheduled' => 'warning',
                                            'in_progress' => 'primary',
                                            'completed' => 'success',
                                            'cancelled' => 'danger'
                                        };

                                        $actions = match($row['status']) {
                                            'scheduled' => "<a href='examine.php?id={$row['id']}' class='btn btn-sm btn-primary'>Examine</a>",
                                            'in_progress' => "<a href='record.php?id={$row['id']}' class='btn btn-sm btn-success'>Record</a>",
                                            'completed' => "<a href='view_record.php?id={$row['id']}' class='btn btn-sm btn-info'>View</a>",
                                            'cancelled' => "<span class='text-muted'>Cancelled</span>"
                                        };

                                        echo "<tr>
                                            <td>{$row['date']}</td>
                                            <td>{$row['time']}</td>
                                            <td>{$row['patient_name']} ({$row['species']})</td>
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
