<?php
// post_vul.php (VULNERABLE - No Login Required)
// - Komentar DITAMPILKAN RAW (stored XSS enabled)
// - TIDAK perlu login untuk komentar
// - Jangan gunakan di server publik

require 'auth_simple.php';

$pdo = pdo_connect();
$post_id = (int)($_GET['id'] ?? 1);
$user = current_user(); // may be null

// ensure session CSRF token exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

$msg = '';
$err = '';

// Handle new comment (POST) - NO LOGIN REQUIRED
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'post_comment') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $err = 'Request tidak valid (CSRF).';
    } else {
        $username = trim($_POST['username'] ?? '');
        $comment = trim($_POST['comment'] ?? '');
        
        if ($username === '') {
            $err = 'Nama tidak boleh kosong.';
        } elseif (mb_strlen($username) > 50) {
            $err = 'Nama terlalu panjang (maks 50 karakter).';
        } elseif ($comment === '') {
            $err = 'Komentar tidak boleh kosong.';
        } elseif (mb_strlen($comment) > 2000) {
            $err = 'Komentar terlalu panjang (maks 2000 karakter).';
        } else {
            // Insert dengan user_id = NULL untuk guest comment
            $stmt = $pdo->prepare("INSERT INTO comments (user_id, post_id, comment, username_guest, created_at) VALUES (NULL, :pid, :c, :uname, NOW())");
            $stmt->execute([
                ':pid' => $post_id,
                ':c'   => $comment,
                ':uname' => $username
            ]);
            header("Location: post_vul.php?id=$post_id");
            exit;
        }
    }
}

// Handle delete comment (POST) - only owner allowed (if logged in)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_comment') {
    if (!$user) {
        $err = 'Anda harus login untuk menghapus komentar.';
    } else {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $err = 'Request tidak valid (CSRF).';
        } else {
            $del_id = (int)($_POST['delete_comment_id'] ?? 0);
            if ($del_id <= 0) {
                $err = 'ID komentar tidak valid.';
            } else {
                $stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = :cid");
                $stmt->execute([':cid' => $del_id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$row) {
                    $err = 'Komentar tidak ditemukan.';
                } elseif ((int)$row['user_id'] !== (int)$user['id']) {
                    $err = 'Anda tidak berhak menghapus komentar ini.';
                } else {
                    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = :cid");
                    $stmt->execute([':cid' => $del_id]);
                    header("Location: post_vul.php?id=$post_id");
                    exit;
                }
            }
        }
    }
}

// Fetch post
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id=:id LIMIT 1");
$stmt->execute([':id' => $post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch comments (prioritize guest username over user table)
$stmt = $pdo->prepare("
    SELECT c.*, 
           COALESCE(c.username_guest, u.username) as display_username,
           c.user_id
    FROM comments c 
    LEFT JOIN users u ON c.user_id = u.id 
    WHERE c.post_id = :pid 
    ORDER BY c.created_at DESC
");
$stmt->execute([':pid' => $post_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($post['title'] ?? 'Post'); ?> — LAB (VULNERABLE)</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
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
      max-width: 900px;
      margin: 36px auto;
    }

    /* Header Section */
    .header-top {
      background: white;
      border-radius: 20px;
      padding: 24px 32px;
      margin-bottom: 24px;
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
      width: 64px;
      height: 64px;
      background: linear-gradient(135deg, #6b46c1 0%, #805ad5 100%);
      border-radius: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 700;
      font-size: 18px;
      letter-spacing: 0.5px;
      box-shadow: 0 2px 8px rgba(107, 70, 193, 0.3);
    }

    .header-info h4 {
      color: #2d3748;
      font-size: 20px;
      font-weight: 600;
      margin-bottom: 4px;
    }

    .note {
      color: #718096;
      font-size: 13px;
      line-height: 1.5;
      font-weight: 400;
    }

    .header-right {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .vuln-badge {
      background: #fef3c7;
      color: #92400e;
      padding: 8px 18px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      display: inline-block;
    }

    /* Post Card */
    .post-card {
      background: white;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      margin-bottom: 24px;
    }

    .post-body {
      padding: 32px;
    }

    /* User Info Section */
    .user-info-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
      padding-bottom: 16px;
      border-bottom: 1px solid #e5e7eb;
      flex-wrap: wrap;
      gap: 12px;
    }

    .user-info-bar .text-muted {
      font-size: 14px;
      color: #718096;
    }

    .user-info-bar strong {
      color: #2d3748;
      font-weight: 600;
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
      font-family: 'Poppins', sans-serif;
    }

    .btn-sm {
      padding: 8px 16px;
      font-size: 13px;
    }

    .btn-primary {
      background: #6b46c1;
      color: white;
      border: none;
    }

    .btn-primary:hover {
      background: #553c9a;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(107, 70, 193, 0.3);
    }

    .btn-outline-secondary {
      background: white;
      color: #6b7280;
      border: 2px solid #e5e7eb;
    }

    .btn-outline-secondary:hover {
      background: #f9fafb;
      border-color: #d1d5db;
    }

    .btn-outline-warning {
      background: #6b46c1;
      color: white;
      border: none;
    }

    .btn-outline-warning:hover {
      background: #553c9a;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(107, 70, 193, 0.3);
    }

    .btn-danger {
      background: #ef4444;
      color: white;
      border: none;
    }

    .btn-danger:hover {
      background: #dc2626;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    /* Post Title & Meta */
    .post-title {
      color: #2d3748;
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 8px;
      line-height: 1.3;
    }

    .post-meta {
      color: #718096;
      font-size: 14px;
      margin-bottom: 24px;
    }

    .post-content {
      color: #4b5563;
      font-size: 15px;
      line-height: 1.8;
      margin-bottom: 32px;
    }

    /* Divider */
    hr {
      border: none;
      border-top: 1px solid #e5e7eb;
      margin: 32px 0;
    }

    /* Section Headings */
    h4 {
      color: #2d3748;
      font-size: 20px;
      font-weight: 600;
      margin-bottom: 16px;
    }

    /* Alerts */
    .alert {
      padding: 16px 20px;
      border-radius: 12px;
      margin-bottom: 16px;
      font-size: 14px;
    }

    .alert-danger {
      background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
      color: #991b1b;
      border: 1px solid #fca5a5;
    }

    .alert-success {
      background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
      color: #065f46;
      border: 1px solid #6ee7b7;
    }

    /* Form Controls */
    .form-control {
      width: 100%;
      padding: 14px 20px;
      border: 2px solid #e5e7eb;
      border-radius: 12px;
      font-size: 15px;
      transition: all 0.3s ease;
      background: #fafafa;
      font-family: 'Poppins', sans-serif;
      resize: vertical;
    }

    .form-control:focus {
      outline: none;
      border-color: #6b46c1;
      background: white;
      box-shadow: 0 0 0 3px rgba(107, 70, 193, 0.1);
    }

    .form-control::placeholder {
      color: #9ca3af;
    }

    textarea.form-control {
      min-height: 120px;
    }

    .form-group {
      margin-bottom: 16px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      color: #2d3748;
      font-weight: 500;
      font-size: 14px;
    }

    /* Form Actions */
    .form-actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 16px;
      gap: 16px;
      flex-wrap: wrap;
    }

    .form-actions .note {
      flex: 1;
      min-width: 200px;
    }

    /* Comments Section */
    .comments-section {
      margin-top: 32px;
    }

    .comment-card {
      background: #fafafa;
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 16px;
      transition: all 0.3s ease;
    }

    .comment-card:hover {
      border-color: #d6bcfa;
      box-shadow: 0 4px 12px rgba(107, 70, 193, 0.1);
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

    .comment-author strong {
      color: #2d3748;
      font-size: 15px;
      font-weight: 600;
    }

    .comment-date {
      color: #9ca3af;
      font-size: 13px;
      margin-top: 2px;
    }

    .comment-body {
      color: #4b5563;
      font-size: 15px;
      line-height: 1.7;
      margin-top: 12px;
      white-space: pre-wrap;
      word-wrap: break-word;
    }

    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 40px 20px;
      color: #9ca3af;
    }

    /* Card Footer */
    .card-footer {
      background: #fef3c7;
      padding: 20px 32px;
      border-top: 1px solid #fbbf24;
      color: #92400e;
      line-height: 1.6;
      font-size: 13px;
    }

    .card-footer strong {
      color: #78350f;
      font-weight: 600;
    }

    /* Responsive */
    @media (max-width: 768px) {
      body {
        padding: 12px;
      }

      .container-main {
        margin: 20px auto;
      }

      .header-top {
        padding: 20px;
      }

      .logo-circle {
        width: 56px;
        height: 56px;
        font-size: 16px;
      }

      .post-body {
        padding: 24px;
      }

      .post-title {
        font-size: 24px;
      }

      .form-actions {
        flex-direction: column;
        align-items: flex-start;
      }

      .user-info-bar {
        flex-direction: column;
        align-items: flex-start;
      }

      .card-footer {
        padding: 16px 24px;
      }
    }
  </style>
</head>
<body>
  <div class="container container-main">
    
    <!-- Header -->
    <div class="header-top">
      <div class="header-left">
        <div class="logo-circle">LAB</div>
        <div class="header-info">
          <h4>Contoh Artikel</h4>
          <div class="note">Halaman ini <strong>intentionally vulnerable</strong> untuk demo Stored XSS (komentar ditampilkan raw). <strong>Tidak perlu login!</strong></div>
        </div>
      </div>
      <div class="header-right">
        <span class="vuln-badge">VULNERABLE</span>
      </div>
    </div>

    <!-- Post Card -->
    <div class="post-card">
      <div class="post-body">
        
        <!-- User Info Bar -->
        <div class="user-info-bar">
          <div>
            <?php if ($user): ?>
              <span class="text-muted">Signed in as: <strong><?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></strong></span>
            <?php else: ?>
              <span class="text-muted">Komentar dapat dikirim tanpa login</span>
            <?php endif; ?>
          </div>
          <div>
            <?php if ($user): ?>
              <a class="btn btn-outline-secondary btn-sm" href="logout.php">Logout</a>
              <a class="btn btn-outline-warning btn-sm" href="dashboard.php">Kembali</a>
            <?php else: ?>
              <a class="btn btn-primary btn-sm" href="login.php">Login</a>
            <?php endif; ?>
          </div>
        </div>

        <!-- Post Content -->
        <h2 class="post-title"><?php echo htmlspecialchars($post['title'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></h2>
        
        <div class="post-meta">
          <?php echo htmlspecialchars($post['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?> 
          <?php if (!empty($post['author'])): ?> 
            &nbsp;oleh <?php echo htmlspecialchars($post['author'], ENT_QUOTES, 'UTF-8'); ?> 
          <?php endif; ?>
        </div>

        <div class="post-content">
          <?php echo nl2br(htmlspecialchars($post['body'] ?? '', ENT_QUOTES, 'UTF-8')); ?>
        </div>

        <hr>

        <!-- Comment Form Section -->
        <h4>Tulis Komentar</h4>

        <?php if ($err): ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if ($msg): ?>
          <div class="alert alert-success"><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form method="post" autocomplete="off">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="action" value="post_comment">
          
          <div class="form-group">
            <label for="username">Nama Anda:</label>
            <input 
              type="text" 
              id="username"
              name="username" 
              class="form-control" 
              placeholder="Masukkan nama Anda (maks 50 karakter)"
              value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') : ''; ?>"
              required
            >
          </div>
          
          <div class="form-group">
            <label for="comment">Komentar:</label>
            <textarea 
              id="comment"
              name="comment" 
              rows="5" 
              class="form-control" 
              placeholder="Tulis komentar Anda (maks 2000 karakter)"
              required
            ><?php echo isset($_POST['comment']) ? htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
          </div>
          
          <div class="form-actions">
            <div class="note">HTML dalam komentar akan dieksekusi — ini sengaja untuk demo XSS.</div>
            <button type="submit" class="btn btn-primary">Kirim Komentar</button>
          </div>
        </form>

        <hr>

        <!-- Comments Section -->
        <div class="comments-section">
          <h4>Komentar</h4>

          <?php if (empty($comments)): ?>
            <div class="empty-state">
              <p>Belum ada komentar.</p>
            </div>
          <?php else: ?>
            <div>
              <?php foreach ($comments as $c): ?>
                <div class="comment-card">
                  <div class="comment-header">
                    <div class="comment-author">
                      <strong><?php echo htmlspecialchars($c['display_username'] ?? 'Guest', ENT_QUOTES, 'UTF-8'); ?></strong>
                      <div class="comment-date"><?php echo htmlspecialchars($c['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                    </div>

                    <?php if ($user && $c['user_id'] && (int)$c['user_id'] === (int)$user['id']): ?>
                      <div>
                        <form method="post" style="display:inline-block;" onsubmit="return confirm('Hapus komentar ini?');">
                          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                          <input type="hidden" name="action" value="delete_comment">
                          <input type="hidden" name="delete_comment_id" value="<?php echo (int)$c['id']; ?>">
                          <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                      </div>
                    <?php endif; ?>
                  </div>

                  <div class="comment-body">
                    <!-- VULNERABLE: RAW output (stored XSS enabled) -->
                    <?php echo $c['comment']; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

      </div>

      <!-- Footer -->
      <div class="card-footer">
        <strong>PERINGATAN:</strong> Halaman ini intentionally vulnerable (Stored XSS). 
        Gunakan hanya untuk latihan di lingkungan terisolasi. <strong>Komentar dapat dikirim tanpa login!</strong>
      </div>
    </div>

  </div>
</body>
</html>