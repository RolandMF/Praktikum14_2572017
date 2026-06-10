<?php
// 2572017 - Roland Michael Febrian
session_start();

// Koneksi ke Database
$host = 'localhost';
$db   = 'latihan_login';
$user = 'root';
$pass = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

$error = '';
$success = '';

// Proses Logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: Tugas2_2572017.php");
    exit();
}

// Proses Form (Login & Register)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // ==========================================
    // LOGIKA REGISTER
    // ==========================================
    if (isset($_POST['register'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        // Cek apakah email atau username sudah terdaftar
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email OR username = :username");
        $stmt->execute(['email' => $email, 'username' => $username]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Email atau Username sudah terdaftar.";
        } else {
            // Hash password untuk keamanan
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $insert->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashed_password
            ]);
            $success = "Data sudah disimpan. Silakan Login.";
        }
    } 
    // ==========================================
    // LOGIKA LOGIN
    // ==========================================
    elseif (isset($_POST['login'])) {
        $login_id = trim($_POST['login_id']); 
        $password = $_POST['password'];

        // Cari user berdasarkan email atau username
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :login_id OR username = :login_id");
        $stmt->execute(['login_id' => $login_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifikasi password
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['username'];
            header("Location: Tugas2_2572017.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Register - 2572017</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php if (isset($_SESSION['user'])): ?>
        <div class="alert alert-success">
            <h4>Selamat datang, <?= htmlspecialchars($_SESSION['user']) ?></h4>
        </div>
        <a href="?action=logout" class="btn btn-danger">Logout</a>
    <?php else: ?>
        <div class="row justify-content-center">
            
            <div class="col-md-10">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
            </div>

            <div class="col-md-5 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">Login</h3>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Email / Username</label>
                                <input type="text" name="login_id" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-success w-100 mb-3">Login</button>
                            <p class="text-center mb-0">Belum punya akun? <a href="#" class="text-decoration-none">Register</a></p>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-5 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">Register</h3>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" name="register" class="btn btn-primary w-100 mb-3">Register</button>
                            <p class="text-center mb-0">Sudah punya akun? <a href="#" class="text-decoration-none">Login</a></p>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>