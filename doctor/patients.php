<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor') {
    header("Location: ../login/index.php");
    exit();
}
include("../includes/dbconn.php");

$doctor_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VetCare Pro - My Patients</title>
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
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="patients.php"><i class="fas fa-paw me-2"></i>My Patients</a>
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
                    <h2>My Patients</h2>
                </div>

                <!-- Patients Table -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-paw me-2"></i>Patients I've Treated</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Patient Name</th>
                                        <th>Species</th>
                                        <th>Breed</th>
                                        <th>Age</th>
                                        <th>Owner</th>
                                        <th>Last Visit</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = $conn->query("
                                        SELECT DISTINCT p.*, o.name as owner_name, o.contact,
                                               MAX(a.date) as last_visit
                                        FROM patients p
                                        JOIN owners o ON p.owner_id = o.id
                                        JOIN appointments a ON p.id = a.patient_id
                                        WHERE a.doctor_id = $doctor_id
                                        GROUP BY p.id
                                        ORDER BY last_visit DESC
                                    ");

                                    while ($row = $result->fetch_assoc()) {
                                        $breed = $row['breed'] ? $row['breed'] : 'N/A';
                                        $age = $row['age'] ? $row['age'] : 'N/A';
                                        echo "<tr>
                                            <td>{$row['name']}</td>
                                            <td>{$row['species']}</td>
                                            <td>{$breed}</td>
                                            <td>{$age}</td>
                                            <td>{$row['owner_name']}<br><small class='text-muted'>{$row['contact']}</small></td>
                                            <td>{$row['last_visit']}</td>
                                            <td>
                                                <a href='history.php?patient_id={$row['id']}' class='btn btn-sm btn-info'>View History</a>
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
</body>
</html>