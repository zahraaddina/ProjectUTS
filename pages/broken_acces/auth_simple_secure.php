<?php
// auth_simple_secure.php
session_start();
require_once '../../config/db.php'; // file yang menginisialisasi $pdo (PDO)

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Ambil id dari GET dan cast ke integer (menghindari string injection)
$requestedId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$loggedInUser = (int) $_SESSION['user_id'];

// Otorisasi:
// - Jika user meminta data yang bukan miliknya, tolak kecuali user adalah admin.
// Pastikan session menyimpan flag admin jika ada (contoh: $_SESSION['is_admin'] = true)
$isAdmin = !empty($_SESSION['is_admin']) && $_SESSION['is_admin'] == true;

if ($requestedId <= 0) {
    http_response_code(400);
    echo "Invalid ID.";
    exit;
}

// Jika bukan pemilik dan bukan admin -> forbidden
if ($requestedId !== $loggedInUser && !$isAdmin) {
    http_response_code(403);
    echo "403 Forbidden - Anda tidak berwenang melihat data ini.";
    exit;
}

try {
    // Prepared statement aman: hanya satu parameter :id digunakan
    $stmt = $pdo->prepare("SELECT id, username, email, full_name FROM users WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $requestedId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Jangan tampilkan detail error di produksi — cukup log di server
    error_log("DB error: " . $e->getMessage());
    http_response_code(500);
    echo "Terjadi kesalahan server.";
    exit;
}

if (!$user) {
    echo "Data pengguna tidak ditemukan.";
    exit;
}

// Tampilkan halaman — escape output dengan htmlspecialchars
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Profil User (Aman)</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body{font-family:'Poppins',sans-serif;background:#f7f4fb;padding:30px}
        .card{max-width:520px;margin:24px auto;background:#fff;border-radius:16px;padding:28px;box-shadow:0 8px 30px rgba(0,0,0,0.06)}
        h2{color:#3b185f;margin-bottom:12px}
        .field{margin:12px 0}
        .label{font-size:13px;color:#6b5380}
        .value{padding:10px 12px;background:#fff;border:1px solid #eee;border-radius:10px}
        .back{display:inline-block;margin-top:16px;color:#6b5380;text-decoration:none}
    </style>
</head>
<body>
    <div class="card">
        <a class="back" href="dashboard.php">← Kembali ke Dashboard</a>
        <h2>Profil User (AMAN)</h2>

        <div class="field">
            <div class="label">ID</div>
            <div class="value"><?php echo htmlspecialchars($user['id']); ?></div>
        </div>

        <div class="field">
            <div class="label">Username</div>
            <div class="value"><?php echo htmlspecialchars($user['username']); ?></div>
        </div>

        <div class="field">
            <div class="label">Email</div>
            <div class="value"><?php echo htmlspecialchars($user['email']); ?></div>
        </div>

        <div class="field">
            <div class="label">Full Name</div>
            <div class="value"><?php echo htmlspecialchars($user['full_name'] ?? '-'); ?></div>
        </div>
    </div>
</body>
</html>
