<?php
// post_safe.php (SAFE - styled to match beranda theme)
// - Comments are escaped (no stored XSS).
// - Uses prepared statements for DB actions.
// - CSRF token + owner-only delete.

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

// Handle new comment (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'post_comment') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $err = 'Request tidak valid (CSRF).';
    } else {
        $name = trim((string)($_POST['name'] ?? ''));
        $comment = trim((string)($_POST['comment'] ?? ''));
        
        if ($name === '') {
            $err = 'Nama tidak boleh kosong.';
        } elseif ($comment === '') {
            $err = 'Komentar tidak boleh kosong.';
        } elseif (mb_strlen($comment, 'UTF-8') > 2000) {
            $err = 'Komentar terlalu panjang (maks 2000 karakter).';
        } else {
            // Insert comment - username will be stored in 'name' column for anonymous users
            $stmt = $pdo->prepare("INSERT INTO comments (user_id, post_id, comment, name, created_at) VALUES (NULL, :pid, :c, :name, NOW())");
            $stmt->execute([
                ':pid' => $post_id,
                ':c'   => $comment,
                ':name' => $name
            ]);
            header("Location: post_safe.php?id=$post_id");
            exit;
        }
    }
}

// Handle delete comment (POST) - anyone can delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_comment') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $err = 'Request tidak valid (CSRF).';
    } else {
        $del_id = (int)($_POST['delete_comment_id'] ?? 0);
        if ($del_id <= 0) {
            $err = 'ID komentar tidak valid.';
        } else {
            $stmt = $pdo->prepare("DELETE FROM comments WHERE id = :cid");
            $stmt->execute([':cid' => $del_id]);
            header("Location: post_safe.php?id=$post_id");
            exit;
        }
    }
}

// Fetch post
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id=:id LIMIT 1");
$stmt->execute([':id' => $post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch comments
$stmt = $pdo->prepare("
    SELECT c.*, u.username, 
           COALESCE(c.name, u.username, 'Guest') as display_name
    FROM comments c 
    LEFT JOIN users u ON c.user_id = u.id 
    WHERE c.post_id = :pid 
    ORDER BY c.created_at DESC
");
$stmt->execute([':pid' => $post_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// helper to safely escape and preserve newlines
function esc_nl(string $s): string {
    return nl2br(htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8'));
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($post['title'] ?? 'Post', ENT_QUOTES, 'UTF-8'); ?> — SAFE</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../css/post_safe.css">
</head>
<body>
  <!-- Header -->
  <header>
    <div class="navbar-container">
      <a href="dashboard.php" class="nav-logo">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
          <path d="M12 2L4 6V12C4 16.5 7 20.5 12 22C17 20.5 20 16.5 20 12V6L12 2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" fill="#5e3a7f" opacity="0.2"/>
          <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <span>Keamanan Data</span>
      </a>
      <div class="nav-right">
        <span class="safe-badge">✓ SAFE VERSION</span>
        <?php if ($user): ?>
          <a class="btn btn-outline" href="logout.php">Logout</a>
        <?php endif; ?>
        <a class="btn btn-back" href="dashboard.php">Kembali</a>
      </div>
    </div>
  </header>

  <div class="container-main">
    <!-- Page Header -->
    <div class="page-header">
      <h1>Halaman Aman (SAFE)</h1>
      <p class="note">
        Halaman ini menggunakan <strong>prepared statements</strong> untuk query database dan 
        <strong>HTML escaping</strong> untuk menampilkan komentar, sehingga aman dari serangan 
        SQL Injection dan XSS (Cross-Site Scripting).
      </p>
    </div>

    <!-- Post Card -->
    <div class="post-card">
      <div class="post-body">
        <div class="post-header">
          <div>
            <h2 class="post-title"><?php echo htmlspecialchars($post['title'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></h2>
            <div class="post-meta">
              <?php echo htmlspecialchars($post['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?> 
              <?php if (!empty($post['author'])): ?> 
                • oleh <?php echo htmlspecialchars($post['author'], ENT_QUOTES, 'UTF-8'); ?> 
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="post-content">
          <?php echo nl2br(htmlspecialchars($post['body'] ?? '', ENT_QUOTES, 'UTF-8')); ?>
        </div>

        <hr>

        <!-- Comment Form -->
        <h3 class="section-title">Tulis Komentar</h3>

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
            <input type="text" name="name" class="form-control" placeholder="Nama Anda" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>
          </div>
          <div class="form-group">
            <textarea name="comment" rows="5" class="form-control" placeholder="Tulis komentar Anda (maks 2000 karakter)" required><?php echo isset($_POST['comment']) ? htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
          </div>
          <div class="form-footer">
            <div class="form-note">
              ✓ Komentar akan di-escape untuk keamanan
            </div>
            <button type="submit" class="btn btn-primary">Kirim Komentar</button>
          </div>
        </form>

        <hr>

        <!-- Comments List -->
        <h3 class="section-title">Komentar</h3>

        <?php if (empty($comments)): ?>
          <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <p>Belum ada komentar. Jadilah yang pertama berkomentar!</p>
          </div>
        <?php else: ?>
          <div>
            <?php foreach ($comments as $c): ?>
              <div class="comment-card">
                <div class="comment-header">
                  <div>
                    <div class="comment-author">
                      <?php echo htmlspecialchars($c['display_name'] ?? 'Guest', ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <div class="comment-date">
                      <?php echo htmlspecialchars($c['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                  </div>

                  <div>
                    <form method="post" style="display:inline-block;" onsubmit="return confirm('Hapus komentar ini?');">
                      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                      <input type="hidden" name="action" value="delete_comment">
                      <input type="hidden" name="delete_comment_id" value="<?php echo (int)$c['id']; ?>">
                      <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                  </div>
                </div>

                <div class="comment-content">
                  <?php echo esc_nl((string)$c['comment']); ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

      </div>

      <div class="card-footer">
        ✓ Halaman ini aman: komentar di-escape untuk mencegah XSS dan menggunakan prepared statements untuk mencegah SQL Injection.
      </div>
    </div>
  </div>
</body>
</html>