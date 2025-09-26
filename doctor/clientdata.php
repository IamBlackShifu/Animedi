<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'doctor') {
    header("Location: ../login/index.php");
    exit();
}
include("../includes/dbconn.php");

$doctor_id = $_SESSION['user_id'];
$message = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VetCare Pro - Client Data</title>
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
        .form-section { margin-bottom: 30px; }
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
            <li class="nav-item"><a class="nav-link" href="records.php"><i class="fas fa-file-medical me-2"></i><span>Medical Records</span></a></li>
            <li class="nav-item"><a class="nav-link" href="history.php"><i class="fas fa-history me-2"></i><span>Patient History</span></a></li>
            <li class="nav-item"><a class="nav-link" href="doctorsnotes.php"><i class="fas fa-sticky-note me-2"></i><span>Doctor Notes</span></a></li>
            <li class="nav-item"><a class="nav-link active" href="clientdata.php"><i class="fas fa-users me-2"></i><span>Client Data</span></a></li>
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
        <h3 class="navbar-brand mb-0">Client Data - Dr. <?php echo $_SESSION['user_name']; ?></h3>
    </div>
</nav>

<!-- Main Content -->
<div class="main-content" id="main-content">
    <div class="container-fluid">
        <?php echo $message; ?>

        <!-- Client Records Table -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-users me-2"></i>Client Records</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-1"></i>ID</th>
                                <th><i class="fas fa-user me-1"></i>Client Name</th>
                                <th><i class="fas fa-tag me-1"></i>Client Type</th>
                                <th><i class="fas fa-paw me-1"></i>Pet Name</th>
                                <th><i class="fas fa-map-marker-alt me-1"></i>Address</th>
                                <th><i class="fas fa-calendar me-1"></i>Date</th>
                                <th><i class="fas fa-cogs me-1"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM patient_details";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $id = $row['id'];
                                    $OwnerName = $row['OwnerName'];
                                    $OwnerSurname = $row['OwnerSurname'];
                                    $client = $row['client'];
                                    $AnimalName = $row['AnimalName'];
                                    $Date = $row['date'];
                                    $address = $row['address'];
                            ?>
                            <tr>
                                <td><?php echo $id; ?></td>
                                <td><?php echo $OwnerName . " " . $OwnerSurname; ?></td>
                                <td><?php echo $client; ?></td>
                                <td><?php echo $AnimalName; ?></td>
                                <td><?php echo $address; ?></td>
                                <td><?php echo date('M d, Y', strtotime($Date)); ?></td>
                                <td>
                                    <a href="history.php?id=<?php echo $id; ?>" class="btn btn-sm btn-primary me-2">
                                        <i class="fas fa-history me-1"></i>History
                                    </a>
                                    <a href="doctorsnotes.php?id=<?php echo $id; ?>" class="btn btn-sm btn-success">
                                        <i class="fas fa-plus me-1"></i>New Record
                                    </a>
                                </td>
                            </tr>
                            <?php
                                }
                            } else {
                            ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">No client records found.</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        const navbar = document.getElementById('navbar');
        const mainContent = document.getElementById('main-content');

        sidebar.classList.toggle('collapsed');
        navbar.classList.toggle('collapsed');
        mainContent.classList.toggle('collapsed');
    });
</script>
</body>
</html>


