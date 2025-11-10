<?php
// auth_simple_vulnerable.php
// Versi rentan: langsung pakai parameter GET tanpa validasi dan tanpa prepared statement

// koneksi mysqli (procedural)
$mysqli = new mysqli('localhost', 'root', '', 'projectuts');

if ($mysqli->connect_errno) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}

// Ambil id dari GET (TIDAK DISANITASI)
$id = isset($_GET['id']) ? $_GET['id'] : 0;

// Query rentan — langsung menyisipkan $id ke query
$res = $mysqli->query("SELECT * FROM users WHERE id = $id");

if (!$res) {
    die("Query error: " . $mysqli->error);
}

$user = $res->fetch_assoc();

// Tampilkan sederhana
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Profil User (Vulnerable)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body{font-family: Poppins, sans-serif;background:#f7f4fb;padding:30px}
        .card{max-width:520px;margin:20px auto;background:#fff;border-radius:16px;padding:28px;box-shadow:0 8px 30px rgba(0,0,0,0.08)}
        h2{color:#3b185f;margin-bottom:8px}
        .field{margin:12px 0}
        .label{font-size:13px;color:#6b5380}
        .value{padding:10px 12px;background:#fff;border:1px solid #f0e9f6;border-radius:10px}
        .back{display:inline-block;margin-top:16px;color:#6b5380;text-decoration:none}
    </style>
</head>
<body>
    <div class="card">
        <a class="back" href="dashboard.php">← Kembali ke Dashboard</a>
        <h2>Profil User (VULNERABLE)</h2>
        <?php if ($user): ?>
            <div class="field">
                <div class="label">ID</div>
                <div class="value"><?php echo htmlspecialchars($user['id']); ?></div>
            </div>
            <div class="field">
                <div class="label">Username</div>
                <div class="value"><?php echo $user['username']; ?></div>
            </div>
            <div class="field">
                <div class="label">Email</div>
                <div class="value"><?php echo $user['email']; ?></div>
            </div>
            <div class="field">
                <div class="label">Full Name</div>
                <div class="value"><?php echo isset($user['full_name']) ? $user['full_name'] : '-'; ?></div>
            </div>
        <?php else: ?>
            <p style="color:#b02a37">⚠️ Data tidak ditemukan.</p>
        <?php endif; ?>
    </div>
</body>
</html>
