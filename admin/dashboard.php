<?php
require __DIR__ . '/guard.php';
$cfg  = require __DIR__ . '/config.php';
$base = rtrim($cfg['base'] ?? '', '/');
$email = htmlspecialchars($cfg['owner_email']);
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Painel do Dono • Doceria</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cookie&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#fff7fb; --panel:#fdeef6; --text:#3b2b33; --muted:#8b6f7b;
      --card:#ffffff; --border:#f0d8e2; --shadow: 0 18px 50px rgba(183,102,138,.15);
      --pink:#ff66a1; --pink2:#ff87bb; --yellow:#ffc014; --purple:#b985ff; --green:#18c06a;
      --radius:22px;
    }
    *{box-sizing:border-box}
    body{margin:0;background:var(--bg);color:var(--text);font-family:Poppins,system-ui,Segoe UI,Roboto,Arial,sans-serif}
    header{
      background:linear-gradient(90deg,#ffd1e5, #fff0c8);
      padding:18px 20px;border-bottom:1px solid #f3dbe6;
    }
    .wrap{max-width:1180px;margin:0 auto}
    .head{
      display:flex;gap:14px;align-items:center;justify-content:space-between
    }
    .brand{display:flex;flex-direction:column}
    .logo{font-family:Cookie,cursive;font-size:32px;color:#e25c92;font-weight:700}
    .subtitle{color:var(--muted);margin-top:-2px}
    .right{display:flex;align-items:center;gap:14px;color:#7a5c69;font-weight:600}
    .btn{
      display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:14px;
      background:#fff;border:1px solid #efc5d8;color:#cf2f76;text-decoration:none;font-weight:700
    }
    .content{padding:28px 20px;background:#fff7fb}
    h2{font-size:32px;margin:6px 0 22px}
    .grid{
      display:grid;grid-template-columns:repeat(4,minmax(210px,1fr));gap:22px
    }
    .card{
      background:var(--card);border:1px solid var(--border);border-radius:var(--radius);
      box-shadow:var(--shadow);padding:26px 20px;text-align:center
    }
    .icon{
      width:74px;height:74px;border-radius:18px;margin:0 auto 12px;display:grid;place-items:center;color:#fff
    }
    .icon.pink{background:linear-gradient(180deg,#ff6aa8,#ff4f92)}
    .icon.yellow{background:linear-gradient(180deg,#ffd156,#ffb800)}
    .icon.purple{background:linear-gradient(180deg,#caa7ff,#ae7cff)}
    .icon.green{background:linear-gradient(180deg,#25d07e,#17ba68)}
    .card h3{margin:8px 0 6px;font-size:26px}
    .muted{color:var(--muted)}
    .card a{display:block;margin-top:12px;text-decoration:none;color:#cf2f76;font-weight:700}
    @media (max-width: 1024px){ .grid{grid-template-columns:repeat(2,1fr);} }
    @media (max-width: 560px){ .grid{grid-template-columns:1fr;} header{padding:14px} .content{padding:18px} }
  </style>
</head>
<body>
  <header>
    <div class="wrap head">
      <div class="brand">
        <div class="logo">Doceria Doce Encanto</div>
        <div class="subtitle">Painel do Dono</div>
      </div>
      <div class="right">
        <span>Bem-vindo,</span>
        <strong><?= $email ?></strong>
        <a class="btn" href="<?= $base ?>/admin/logout.php" title="Sair">
          <!-- ícone sair -->
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M15 7l5 5-5 5M20 12H9" stroke="#cf2f76" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Sair
        </a>
      </div>
    </div>
  </header>

  <div class="content">
    <div class="wrap">
      <h2>Atalhos rápidos</h2>
      <div class="grid">

        <!-- Produtos -->
        <div class="card">
          <div class="icon pink">
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none">
              <path d="M3 7l9-4 9 4-9 4-9-4z" fill="currentColor" opacity=".95"/>
              <path d="M3 7v10l9 4 9-4V7" stroke="rgba(255,255,255,.85)" stroke-width="2" stroke-linejoin="round"/>
            </svg>
          </div>
          <h3>Produtos</h3>
          <div class="muted">Gerenciar catálogo</div>
          <a href="<?= $base ?>/admin/produtos.php">Abrir</a>
        </div>

        <!-- Pedidos -->
        <div class="card">
          <div class="icon yellow">
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none">
              <path d="M6 6h15l-1.5 6H7.5L6 6z" fill="currentColor"/>
              <circle cx="9" cy="20" r="2" fill="#fff"/>
              <circle cx="18" cy="20" r="2" fill="#fff"/>
            </svg>
          </div>
          <h3>Pedidos</h3>
          <div class="muted">Acompanhar pedidos</div>
          <a href="<?= $base ?>/admin/pedidos.php">Abrir</a>
        </div>

        <!-- Promoções -->
        <div class="card">
          <div class="icon purple">
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none">
              <path d="M12 4l2.2 4.5L19 9l-3.5 3.2L16 18l-4-2.2L8 18l.5-5.8L5 9l4.8-.5L12 4z" fill="currentColor"/>
            </svg>
          </div>
          <h3>Promoções</h3>
          <div class="muted">Criar destaques da semana</div>
          <a href="<?= $base ?>/admin/promocoes.php">Abrir</a>
        </div>

        <!-- Renda -->
        <div class="card">
          <div class="icon green">
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none">
              <path d="M12 3v18M7 8c0-2 2.5-3 5-3s5 1 5 3-2 3-5 3-5 1-5 3 2.5 3 5 3 5-1 5-3" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </div>
          <h3>Renda</h3>
          <div class="muted">Visualizar e gerenciar lucro mensal</div>
          <a href="<?= $base ?>/admin/renda.php">Abrir</a>
        </div>

      </div>
    </div>
  </div>
</body>
</html>
