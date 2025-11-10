<?php
// search_safe.php (SAFE version)
require 'auth_simple.php';
$pdo = pdo_connect();

$q = trim((string)($_GET['q'] ?? ''));
$results = [];
$error = null;

if ($q !== '') {
    try {
        $sql = "SELECT c.id, u.username, c.comment, c.created_at
                FROM comments c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE LOWER(c.comment) LIKE :q OR LOWER(u.username) LIKE :q
                ORDER BY c.created_at DESC
                LIMIT 200";
        $stmt = $pdo->prepare($sql);
        $like = '%' . mb_strtolower($q, 'UTF-8') . '%';
        $stmt->execute([':q' => $like]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan saat mencari. Coba lagi.';
    }
}

function safe_highlight(string $text, string $query): string {
    $escaped = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    if ($query === '') return nl2br($escaped);
    
    $safe_q = htmlspecialchars($query, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $pattern = '/' . preg_quote($safe_q, '/') . '/iu';
    $highlighted = preg_replace($pattern, '<mark>$0</mark>', $escaped);
    return nl2br($highlighted);
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Search Comments — SAFE</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* Reset & Base */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: #f5f5f7;
      color: #1f2937;
      line-height: 1.6;
      min-height: 100vh;
      padding: 20px;
    }

    /* Container Main */
    .container-main {
      max-width: 1200px;
      margin: 0 auto;
    }

    /* Header Section */
    .header-section {
      background: white;
      border-radius: 20px;
      padding: 28px 36px;
      margin-bottom: 28px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 20px;
    }

    .header-left {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .logo-circle {
      width: 68px;
      height: 68px;
      background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
      border-radius: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
      font-size: 14px;
      letter-spacing: 0.5px;
      box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }

    .header-info h4 {
      color: #2d3748;
      font-size: 22px;
      font-weight: 600;
      margin-bottom: 6px;
    }

    .note {
      color: #718096;
      font-size: 14px;
      line-height: 1.5;
      font-weight: 400;
    }

    .header-right {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .safe-badge {
      background: #d1fae5;
      color: #065f46;
      padding: 8px 18px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border: none;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    /* Buttons */
    .btn {
      padding: 10px 20px;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
      border: none;
    }

    .btn-outline-warning {
      background: #6b46c1;
      color: white;
      border: none;
      font-weight: 600;
    }

    .btn-outline-warning:hover {
      background: #553c9a;
      color: white;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(107, 70, 193, 0.3);
    }

    .btn-success {
      background: #10b981;
      color: white;
      border: none;
      font-weight: 600;
    }

    .btn-success:hover {
      background: #059669;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .btn-outline-secondary {
      background: white;
      color: #6b7280;
      border: 2px solid #e5e7eb;
      padding: 6px 14px;
      font-size: 13px;
    }

    .btn-outline-secondary:hover {
      background: #f9fafb;
      border-color: #d1d5db;
    }

    .btn-sm {
      padding: 8px 16px;
      font-size: 13px;
    }

    /* Search Card */
    .search-card {
      background: white;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .card-body {
      padding: 32px;
    }

    /* Search Form */
    .search-form {
      display: flex;
      gap: 12px;
      margin-bottom: 0;
    }

    .form-control {
      flex: 1;
      padding: 14px 20px;
      border: 2px solid #e5e7eb;
      border-radius: 12px;
      font-size: 15px;
      transition: all 0.3s ease;
      background: #fafafa;
      font-family: 'Poppins', sans-serif;
    }

    .form-control:focus {
      outline: none;
      border-color: #10b981;
      background: white;
      box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .form-control::placeholder {
      color: #9ca3af;
    }

    hr {
      border: none;
      border-top: 1px solid #e5e7eb;
      margin: 24px 0;
    }

    /* Results Header */
    .results-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 24px;
      flex-wrap: wrap;
      gap: 16px;
    }

    .results-info h5 {
      color: #2d3748;
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 6px;
    }

    .text-muted {
      color: #718096;
      font-weight: 400;
    }

    .count-badge {
      background: #d1fae5;
      color: #065f46;
      padding: 6px 16px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      display: inline-block;
    }

    /* Alert */
    .alert {
      padding: 16px 20px;
      border-radius: 12px;
      margin-top: 16px;
      font-size: 14px;
    }

    .alert-info {
      background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
      color: #1e40af;
      border: 1px solid #93c5fd;
    }

    .alert-danger {
      background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
      color: #991b1b;
      border: 1px solid #fca5a5;
    }

    /* Comments */
    .comment {
      background: #fafafa;
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 16px;
      transition: all 0.3s ease;
    }

    .comment:hover {
      border-color: #a7f3d0;
      box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
      transform: translateY(-2px);
    }

    .comment-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 12px;
      flex-wrap: wrap;
      gap: 12px;
    }

    .comment-header strong {
      color: #2d3748;
      font-size: 15px;
      font-weight: 600;
    }

    .comment-header .text-muted {
      font-size: 13px;
      color: #9ca3af;
    }

    .comment-text {
      color: #4b5563;
      font-size: 15px;
      line-height: 1.7;
      padding: 12px 0;
    }

    .comment-text mark {
      background: #fef3c7;
      color: #92400e;
      padding: 2px 4px;
      border-radius: 4px;
      font-weight: 500;
    }

    /* Card Footer */
    .card-footer {
      background: #f0fdf4;
      padding: 20px 32px;
      border-top: 1px solid #bbf7d0;
      color: #166534;
      line-height: 1.6;
    }

    .card-footer strong {
      color: #065f46;
      font-weight: 600;
    }

    .card-footer code {
      background: #dcfce7;
      color: #166534;
      padding: 2px 8px;
      border-radius: 4px;
      font-family: 'Courier New', monospace;
      font-size: 13px;
    }

    .small {
      font-size: 14px;
    }

    /* Responsive */
    @media (max-width: 768px) {
      body {
        padding: 12px;
      }

      .header-section {
        padding: 20px;
      }

      .header-left {
        width: 100%;
      }

      .header-right {
        width: 100%;
        justify-content: flex-start;
      }

      .logo-circle {
        width: 56px;
        height: 56px;
        font-size: 12px;
      }

      .header-info h4 {
        font-size: 20px;
      }

      .card-body {
        padding: 24px;
      }

      .search-form {
        flex-direction: column;
      }

      .results-header {
        flex-direction: column;
      }

      .card-footer {
        padding: 16px 24px;
      }
    }
  </style>
</head>
<body>
  <div class="container-main">
    
    <!-- Header -->
    <div class="header-section">
      <div class="header-left">
        <div class="logo-circle">SAFE</div>
        <div class="header-info">
          <h4>Search Komentar (SAFE)</h4>
          <div class="note">
            Versi aman: prepared statements + escaping. Cocok untuk perbandingan dengan versi vulnerable.
          </div>
        </div>
      </div>
      <div class="header-right">
        <span class="safe-badge">✓ SAFE VERSION</span>
        <a class="btn btn-outline-warning btn-sm" href="dashboard.php">Kembali</a>
      </div>
    </div>

    <!-- Search Card -->
    <div class="search-card">
      <div class="card-body">
        
        <!-- Search Form -->
        <form class="search-form" method="get">
          <input 
            name="q" 
            class="form-control" 
            placeholder="Cari komentar atau username..." 
            value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>" 
            autofocus
          >
          <button class="btn btn-success" type="submit">Search</button>
        </form>

        <?php if ($q !== ''): ?>
          <hr>

          <!-- Results Header -->
          <div class="results-header">
            <div class="results-info">
              <h5>Hasil untuk: <span class="text-muted"><?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?></span></h5>
              <div class="note">
                Menampilkan komentar yang mengandung kata pencarian atau username (case-insensitive).
              </div>
            </div>
            <div>
              <span class="count-badge"><?php echo count($results); ?> hasil</span>
            </div>
          </div>

          <!-- Error -->
          <?php if ($error): ?>
            <div class="alert alert-danger">
              <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
          <?php endif; ?>

          <!-- No Results -->
          <?php if (empty($results)): ?>
            <div class="alert alert-info">
              Tidak ada hasil untuk pencarian ini.
            </div>
          <?php else: ?>
            
            <!-- Results -->
            <div>
              <?php foreach ($results as $r): ?>
                <div class="comment">
                  <div class="comment-header">
                    <div>
                      <strong><?php echo htmlspecialchars($r['username'] ?? 'Guest', ENT_QUOTES, 'UTF-8'); ?></strong>
                      <div class="text-muted"><?php echo htmlspecialchars($r['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                    </div>
                    <div>
                      <a href="#" class="btn btn-outline-secondary btn-sm">View</a>
                    </div>
                  </div>
                  <div class="comment-text">
                    <?php echo safe_highlight((string)$r['comment'], $q); ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

          <?php endif; ?>
        <?php endif; ?>

      </div>

      <!-- Footer -->
      <div class="card-footer small">
        <strong>Catatan:</strong> File ini <strong>aman</strong> — menggunakan prepared statements dan escaping output. 
        Untuk demonstrasi perbandingan, bandingkan dengan <code>search_vul.php</code> (intentionally vulnerable).
      </div>
    </div>

  </div>
</body>
</html>