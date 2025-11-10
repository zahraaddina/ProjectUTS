<?php
// create_user_secure.php
session_start();
require_once '../../config/db.php'; // inisialisasi $pdo (PDO)

// Jika halaman ini hanya untuk admin, cek is_admin
// if (empty($_SESSION['is_admin'])) { header('Location: dashboard.php'); exit; }

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil input dan trim
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Validasi sederhana
    if ($username === '') $errors[] = 'Username wajib diisi.';
    if ($password === '') $errors[] = 'Password wajib diisi.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';

    // Cek jika username/email sudah ada
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        $count = (int) $stmt->fetchColumn();
        if ($count > 0) $errors[] = 'Username atau email sudah terdaftar.';
    }

    if (empty($errors)) {
        // Hash password sebelum simpan
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, email) VALUES (:username, :password, :full_name, :email)");
            $stmt->execute([
                'username' => $username,
                'password' => $passwordHash,
                'full_name' => $full_name,
                'email' => $email
            ]);
            $success = 'User berhasil dibuat (aman).';
        } catch (PDOException $e) {
            error_log("DB error: " . $e->getMessage());
            $errors[] = 'Gagal menyimpan ke database.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Create User (Aman)</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body{font-family:'Poppins',sans-serif;background:linear-gradient(90deg,#fff 0%, #fbf7ff 100%);padding:30px}
        .container{max-width:540px;margin:20px auto}
        .card{background:#fff;border-radius:18px;padding:28px;box-shadow:0 10px 30px rgba(106,90,205,0.06)}
        .title{display:flex;align-items:center;gap:14px}
        .circle{width:64px;height:64px;border-radius:14px;background:#e9f8ef;display:flex;align-items:center;justify-content:center;color:#2d8559;font-size:28px}
        h1{font-size:22px;color:#3b185f;margin:0}
        .form-group{margin-top:18px}
        label{display:block;color:#5a4171;font-weight:600;margin-bottom:8px}
        input[type="text"], input[type="password"], input[type="email"]{width:100%;padding:12px;border-radius:12px;border:1px solid #efe7f6;background:#fff;outline:none}
        .btn{display:block;width:100%;margin-top:18px;padding:14px;border-radius:12px;background:#2d8559;color:#fff;font-weight:700;border:none}
        .errors{margin-top:14px;padding:12px;border-radius:10px;background:#ffe6e6;border:1px solid #f3c2c2;color:#8b1f1f}
        .success{margin-top:14px;padding:12px;border-radius:10px;background:#e6ffef;border:1px solid #b7efc8;color:#1f6f3a}
        .small{font-size:13px;color:#6b5380;margin-top:8px}
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="title">
                <div class="circle">✔</div>
                <div>
                    <h1>Create User</h1>
                    <div class="small">Versi aman: prepared statement + password hashing + validasi</div>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="errors">
                    <ul style="margin:0 0 0 18px;">
                        <?php foreach ($errors as $err): ?>
                            <li><?php echo htmlspecialchars($err); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php elseif ($success): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>

                <button class="btn" type="submit">Buat User (Aman)</button>
                <div class="small">✔ Password disimpan menggunakan <code>password_hash()</code>. ✔ Semua input diproses lewat prepared statement.</div>
            </form>
        </div>
    </div>
</body>
</html>
