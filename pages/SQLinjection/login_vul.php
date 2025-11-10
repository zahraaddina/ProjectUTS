<?php
// login_vul.php  (VERSI RENTAN — DEMO)
session_start();

$dsn = 'mysql:host=127.0.0.1;dbname=praktek_sqli;charset=utf8mb4';
$dbUser = 'root';
$dbPass = '';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        // --- pola rentan: concatenation langsung dengan input user ---
        $sql = "SELECT id, username, password, full_name FROM users_vul
                WHERE username = '" . $username . "' AND password = '" . $password . "'";
        $stmt = $pdo->query($sql);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['demo_mode'] = 'vul';
            header('Location: dashboard.php');
            exit;
        } else {
            $message = 'Username atau password salah.';
        }
    } catch (PDOException $e) {
        $message = 'Terjadi kesalahan server.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — VERSI RENTAN (Demo)</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 480px;
            width: 100%;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #5e3a7f;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            color: #7c5699;
            transform: translateX(-4px);
        }

        .login-box {
            background: #ffffff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(94, 58, 127, 0.12);
            border: 2px solid #ffccd3;
        }

        .header {
            text-align: center;
            margin-bottom: 32px;
        }

        .icon-box {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 20px;
            background: linear-gradient(135deg, #ffe5e8, #ffd1d7);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-box svg {
            stroke: #dc3545;
        }

        h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #4a2d5f;
            margin-bottom: 8px;
        }

        .badge {
            display: inline-block;
            padding: 6px 16px;
            background: #ffe5e8;
            color: #c82333;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .alert {
            background: rgba(255, 228, 232, 0.5);
            border: 1px solid #ffccd3;
            color: #c82333;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 0.95rem;
        }

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
            padding: 14px 16px;
            border: 2px solid #e0d5eb;
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            background: #f9f7fb;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #dc3545;
            background: white;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.25);
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.35);
        }

        .warning-note {
            margin-top: 24px;
            padding: 16px;
            background: #fff9e6;
            border: 1px solid #ffe5a3;
            border-radius: 12px;
            font-size: 0.85rem;
            color: #7c5699;
            line-height: 1.6;
        }

        .warning-note strong {
            color: #4a2d5f;
        }

        @media (max-width: 480px) {
            .login-box {
                padding: 28px;
            }

            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Kembali ke Dashboard
        </a>

        <div class="login-box">
            <div class="header">
                <div class="icon-box">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </div>
                <h2>Login</h2>
                <span class="badge">VULNERABLE - SQL Injection</span>
            </div>

            <?php if ($message): ?>
                <div class="alert"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <button type="submit">Login</button>
            </form>

            <div class="warning-note">
                <strong>⚠️ Peringatan:</strong> Halaman ini sengaja dibuat rentan untuk tujuan pembelajaran. Query SQL menggunakan concatenation langsung tanpa prepared statements. Jangan gunakan di lingkungan produksi!
            </div>
        </div>
    </div>
</body>
</html>