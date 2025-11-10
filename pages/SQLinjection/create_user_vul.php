<?php
// create_user_vul.php
// DEMO ONLY: VULNERABLE user creation form — gunakan hanya di lab lokal/VM

$dsn = 'mysql:host=127.0.0.1;dbname=praktek_sqli;charset=utf8mb4';
$dbUser = 'root';
$dbPass = '';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $fullname = $_POST['full_name'] ?? '';

    try {
        $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        // VULNERABLE: menyimpan password plaintext and concatenation query
        $sql = "INSERT INTO users_vul (username, password, full_name) VALUES ('" 
                . $username . "', '" . $password . "', '" . $fullname . "')";
        $pdo->exec($sql);

        $message = "User berhasil dibuat: " . htmlspecialchars($username);
        $messageType = 'success';

    } catch (PDOException $e) {
        $message = "Terjadi kesalahan: " . $e->getMessage();
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User — VULNERABLE (Demo)</title>
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
            max-width: 520px;
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

        .form-box {
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
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 0.95rem;
        }

        .alert.success {
            background: rgba(230, 247, 237, 0.8);
            border: 1px solid #c3e6cd;
            color: #218838;
        }

        .alert.error {
            background: rgba(255, 228, 232, 0.5);
            border: 1px solid #ffccd3;
            color: #c82333;
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

        .warning-box {
            margin-top: 24px;
            padding: 18px;
            background: #fff9e6;
            border: 1px solid #ffe5a3;
            border-radius: 12px;
        }

        .warning-box h3 {
            font-size: 1rem;
            color: #4a2d5f;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .vulnerability-list {
            list-style: none;
            padding: 0;
        }

        .vulnerability-list li {
            padding: 6px 0;
            color: #7c5699;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .vulnerability-list li::before {
            content: '✗';
            color: #dc3545;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .tip-box {
            margin-top: 16px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 8px;
            font-size: 0.85rem;
            color: #6b5580;
        }

        @media (max-width: 480px) {
            .form-box {
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

        <div class="form-box">
            <div class="header">
                <div class="icon-box">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <line x1="20" y1="8" x2="20" y2="14"/>
                        <line x1="23" y1="11" x2="17" y2="11"/>
                    </svg>
                </div>
                <h2>Create User</h2>
                <span class="badge">VULNERABLE - XSS & SQL Injection</span>
            </div>

            <?php if ($message): ?>
                <div class="alert <?= $messageType ?>"><?= $message ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required placeholder="Masukkan username">
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="text" name="password" required placeholder="Masukkan password">
                </div>

                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" placeholder="Nama lengkap (opsional)">
                </div>

                <button type="submit">Buat User (Vulnerable)</button>
            </form>

            <div class="warning-box">
                <h3>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    Kerentanan yang Ada
                </h3>
                <ul class="vulnerability-list">
                    <li>String concatenation SQL (SQL Injection)</li>
                    <li>Password tersimpan plaintext</li>
                    <li>Tidak ada input validation</li>
                    <li>Tidak ada CSRF protection</li>
                </ul>
                <div class="tip-box">
                    <strong>💡 Coba injeksi:</strong> Username: <code>admin' OR '1'='1</code> untuk melihat SQL Injection dalam aksi
                </div>
            </div>
        </div>
    </div>
</body>
</html>