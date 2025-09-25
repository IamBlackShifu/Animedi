<?php
session_start();
include("../includes/dbconn.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor') {
    header("Location: ../login/index.php");
    exit();
}

$doctor_id = $_SESSION['user_id'];
$today = date('Y-m-d');
$saturday = date('Y-m-d', strtotime('saturday this week'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VetCare Pro - Doctor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/theme-infinityloc.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar p-3">
            <h4 class="text-center mb-4">VetCare Pro</h4>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="patients.php"><i class="fas fa-paw me-2"></i>My Patients</a></li>
                <li class="nav-item"><a class="nav-link" href="records.php"><i class="fas fa-file-medical me-2"></i>Medical Records</a></li>
                <li class="nav-item"><a class="nav-link" href="history.php"><i class="fas fa-history me-2"></i>Patient History</a></li>
                <li class="nav-item"><a class="nav-link" href="doctorsnotes.php"><i class="fas fa-sticky-note me-2"></i>Doctor Notes</a></li>
                <li class="nav-item"><a class="nav-link" href="clientdata.php"><i class="fas fa-users me-2"></i>Client Data</a></li>
                <li class="nav-item mt-4"><a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 p-4 dashboard-anim">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="fas fa-user-md me-2"></i>Welcome, Dr. <?php echo $_SESSION['user_name']; ?></h3>
            </div>

            <!-- Dashboard Cards -->
            <div class="row mb-4">
                <?php
                $cards = [
                    ['title'=>'Today\'s Appointments', 'icon'=>'calendar', 'color'=>'primary', 'query'=>"SELECT COUNT(*) as count FROM appointments WHERE doctor_id = $doctor_id AND date = '$today'"],
                    ['title'=>'In Progress', 'icon'=>'play', 'color'=>'warning', 'query'=>"SELECT COUNT(*) as count FROM appointments WHERE doctor_id = $doctor_id AND status = 'in_progress'"],
                    ['title'=>'Completed Today', 'icon'=>'check-circle', 'color'=>'success', 'query'=>"SELECT COUNT(*) as count FROM appointments WHERE doctor_id = $doctor_id AND date = '$today' AND status = 'completed'"],
                    ['title'=>'Total Records', 'icon'=>'file-medical', 'color'=>'info', 'query'=>"SELECT COUNT(*) as count FROM records r JOIN appointments a ON r.appointment_id = a.id WHERE a.doctor_id = $doctor_id"]
                ];

                foreach($cards as $card) {
                    $result = $conn->query($card['query']);
                    $count = $result->fetch_assoc()['count'];
                    echo "
                    <div class='col-md-3'>
                        <div class='card text-center'>
                            <div class='card-body'>
                                <i class='fas fa-{$card['icon']} fa-2x text-{$card['color']} mb-2'></i>
                                <h5 class='card-title'>{$card['title']}</h5>
                                <h3 class='text-{$card['color']}'>{$count}</h3>
                            </div>
                        </div>
                    </div>";
                }
                ?>
            </div>

            <!-- This Week's Appointments -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-calendar me-2"></i>Appointments This Week (Today → Saturday)</h5>
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
                                $stmt = $conn->prepare("
                                    SELECT a.*, p.name as patient_name, p.species
                                    FROM appointments a
                                    JOIN patients p ON a.patient_id = p.id
                                    WHERE a.doctor_id = ?
                                      AND a.date BETWEEN ? AND ?
                                    ORDER BY a.date, a.time ASC
                                ");
                                $stmt->bind_param("iss", $doctor_id, $today, $saturday);
                                $stmt->execute();
                                $result = $stmt->get_result();

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

            <div class="attribution mt-4">
                <strong>Infinity Lines of Code</strong>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
