<?php
session_start();
$err = '';

function pdo_connect() {
    // sesuaikan koneksi database kamu di sini
    $host = 'localhost';
    $db   = 'nama_database';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    return new PDO($dsn, $user, $pass, $opt);
}

// simple CSRF token (lab/demo)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // CSRF check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $err = 'Invalid request (CSRF).';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $err = 'Username dan password wajib diisi.';
        } else {
            $pdo = pdo_connect();
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :u LIMIT 1");
            $stmt->execute([':u' => $username]);
            $u = $stmt->fetch();

            if ($u) {
                $ok = false;
                if (password_verify($password, $u['password'])) {
                    $ok = true;
                } elseif ($password === $u['password']) { // fallback plaintext
                    $ok = true;
                }

                if ($ok) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $u['id'];
                    unset($_SESSION['csrf_token']);
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $err = 'Login gagal: username atau password salah.';
                }
            } else {
                $err = 'Login gagal: username atau password salah.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login — Lab</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../css/login.css" rel="stylesheet">
</head>
<body>
  <div class="card card-login">
    <div class="card-body p-4">
      <div class="text-center mb-3">
        <div class="brand mx-auto mb-2">XSS</div>
        <h4 class="card-title mb-0">Selamat datang</h4>
        <small class="text-muted">Masuk untuk melanjutkan</small>
      </div>

      <?php if($err): ?>
        <div class="alert alert-danger" role="alert">
          <?= htmlspecialchars($err); ?>
        </div>
      <?php endif; ?>

      <form method="post" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">

        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input id="username" name="username" class="form-control" placeholder="masukkan username" required
                 value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
        </div>

        <div class="mb-3">
          <label for="password" class="form-label d-flex justify-content-between">
            <span>Password</span>
            <a href="#" class="small">Lupa password?</a>
          </label>
          <input id="password" name="password" type="password" class="form-control" placeholder="••••••••" required>
        </div>

        <div class="d-grid">
          <button class="btn btn-primary" type="submit">Masuk</button>
        </div>
      </form>

      <div class="text-center mt-3 form-footer">
        <span>Belum punya akun? <a href="register.php">Daftar</a></span>
      </div>
    </div>
    <div class="card-footer text-center small text-muted">
      Untuk keperluan lab: password bisa berupa plaintext. Di produksi, gunakan <code>password_hash()</code>.
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('username')?.focus();
  </script>
</body>
</html>
