<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$cfg  = require __DIR__ . '/config.php';
$base = rtrim($cfg['base'] ?? '', '/');

// se já estiver logado como dono, manda pro painel
if (!empty($_SESSION['owner_logged'])) {
  header("Location: {$base}/admin/dashboard.php");
  exit;
}

$err             = isset($_GET['err']) ? (int)$_GET['err'] : 0;
$remember_email  = $_COOKIE['remember_email'] ?? '';
?>
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Doce Encanto • Entrar</title>
    <style>
      :root{
        --bg1:#f9d7e4; --bg2:#faf7ef; --txt:#4b2d3a; --muted:#7d6570; --brand:#f08fb0; --brand2:#ff6aa8; --input:#fbf1f3; --ring:#f4c6d5;
      }
      *{box-sizing:border-box}
      html,body{height:100%}
      body{
        margin:0; font-family: Poppins, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; color:var(--txt);
        background: radial-gradient(1200px 600px at 0% 0%, var(--bg1), transparent 60%),
                    radial-gradient(1200px 600px at 100% 100%, var(--bg2), transparent 60%),
                    linear-gradient(135deg, #fdeef4, #fbfaf6);
        display:flex; align-items:flex-start; justify-content:center; padding:40px 16px;
      }
      .card{
        width:100%; max-width:540px; background:#fff; border-radius:20px; border:1px solid #f0e6ea;
        box-shadow: 0 20px 50px rgba(0,0,0,.06); padding:34px 26px 30px;
      }
      .brand{
        text-align:center; margin:6px 0 16px; font-weight:700; color:#e06a92; font-size:28px; font-family: 'Cookie', cursive;
      }
      .subtitle{ text-align:center; color:var(--muted); margin-bottom:24px; }
      label{ display:block; margin:.6rem 0 .35rem; font-weight:600; }
      .input{
        width:100%; border:1.5px solid #f1e3e7; background:var(--input); color:#5b3f49;
        border-radius:16px; padding:14px 16px; outline:none; transition:.2s;
      }
      .input:focus{ border-color: var(--ring); box-shadow:0 0 0 4px rgba(240,143,176,.15); background:#fff; }
      .row{ display:flex; align-items:center; gap:.6rem; margin:12px 2px; color:var(--muted); }
      .btn{
        width:100%; border-radius:16px; border:1px solid #f2a7c2; background: linear-gradient(180deg, var(--brand), var(--brand2));
        color:#fff; font-weight:700; padding:14px 16px; cursor:pointer; transition:.15s; letter-spacing:.2px;
      }
      .btn:hover{ filter:brightness(.97) }
      .links{ text-align:center; margin-top:14px; }
      .links a{ color:#d36f98; text-decoration:none }
      .links a:hover{ text-decoration:underline }
      .error{ color:#d14b6a; background:#ffe8ee; border:1px solid #ffd0dc; padding:10px 12px; border-radius:12px; margin:10px 0; font-size:.95rem }
      .back{ display:block; text-align:center; margin-top:6px; color:#ad7ca0; text-decoration:none }
      .back:hover{ text-decoration:underline }
      @media (max-width:560px){ .card{ padding:26px 18px; } }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cookie&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  </head>
  <body>
    <form class="card" method="post" action="<?= $base ?>/admin/do_login.php" novalidate>
      <div class="brand">Doce Encanto</div>
      <div class="subtitle">Entre na sua conta</div>

      <?php if($err): ?>
        <div class="error">E-mail ou senha inválidos.</div>
      <?php endif; ?>

      <label for="email">E-mail</label>
      <input id="email" class="input" type="email" name="email" placeholder="seu@email.com"
             value="<?= htmlspecialchars($remember_email) ?>" required>

      <label for="pass">Senha</label>
      <input id="pass" class="input" type="password" name="pass" placeholder="••••••••" required>

      <div class="row">
               <input id="remember" type="checkbox" name="remember" style="accent-color:#ff6aa8">
        <label for="remember" style="margin:0; font-weight:500;">Lembrar-me</label>
      </div>

      <button class="btn" type="submit">Entrar</button>

      <div class="links">
        Não tem uma conta?
        <a href="<?= $base ?>/admin/register.php">Cadastre-se</a>
      </div>

      <a class="back" href="<?= $base ?>/">Voltar para a loja</a>
    </form>
  </body>
</html>
