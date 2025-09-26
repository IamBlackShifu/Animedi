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
            <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i><span>Dashboard</span></a></li>
            <li class="nav-item"><a class="nav-link" href="patients.php"><i class="fas fa-paw me-2"></i><span>My Patients</span></a></li>
            <li class="nav-item"><a class="nav-link" href="records.php"><i class="fas fa-file-medical me-2"></i><span>Medical Records</span></a></li>
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
        <h3 class="navbar-brand mb-0">Welcome, Dr. <?php echo $_SESSION['user_name']; ?></h3>
    </div>
</nav>

<!-- Main Content -->
<div class="main-content" id="main-content">
    <div class="dashboard-anim">

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
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-calendar-day me-1"></i>Date</th>
                                    <th><i class="fas fa-clock me-1"></i>Time</th>
                                    <th><i class="fas fa-paw me-1"></i>Patient</th>
                                    <th><i class="fas fa-info-circle me-1"></i>Status</th>
                                    <th><i class="fas fa-cogs me-1"></i>Actions</th>
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
