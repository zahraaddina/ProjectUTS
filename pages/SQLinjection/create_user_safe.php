<?php
// create_user_safe.php
// SAFE user creation form — gunakan untuk praktikum mahasiswa / disebarkan

session_start();

$dsn = 'mysql:host=127.0.0.1;dbname=praktek_sqli;charset=utf8mb4';
$dbUser = 'root';
$dbPass = ''; // sesuaikan jika perlu

// generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        $errors[] = 'Token CSRF tidak valid.';
    }

    // read and trim inputs
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $fullname = trim($_POST['full_name'] ?? '');

    // basic validation
    if ($username === '' || $password === '') {
        $errors[] = 'Username dan password wajib diisi.';
    } else {
        if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
            $errors[] = 'Username hanya boleh huruf, angka, underscore; 3-30 karakter.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Password minimal 8 karakter.';
        }
    }

    if (empty($errors)) {
        try {
            $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

            // Periksa username sudah ada
            $stmt = $pdo->prepare("SELECT id FROM users_safe WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $errors[] = 'Username sudah terdaftar. Pilih username lain.';
            } else {
                // hash password
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                // prepared statement (aman)
                $stmt = $pdo->prepare("INSERT INTO users_safe (username, password_hash, full_name) VALUES (?, ?, ?)");
                $stmt->execute([$username, $passwordHash, $fullname]);

                $message = "User aman berhasil dibuat: " . htmlspecialchars($username);

                // regenerate CSRF token after success to avoid form resubmission risk
                $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
            }
        } catch (PDOException $e) {
            // log server-side dalam implementasi nyata
            $errors[] = 'Terjadi kesalahan server. Coba lagi nanti.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User (SAFE) — Lab Keamanan Web</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #ffffff 0%, #f9f7fb 100%);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            color: #4a3055;
            padding: 20px;
        }

        .wrap {
            max-width: 600px;
            margin: 0 auto;
        }

        /* Topbar */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
            background: #ffffff;
            padding: 20px 28px;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(94, 58, 127, 0.08);
            border: 2px solid #f0ebf5;
        }

        .d-flex {
            display: flex;
        }

        .align-items-center {
            align-items: center;
        }

        .gap-3 {
            gap: 16px;
        }

        .brand {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #28a745, #218838);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.25);
        }

        .brand-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #4a2d5f;
        }

        .brand-subtitle {
            font-size: 0.9rem;
            color: #28a745;
            margin-top: 2px;
        }

        .badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #e6f7ed;
            color: #218838;
        }

        /* Form Card */
        .form-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 6px 20px rgba(94, 58, 127, 0.08);
            border: 2px solid #c3e6cd;
        }

        .form-card h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #4a2d5f;
            margin-bottom: 8px;
        }

        .form-subtitle {
            color: #7c5699;
            font-size: 0.9rem;
            margin-bottom: 24px;
        }

        /* Alert Messages */
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #e6f7ed;
            border: 1px solid #c3e6cd;
            color: #218838;
        }

        .alert-danger {
            background: #ffe5e8;
            border: 1px solid #ffccd3;
            color: #c82333;
        }

        .alert ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .alert li {
            margin: 4px 0;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 600;
            color: #4a2d5f;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0d5eb;
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            color: #4a3055;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #28a745;
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
        }

        .input-hint {
            font-size: 0.8rem;
            color: #7c5699;
            margin-top: 4px;
        }

        /* Button */
        .btn {
            width: 100%;
            padding: 14px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-family: 'Poppins', sans-serif;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745, #218838);
            color: white;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.25);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.35);
        }

        .btn-outline {
            background: transparent;
            color: #7c5699;
            border: 2px solid #e0d5eb;
            margin-top: 12px;
        }

        .btn-outline:hover {
            background: #f9f7fb;
            border-color: #7c5699;
        }

        /* Security Features Box */
        .security-box {
            background: #e6f7ed;
            border-left: 4px solid #28a745;
            padding: 16px 20px;
            border-radius: 8px;
            margin-top: 24px;
        }

        .security-box h4 {
            font-size: 0.9rem;
            font-weight: 600;
            color: #218838;
            margin-bottom: 8px;
        }

        .security-box ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .security-box li {
            font-size: 0.85rem;
            color: #4a3055;
            padding: 4px 0;
            padding-left: 20px;
            position: relative;
        }

        .security-box li:before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #28a745;
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .form-card {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <!-- Topbar -->
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <div class="brand">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <div>
                    <div class="brand-title">Create User (SAFE)</div>
                    <div class="brand-subtitle">Versi Aman dengan Proteksi</div>
                </div>
            </div>
            <span class="badge">PROTECTED</span>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <h2>Buat User Baru</h2>
            <p class="form-subtitle">Form pembuatan user dengan keamanan lengkap</p>

            <?php if ($message): ?>
                <div class="alert alert-success">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span><?= $message ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required value="<?= isset($username) ? htmlspecialchars($username) : '' ?>" placeholder="Masukkan username">
                    <div class="input-hint">3-30 karakter: huruf, angka, atau underscore</div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Minimal 8 karakter">
                    <div class="input-hint">Gunakan kombinasi huruf, angka, dan simbol</div>
                </div>

                <div class="form-group">
                    <label>Nama Lengkap (Opsional)</label>
                    <input type="text" name="full_name" value="<?= isset($fullname) ? htmlspecialchars($fullname) : '' ?>" placeholder="Masukkan nama lengkap">
                </div>

                <button type="submit" class="btn btn-success">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline-block;vertical-align:middle;margin-right:8px">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Buat User Aman
                </button>

                <button type="button" class="btn btn-outline" onclick="window.location.href='dashboard_sql.php'">
                    Kembali ke Dashboard
                </button>
            </form>

            <div class="security-box">
                <h4>🔒 Fitur Keamanan Aktif:</h4>
                <ul>
                    <li>Validasi input (regex untuk username)</li>
                    <li>CSRF Token Protection</li>
                    <li>Password Hashing (bcrypt)</li>
                    <li>Prepared Statements (SQL Injection safe)</li>
                    <li>HTML Escaping pada output</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>