<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Lab Demo Keamanan Web</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* dashboard.css - Styling untuk dashboard keamanan web */

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
            max-width: 1200px;
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

        .gap-2 {
            gap: 12px;
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
            background: linear-gradient(135deg, #5e3a7f, #7c5699);
            box-shadow: 0 6px 20px rgba(94, 58, 127, 0.25);
        }

        .brand-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #4a2d5f;
        }

        .brand-subtitle {
            font-size: 0.9rem;
            color: #7c5699;
            margin-top: 2px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .stats-box {
            display: flex;
            gap: 20px;
            padding: 12px 20px;
            background: #f9f7fb;
            border-radius: 12px;
            border: 1px solid #e0d5eb;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #7c5699;
        }

        .stat-value {
            font-size: 1rem;
            font-weight: 700;
            color: #4a2d5f;
        }

        /* Buttons */
        .btn {
            padding: 10px 24px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            display: inline-block;
            font-family: 'Poppins', sans-serif;
        }

        .btn-primary {
            background: linear-gradient(135deg, #5e3a7f, #7c5699);
            color: white;
            box-shadow: 0 4px 15px rgba(94, 58, 127, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(94, 58, 127, 0.35);
        }

        .btn-outline {
            background: transparent;
            color: #5e3a7f;
            border: 2px solid #9c7ab8;
        }

        .btn-outline:hover {
            background: #f5f1f9;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.25);
            width: 100%;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.35);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745, #218838);
            color: white;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.25);
            width: 100%;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.35);
        }

        /* Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, #5e3a7f, #7c5699);
            color: white;
            padding: 32px;
            border-radius: 20px;
            margin-bottom: 32px;
            box-shadow: 0 10px 30px rgba(94, 58, 127, 0.2);
        }

        .welcome-card h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .welcome-card p {
            font-size: 1.05rem;
            line-height: 1.7;
            opacity: 0.95;
        }

        .warning-box {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 255, 255, 0.15);
            padding: 16px 20px;
            border-radius: 12px;
            margin-top: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .warning-box svg {
            flex-shrink: 0;
            stroke: white;
        }

        /* Section Title */
        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #4a2d5f;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #5e3a7f, #7c5699);
            border-radius: 2px;
        }

        /* Card Grid */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .dash-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 6px 20px rgba(94, 58, 127, 0.08);
            border: 2px solid #f0ebf5;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .dash-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(94, 58, 127, 0.15);
        }

        .dash-card.vulnerable {
            border-color: #ffccd3;
        }

        .dash-card.vulnerable:hover {
            border-color: #ff99a5;
        }

        .dash-card.safe {
            border-color: #c3e6cd;
        }

        .dash-card.safe:hover {
            border-color: #9dd9a7;
        }

        .card-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
        }

        .vulnerable-icon {
            background: linear-gradient(135deg, #ffe5e8, #ffd1d7);
            color: #dc3545;
        }

        .safe-icon {
            background: linear-gradient(135deg, #e6f7ed, #d4f1dd);
            color: #28a745;
        }

        .card-content {
            flex: 1;
        }

        .card-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #4a2d5f;
            margin-bottom: 8px;
        }

        .card-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .vulnerable-badge {
            background: #ffe5e8;
            color: #c82333;
        }

        .safe-badge {
            background: #e6f7ed;
            color: #218838;
        }

        .card-desc {
            color: #6b5580;
            line-height: 1.7;
            font-size: 0.95rem;
            margin-bottom: 16px;
        }

        .card-features {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .feature-item {
            font-size: 0.9rem;
            color: #7c5699;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-actions {
            margin-top: auto;
        }

        /* Info Card */
        .info-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 6px 20px rgba(94, 58, 127, 0.08);
            border: 2px solid #e6f0ff;
            margin-bottom: 32px;
            display: flex;
            gap: 20px;
        }

        .info-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            background: linear-gradient(135deg, #e6f0ff, #d9e8ff);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .info-icon svg {
            stroke: #5e3a7f;
        }

        .info-card h3 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #4a2d5f;
            margin-bottom: 12px;
        }

        .info-card ul {
            list-style: none;
            padding: 0;
        }

        .info-card li {
            color: #6b5580;
            line-height: 1.8;
            font-size: 0.95rem;
            padding-left: 20px;
            position: relative;
            margin-bottom: 8px;
        }

        .info-card li::before {
            content: '•';
            position: absolute;
            left: 0;
            color: #7c5699;
            font-weight: 700;
        }

        .info-card code {
            background: #f5f1f9;
            padding: 2px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #5e3a7f;
            font-size: 0.9rem;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 24px;
            margin-top: 40px;
        }

        .footer-content {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            color: #7c5699;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .topbar-right {
                width: 100%;
                flex-direction: column;
                gap: 12px;
            }

            .stats-box {
                width: 100%;
                justify-content: space-around;
            }

            .btn-primary {
                width: 100%;
                text-align: center;
            }

            .card-grid {
                grid-template-columns: 1fr;
            }

            .info-card {
                flex-direction: column;
            }

            .footer-content {
                flex-direction: column;
                gap: 12px;
            }
        }

        @media (max-width: 480px) {
            .brand-title {
                font-size: 1.1rem;
            }

            .welcome-card h2 {
                font-size: 1.5rem;
            }

            .section-title {
                font-size: 1.3rem;
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
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
                        <path d="M12 2L4 6V12C4 16.5 7 20.5 12 22C17 20.5 20 16.5 20 12V6L12 2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" fill="white" opacity="0.3"/>
                        <path d="M9 12L11 14L15 10" stroke="white" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div>
                    <div class="brand-title">Dashboard Demo Keamanan Web</div>
                    <div class="brand-subtitle">Demonstrasi XSS</div>
                </div>
            </div>
            
            <div class="topbar-right">
                <div class="stats-box">
                    <div class="stat-item">
                        <span class="stat-label">Posts:</span>
                        <span class="stat-value">2</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Comments:</span>
                        <span class="stat-value">3</span>
                    </div>
                </div>
                <a href="../../pages/Beranda/beranda.html" class="btn btn-primary">Kembali ke Beranda</a>
            </div>
        </div>

        <!-- Welcome Card -->
        <div class="welcome-card">
            <h2>Selamat Datang di Lab Keamanan Web</h2>
            <p>Halaman ini menyediakan demo interaktif untuk memahami kerentanan keamanan web seperti SQL Injection.</p>
            <div class="warning-box">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                <span><strong>Peringatan:</strong> Halaman vulnerable hanya untuk pembelajaran. Jangan gunakan di produksi!</span>
            </div>
        </div>

        <!-- Vulnerable Section -->
        <h2 class="section-title">Halaman Vulnerable (Untuk Demonstrasi)</h2>
        <div class="card-grid">
            <!-- Create User Vulnerable -->
            <div class="dash-card vulnerable">
                <div class="card-icon vulnerable-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </div>
                <div class="card-content">
                    <h3 class="card-title">create_user_vul.php</h3>
                    <span class="card-badge vulnerable-badge">VULNERABLE - XSS</span>
                    <p class="card-desc">Halaman posting yang memampukan komentar tanpa sanitasi. Rentan terhadap Stored XSS attack.</p>
                    <div class="card-features">
                        <div class="feature-item">✗ Tidak ada HTML escaping</div>
                        <div class="feature-item">✗ Raw output komentar</div>
                        <div class="feature-item">✗ No input validation</div>
                    </div>
                </div>
                <div class="card-actions">
                    <button class="btn btn-danger" onclick="navigateTo('create_user_vul.php')">Buka Demo (VULNERABLE)</button>
                </div>
            </div>

            <!-- Login Vulnerable -->
            <div class="dash-card vulnerable">
                <div class="card-icon vulnerable-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
                <div class="card-content">
                    <h3 class="card-title">login_vul.php</h3>
                    <span class="card-badge vulnerable-badge">VULNERABLE - SQL Injection</span>
                    <p class="card-desc">Pencarian komentar dengan query SQL yang di-concatenate langsung. Rentan terhadap SQL Injection.</p>
                    <div class="card-features">
                        <div class="feature-item">✗ String concatenation SQL</div>
                        <div class="feature-item">✗ No prepared statements</div>
                        <div class="feature-item">✗ Direct input to query</div>
                    </div>
                </div>
                <div class="card-actions">
                    <button class="btn btn-danger" onclick="navigateTo('login_vul.php')">Buka Demo (VULNERABLE)</button>
                </div>
            </div>
        </div>

        <!-- Safe Section -->
        <h2 class="section-title">Halaman Safe (Best Practice)</h2>
        <div class="card-grid">
            <!-- Create User Safe -->
            <div class="dash-card safe">
                <div class="card-icon safe-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <div class="card-content">
                    <h3 class="card-title">create_user_safe.php</h3>
                    <span class="card-badge safe-badge">SAFE - Protected</span>
                    <p class="card-desc">Versi aman dengan HTML escaping dan CSRF protection. Komentar disanitasi untuk mencegah XSS.</p>
                    <div class="card-features">
                        <div class="feature-item">✓ HTML escaping (htmlspecialchars)</div>
                        <div class="feature-item">✓ CSRF token protection</div>
                        <div class="feature-item">✓ Input validation</div>
                    </div>
                </div>
                <div class="card-actions">
                    <button class="btn btn-success" onclick="navigateTo('create_user_safe.php')">Buka Demo (SAFE)</button>
                </div>
            </div>

            <!-- Login Safe -->
            <div class="dash-card safe">
                <div class="card-icon safe-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <div class="card-content">
                    <h3 class="card-title">login_safe.php</h3>
                    <span class="card-badge safe-badge">SAFE - Protected</span>
                    <p class="card-desc">Pencarian aman menggunakan prepared statements PDO. Hasil pencarian di-escape untuk keamanan tampilan.</p>
                    <div class="card-features">
                        <div class="feature-item">✓ PDO prepared statements</div>
                        <div class="feature-item">✓ Parameterized queries</div>
                        <div class="feature-item">✓ Output escaping</div>
                    </div>
                </div>
                <div class="card-actions">
                    <button class="btn btn-success" onclick="navigateTo('login_safe.php')">Buka Demo (SAFE)</button>
                </div>
            </div>
        </div>

        <!-- Tips Section -->
        <div class="info-card">
            <div class="info-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <div>
                <h3>Tips Pembelajaran</h3>
                <ul>
                    <li>Coba buka halaman <strong>vulnerable</strong> terlebih dahulu untuk melihat celah keamanan</li>
                    <li>Bandingkan dengan halaman <strong>safe</strong> untuk memahami implementasi yang benar</li>
                    <li>Untuk demo XSS, coba input: <code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code></li>
                    <li>Untuk demo SQL Injection, coba input: <code>' OR '1'='1</code></li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        function navigateTo(page) {
            // File PHP harus diakses lewat localhost (Laragon/XAMPP)
            // JANGAN pakai Go Live dari VS Code!
            window.location.href = page;
        }
    </script>
</body>
</html>