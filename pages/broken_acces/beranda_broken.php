<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Dashboard Demo Keamanan Web</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#f6f4fb;
      --card:#ffffff;
      --purple:#7b4bd6;
      --muted:#6b587a;
      --danger:#d9534f;
      --success:#2d8559;
      --soft-purple: #471a96ff;
      --soft-red: #fff0f0;
      --soft-green: #e8f7ef;
    }
    *{box-sizing:border-box}
    body{
      margin:0;
      font-family:'Poppins',system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial;
      background:var(--bg);
      color:#2c2340;
      -webkit-font-smoothing:antialiased;
    }

    .wrap{
      max-width:1140px;
      margin:36px auto;
      padding:20px;
    }

    .topbar{
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:12px;
      margin-bottom:22px;
    }
    .brand{
      display:flex;
      gap:14px;
      align-items:center;
    }
    .logo{
      width:56px;height:56px;border-radius:12px;
      background:var(--purple);
      display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;
      box-shadow:0 4px 12px rgba(123,75,214,0.2);
      font-size:20px;
    }
    .brand h1{margin:0;font-size:18px;color:#2c2340;font-weight:600;}
    .brand p{margin:0;font-size:13px;color:var(--muted)}

    .cta{
      display:flex;gap:12px;align-items:center;
    }
    .badge{
      background:#fff;padding:8px 12px;border-radius:999px;border:1px solid #e5e5e5;color:var(--muted);
      font-weight:600;font-size:13px;
    }
    .btn-primary{
      background:var(--purple); color:#fff; padding:10px 16px;border-radius:8px;border:none;
      font-weight:600; cursor:pointer;text-decoration:none;display:inline-block;
    }
    .btn-primary:hover{
      background:#6a3fc4;
    }

    .hero{
      background:var(--purple);
      color:#fff;padding:28px;border-radius:12px;box-shadow:0 4px 12px rgba(123,75,214,0.15);
      margin-bottom:26px;
    }
    .hero h2{margin:0 0 8px 0;font-size:22px;font-weight:600;}
    .hero p{margin:0;color:rgba(255,255,255,0.95);font-size:14px;line-height:1.5;}
    .hero .warn{
      margin-top:14px;background:rgba(255,255,255,0.1);padding:10px 12px;border-radius:8px;
      font-size:13px;color:#fff;
    }

    .section-title{
      margin:24px 0 14px 0;
    }
    .section-title h3{margin:0;color:#2c2340;font-size:18px;font-weight:600;}

    .grid{
      display:grid;
      grid-template-columns:repeat(2,1fr);
      gap:18px;
    }

    .card{
      background:var(--card);
      border-radius:12px;
      padding:20px;
      box-shadow:0 2px 8px rgba(40,30,60,0.08);
      border:1px solid #f0f0f0;
      display:flex;
      flex-direction:column;
      gap:14px;
    }

    .card .head{
      display:flex;align-items:center;gap:12px;
    }
    .icon{
      width:48px;height:48px;border-radius:10px;
      display:flex;align-items:center;justify-content:center;font-size:20px;
    }
    .icon.vuln{background:var(--soft-red);color:var(--danger);}
    .icon.safe{background:var(--soft-green);color:var(--success);}
    
    .card h4{margin:0;font-size:15px;color:#2c2340;font-weight:600;}
    .badge-small{
      display:inline-block;padding:4px 10px;border-radius:6px;font-weight:600;font-size:11px;
      margin-top:6px;
    }
    .vuln-badge{background:var(--soft-red);color:var(--danger);}
    .safe-badge{background:var(--soft-green);color:var(--success);}

    .card .footer{
      display:flex;justify-content:flex-end;gap:10px;margin-top:auto;padding-top:8px;
    }
    .btn-danger{
      background:var(--danger);color:#fff;padding:9px 16px;border-radius:8px;border:none;font-weight:600;cursor:pointer;
      text-decoration:none;display:inline-block;font-size:13px;
    }
    .btn-danger:hover{
      background:#c9302c;
    }
    .btn-success{
      background:var(--success);color:#fff;padding:9px 16px;border-radius:8px;border:none;font-weight:600;cursor:pointer;
      text-decoration:none;display:inline-block;font-size:13px;
    }
    .btn-success:hover{
      background:#256f4a;
    }

    @media (max-width:900px){
      .grid{grid-template-columns:1fr; }
      .topbar{flex-direction:column;align-items:flex-start;gap:12px}
      .cta{width:100%;justify-content:space-between}
    }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="brand">
        <div class="logo">WK</div>
        <div>
          <h1>Dashboard Demo Keamanan Web</h1>
          <p>Demonstrasi XSS</p>
        </div>
      </div>
      <div class="cta">
        <div class="badge">Posts: 2 &nbsp;•&nbsp; Comments: 0</div>
        <a href="pages/Beranda/beranda.html" class="btn-primary">Kembali ke Beranda</a>
      </div>
    </div>

    <div class="hero">
      <h2>Selamat Datang di Lab Keamanan Web</h2>
      <p>Halaman ini menyediakan demo interaktif untuk memahami kerentanan keamanan web seperti XSS (Cross-Site Scripting).</p>
      <div class="warn">⚠️ Peringatan: Halaman vulnerable hanya untuk pembelajaran. Jangan gunakan di produksi!</div>
    </div>

    <div class="section-title">
      <h3>Halaman Vulnerable (Untuk Demonstrasi)</h3>
    </div>

    <div class="grid">
      <div class="card">
        <div>
          <div class="head">
            <div class="icon vuln">⚠️</div>
            <div>
              <h4>auth_simple_vulnerable.php</h4>
              <div class="badge-small vuln-badge">VULNERABLE - XSS</div>
            </div>
          </div>
        </div>

        <div class="footer">
          <a href="pages/broken_acces/auth_simple_vulnerable.php" class="btn-danger">Buka Demo (VULNERABLE)</a>
        </div>
      </div>

      <div class="card">
        <div>
          <div class="head">
            <div class="icon vuln">⚠️</div>
            <div>
              <h4>create_user_vulnerable.php</h4>
              <div class="badge-small vuln-badge">VULNERABLE - XSS</div>
            </div>
          </div>
        </div>

        <div class="footer">
          <a href="pages/broken_acces/create_user_vulnerable.php" class="btn-danger">Buka Demo (VULNERABLE)</a>
        </div>
      </div>

      <div class="card">
        <div>
          <div class="head">
            <div class="icon vuln">⚠️</div>
            <div>
              <h4>artikel_vulnerable.php</h4>
              <div class="badge-small vuln-badge">VULNERABLE - File Upload</div>
            </div>
          </div>
        </div>

        <div class="footer">
          <a href="pages/broken_acces/artikel_vulnerable.php" class="btn-danger">Buka Demo (VULNERABLE)</a>
        </div>
      </div>
    </div>

    <div style="margin-top:32px;">
      <div class="section-title">
        <h3>Halaman Safe (Best Practice)</h3>
      </div>

      <div class="grid">
        <div class="card">
          <div>
            <div class="head">
              <div class="icon safe">✓</div>
              <div>
                <h4>auth_simple_secure.php</h4>
                <div class="badge-small safe-badge">SAFE - Protected</div>
              </div>
            </div>
          </div>

          <div class="footer">
            <a href="pages/broken_acces/auth_simple_secure.php" class="btn-success">Buka Demo (SAFE)</a>
          </div>
        </div>

        <div class="card">
          <div>
            <div class="head">
              <div class="icon safe">✓</div>
              <div>
                <h4>create_user_secure.php</h4>
                <div class="badge-small safe-badge">SAFE - Protected</div>
              </div>
            </div>
          </div>

          <div class="footer">
            <a href="pages/broken_acces/create_user_secure.php" class="btn-success">Buka Demo (SAFE)</a>
          </div>
        </div>

        <div class="card">
          <div>
            <div class="head">
              <div class="icon safe">✓</div>
              <div>
                <h4>artikel_secure.php</h4>
                <div class="badge-small safe-badge">SAFE - Protected</div>
              </div>
            </div>
          </div>

          <div class="footer">
            <a href="pages/broken_acces/artikel_secure.php" class="btn-success">Buka Demo (SAFE)</a>
          </div>
        </div>
      </div>
    </div>

  </div>
</body>
</html>