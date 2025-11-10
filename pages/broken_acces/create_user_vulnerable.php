<?php
// create_user_vulnerable.php
// Tampilan form + proses insert (rentan XSS & SQL Injection karena input langsung dimasukkan ke query)

$mysqli = new mysqli('localhost', 'root', '', 'projectuts');

if ($mysqli->connect_errno) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}

$notice = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $full_name = $_POST['full_name'] ?? '';

    // RENTAN: menyimpan password plain dan langsung menyisipkan input ke query
    $sql = "INSERT INTO users (username, password, full_name, email) VALUES ('$username', '$password', '$full_name', '')";
    if ($mysqli->query($sql)) {
        $notice = 'User berhasil dibuat (Vulnerable).';
    } else {
        $notice = 'Error: ' . $mysqli->error;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Create User (Vulnerable)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body{font-family:'Poppins',sans-serif;background:linear-gradient(90deg,#fff 0%, #fbf7ff 100%);padding:30px}
        .container{max-width:540px;margin:20px auto}
        .card{background:#fff;border-radius:18px;padding:28px;box-shadow:0 10px 30px rgba(106,90,205,0.06)}
        .title{display:flex;align-items:center;gap:14px}
        .circle{width:64px;height:64px;border-radius:14px;background:#ffe9ee;display:flex;align-items:center;justify-content:center;color:#d23b4b;font-size:28px}
        h1{font-size:22px;color:#3b185f;margin:0}
        .badge{display:inline-block;margin-top:10px;padding:8px 12px;border-radius:20px;background:#ffecec;color:#b02a37;font-weight:600;font-size:12px}
        .form-group{margin-top:18px}
        label{display:block;color:#5a4171;font-weight:600;margin-bottom:8px}
        input[type="text"], input[type="password"]{width:100%;padding:12px;border-radius:12px;border:1px solid #efe7f6;background:#fff;outline:none}
        .btn{display:block;width:100%;margin-top:18px;padding:14px;border-radius:12px;background:#d83a45;color:#fff;font-weight:700;border:none;box-shadow:0 8px 18px rgba(216,58,69,0.18)}
        .warn{margin-top:14px;padding:12px;border-radius:10px;background:#fff6e6;border:1px solid #f1d6a0;color:#7a4b1a}
        .small{font-size:13px;color:#6b5380;margin-top:8px}
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="title">
                <div class="circle">👤+</div>
                <div>
                    <h1>Create User</h1>
                    <div class="badge">VULNERABLE - XSS & SQL Injection</div>
                </div>
            </div>

            <?php if ($notice): ?>
                <div class="warn"><?php echo $notice; ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Masukkan username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                </div>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" placeholder="Nama lengkap (opsional)">
                </div>

                <button class="btn" type="submit">Buat User (Vulnerable)</button>
                <div class="small">⚠️ Contoh ini rentan: menyimpan password tanpa hash, input langsung ke SQL, dan hasil echo tidak di-escape.</div>
            </form>
        </div>
    </div>
</body>
</html>
