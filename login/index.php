<?php
// Infinity Lines of Code - Login logic must be at the top before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) {
    header("Location: ../" . $_SESSION['role'] . "/dashboard.php");
    exit();
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("../includes/dbconn.php");
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT id, name, role, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            header("Location: ../" . $user['role'] . "/dashboard.php");
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "User not found";
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VetCare Pro - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            font-family: 'Montserrat', Arial, sans-serif;
            background: linear-gradient(120deg, #43cea2 0%, #185a9d 100%);
            overflow-x: hidden;
        }
        .paw-bg {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            z-index: 0;
            pointer-events: none;
            opacity: 0.08;
            background-image: url('https://cdn-icons-png.flaticon.com/512/616/616408.png');
            background-size: 120px;
            background-repeat: repeat;
            animation: pawMove 20s linear infinite;
        }
        @keyframes pawMove {
            0% { background-position: 0 0; }
            100% { background-position: 200px 200px; }
        }
        .login-container {
            background: rgba(255,255,255,0.97);
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(24,90,157,0.18);
            padding: 48px 36px 32px 36px;
            width: 100%;
            max-width: 410px;
            margin: 60px auto;
            position: relative;
            z-index: 2;
            opacity: 0;
            transform: translateY(40px);
            animation: fadeInUp 1.1s cubic-bezier(.23,1.01,.32,1) 0.2s forwards;
        }
        @keyframes fadeInUp {
            to { opacity: 1; transform: none; }
        }
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-header img {
            width: 64px;
            margin-bottom: 10px;
            animation: logoPop 1.2s cubic-bezier(.23,1.01,.32,1);
        }
        @keyframes logoPop {
            0% { transform: scale(0.7) rotate(-10deg); opacity: 0; }
            60% { transform: scale(1.1) rotate(8deg); opacity: 1; }
            100% { transform: scale(1) rotate(0); }
        }
        .login-header h2 {
            color: #185a9d;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .login-header p {
            color: #666;
            font-size: 1rem;
        }
        .btn-login {
            background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%);
            border: none;
            padding: 13px;
            width: 100%;
            border-radius: 6px;
            color: #fff;
            font-weight: 600;
            font-size: 1.1rem;
            box-shadow: 0 2px 8px rgba(67,206,162,0.08);
            transition: background 0.2s, box-shadow 0.2s;
        }
        .btn-login:hover {
            background: linear-gradient(90deg, #185a9d 0%, #43cea2 100%);
            box-shadow: 0 4px 16px rgba(24,90,157,0.13);
        }
        .form-label {
            color: #185a9d;
            font-weight: 600;
        }
        .form-control:focus {
            border-color: #43cea2;
            box-shadow: 0 0 0 2px #43cea233;
        }
        .attribution {
            text-align: center;
            margin-top: 32px;
            color: #888;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }
        @media (max-width: 600px) {
            .login-container { padding: 28px 8px 18px 8px; }
        }
    </style>
</head>
<body>
    <div class="paw-bg"></div>
    <div class="login-container" role="main" aria-label="Login form">
        <div class="login-header">
            <img src="https://cdn-icons-png.flaticon.com/512/616/616408.png" alt="VetCare Pro Logo" />
            <h2>VetCare Pro</h2>
            <p class="text-muted">Veterinary Practice Management System</p>
        </div>

        <?php
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['user_id'])) {
            header("Location: ../" . $_SESSION['role'] . "/dashboard.php");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            include("../includes/dbconn.php");

            $email = $_POST['email'];
            $password = $_POST['password'];

            $stmt = $conn->prepare("SELECT id, name, role, password FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['role'] = $user['role'];

                    header("Location: ../" . $user['role'] . "/dashboard.php");
                    exit();
                } else {
                    $error = "Invalid password";
                }
            } else {
                $error = "User not found";
            }
            $stmt->close();
            $conn->close();
        }
        ?>

        <form method="POST">
            <?php if (isset($error)): ?>
                <!-- <div class="alert alert-danger"><?php echo $error; ?></div> -->
            <?php endif; ?>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>Login
            </button>
        </form>
        <div class="attribution">
            <strong>Infinity Lines of Code</strong><br>
            &copy; <?php echo date('Y'); ?> VetCare Pro
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>