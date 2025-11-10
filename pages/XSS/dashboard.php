<?php
// dashboard.php
// Public dashboard page. No login required.
// Shows links to vulnerable/safe demo pages.

require 'auth_simple.php';
$pdo = pdo_connect();

// Check if user is logged in (optional, not required)
$user = current_user();

// fetch some simple stats (best-effort; tidak fatal jika query gagal)
$stats = [
    'posts' => null,
    'comments' => null,
    'users' => null,
];
try {
    $stmt = $pdo->query("SELECT COUNT(*) AS cnt FROM posts");
    $stats['posts'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
} catch (Exception $e) { /* ignore */ }

try {
    $stmt = $pdo->query("SELECT COUNT(*) AS cnt FROM comments");
    $stats['comments'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
} catch (Exception $e) { /* ignore */ }

try {
    $stmt = $pdo->query("SELECT COUNT(*) AS cnt FROM users");
    $stats['users'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
} catch (Exception $e) { /* ignore */ }

function esc($s) {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard — Lab Demo Keamanan Web</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../css/dashboard.css">
</head>
<body>
  <div class="wrap">
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
          <div class="brand-subtitle">Demonstrasi XSS </div>
        </div>
      </div>

      <div class="d-flex gap-2 align-items-center">
        <div class="stats-box">
          <div class="stat-item">
            <span class="stat-label">Posts:</span>
            <span class="stat-value"><?php echo is_null($stats['posts']) ? '—' : esc((string)$stats['posts']); ?></span>
          </div>
          <div class="stat-item">
            <span class="stat-label">Comments:</span>
            <span class="stat-value"><?php echo is_null($stats['comments']) ? '—' : esc((string)$stats['comments']); ?></span>
          </div>
        </div>
        <?php if ($user): ?>
          <a href="logout.php" class="btn btn-outline">Logout</a>
        <?php endif; ?>
        <a href="../../pages/Beranda/beranda.html" class="btn btn-primary">Kembali ke Beranda</a>
      </div>
    </div>

    <div class="welcome-card">
      <h2>Selamat Datang di Lab Keamanan Web</h2>
      <p>Halaman ini menyediakan demo interaktif untuk memahami kerentanan keamanan web seperti <strong>Cross-Site Scripting (XSS)</strong></p>
      <div class="warning-box">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
          <path d="M12 9V13M12 17H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <span><strong>Peringatan:</strong> Halaman vulnerable hanya untuk pembelajaran. Jangan gunakan di production!</span>
      </div>
    </div>

    <div class="section-title">Halaman Vulnerable (Untuk Demonstrasi)</div>
    <div class="card-grid">
      <!-- Vulnerable: Post -->
      <div class="dash-card vulnerable">
        <div class="card-icon vulnerable-icon">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
            <path d="M12 9V13M12 17H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="card-content">
          <div class="card-title">post_vul.php</div>
          <div class="card-badge vulnerable-badge">VULNERABLE - XSS</div>
          <div class="card-desc">Halaman posting yang menampilkan komentar tanpa sanitasi. Rentan terhadap <strong>Stored XSS</strong> attack.</div>
          <div class="card-features">
            <div class="feature-item">❌ Tidak ada HTML escaping</div>
            <div class="feature-item">❌ Raw output komentar</div>
            <div class="feature-item">❌ No input validation</div>
          </div>
        </div>
        <div class="card-actions">
          <a href="post_vul.php" class="btn btn-danger">Buka Demo (VULNERABLE)</a>
        </div>
      </div>

      <!-- Vulnerable: Search -->
      <div class="dash-card vulnerable">
        <div class="card-icon vulnerable-icon">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
            <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="M11 8V14M8 11H14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="card-content">
          <div class="card-title">search_vul.php</div>
          <div class="card-badge vulnerable-badge">VULNERABLE - SQL Injection</div>
          <div class="card-desc">Pencarian komentar dengan query SQL yang di-concatenate langsung. Rentan terhadap <strong>SQL Injection</strong>.</div>
          <div class="card-features">
            <div class="feature-item">❌ String concatenation SQL</div>
            <div class="feature-item">❌ No prepared statements</div>
            <div class="feature-item">❌ Direct user input ke query</div>
          </div>
        </div>
        <div class="card-actions">
          <a href="search_vul.php" class="btn btn-danger">Buka Demo (VULNERABLE)</a>
        </div>
      </div>
    </div>

    <div class="section-title" style="margin-top: 40px;">Halaman Safe (Best Practice)</div>
    <div class="card-grid">
      <!-- Safe: Post -->
      <div class="dash-card safe">
        <div class="card-icon safe-icon">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
            <path d="M12 2L4 6V12C4 16.5 7 20.5 12 22C17 20.5 20 16.5 20 12V6L12 2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="card-content">
          <div class="card-title">post_safe.php</div>
          <div class="card-badge safe-badge">SAFE - Protected</div>
          <div class="card-desc">Versi aman dengan HTML escaping dan CSRF protection. Komentar di-sanitasi untuk mencegah XSS.</div>
          <div class="card-features">
            <div class="feature-item">✓ HTML escaping (htmlspecialchars)</div>
            <div class="feature-item">✓ CSRF token protection</div>
            <div class="feature-item">✓ Input validation</div>
          </div>
        </div>
        <div class="card-actions">
          <a href="post_safe.php" class="btn btn-success">Buka Demo (SAFE)</a>
        </div>
      </div>

      <!-- Safe: Search -->
      <div class="dash-card safe">
        <div class="card-icon safe-icon">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
            <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="card-content">
          <div class="card-title">search_safe.php</div>
          <div class="card-badge safe-badge">SAFE - Protected</div>
          <div class="card-desc">Pencarian aman menggunakan prepared statements PDO. Hasil pencarian di-escape untuk keamanan tampilan.</div>
          <div class="card-features">
            <div class="feature-item">✓ PDO prepared statements</div>
            <div class="feature-item">✓ Parameterized queries</div>
            <div class="feature-item">✓ Output escaping</div>
          </div>
        </div>
        <div class="card-actions">
          <a href="search_safe.php" class="btn btn-success">Buka Demo (SAFE)</a>
        </div>
      </div>
    </div>

    <div class="info-card">
      <div class="info-icon">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
          <path d="M12 16V12M12 8H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
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

    <footer>
      <div class="footer-content">
        <div>📚 Tugas Mata Kuliah Keamanan Data</div>
        <div>⚠️ Hanya untuk keperluan edukasi dan pembelajaran</div>
      </div>
    </footer>
  </div>
</body>
</html>