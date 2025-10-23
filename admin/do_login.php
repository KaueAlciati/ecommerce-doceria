<?php
// admin/do_login.php
if (session_status() === PHP_SESSION_NONE) session_start();

$cfg  = require __DIR__ . '/config.php';
$base = rtrim($cfg['base'] ?? '', '/');

require __DIR__ . '/../includes/db_connect.php';

// pega dados do form
$email    = trim($_POST['email'] ?? '');
$pass     = $_POST['pass'] ?? '';
$remember = !empty($_POST['remember']);

if ($email === '' || $pass === '') {
  header("Location: {$base}/admin/login.php?err=1");
  exit;
}

// lembrar e-mail (30 dias)
if ($remember) {
  setcookie('remember_email', $email, time() + 30*24*3600, '/');
} else {
  setcookie('remember_email', '', time() - 3600, '/');
}

/**
 * 1) LOGIN DO DONO (config.php)
 * - se quiser desativar essa rota, basta remover este bloco
 */
$ownerEmail = $cfg['owner_email'] ?? null;
$ownerHash  = $cfg['owner_pass_hash'] ?? null;

if ($ownerEmail && $email === $ownerEmail && $ownerHash && password_verify($pass, $ownerHash)) {
  $_SESSION['owner_logged'] = true;
  $_SESSION['admin_email']  = $ownerEmail;
  $_SESSION['admin_name']   = $cfg['owner_user'] ?? 'Dono';
  header("Location: {$base}/admin/dashboard.php");
  exit;
}

/**
 * 2) LOGIN DE USUÁRIO DO BANCO (tabela usuarios)
 * - usa as colunas do seu schema: id_usuario, nome, email, senha, tipo_usuario
 */
$st = $pdo->prepare('SELECT id_usuario, nome, email, senha, tipo_usuario FROM usuarios WHERE email = ? LIMIT 1');
$st->execute([$email]);
$user = $st->fetch();

if ($user && password_verify($pass, $user['senha'])) {

  // se for DONO via DB, deixa acessar o painel
  if (strcasecmp($user['tipo_usuario'], 'dono') === 0) {
    $_SESSION['owner_logged'] = true;         // passa no guard do admin
    $_SESSION['admin_email']  = $user['email'];
    $_SESSION['admin_name']   = $user['nome'];
    $_SESSION['admin_id']     = (int)$user['id_usuario'];

    header("Location: {$base}/admin/dashboard.php");
    exit;
  }

  // se for CLIENTE, loga como cliente e manda pra loja
  $_SESSION['client_id']    = (int)$user['id_usuario'];
  $_SESSION['client_email'] = $user['email'];
  $_SESSION['client_name']  = $user['nome'];

  header("Location: {$base}/index.php");
  exit;
}

// falhou: volta com erro
header("Location: {$base}/admin/login.php?err=1");
exit;
