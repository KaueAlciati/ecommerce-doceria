<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$cfg   = require __DIR__ . '/config.php';
$base  = rtrim($cfg['base'] ?? '', '/');

$email = trim($_POST['email'] ?? ''); // campo do formulário (aceitaremos email OU usuário aqui)
$pass  = trim($_POST['pass']  ?? '');

$targetEmail = strtolower(trim($cfg['owner_email']));
$targetUser  = strtolower(trim($cfg['owner_user']));
$inputId     = strtolower($email);

// valida login por email OU por usuário
$okIdentity = ($inputId === $targetEmail) || ($inputId === $targetUser);

// valida senha: hash ou (se liberado) texto plano
$okPassHash = password_verify($pass, $cfg['owner_pass_hash']);
$okPassText = !empty($cfg['dev_allow_plain']) && isset($cfg['owner_pass_plain']) && $pass === $cfg['owner_pass_plain'];
$okPass     = $okPassHash || $okPassText;

// DEBUG OPCIONAL: ative adicionando ?debug=1 na URL do login.php
if (!empty($_GET['debug'])) {
  header('Content-Type: text/plain; charset=utf-8');
  echo "DEBUG LOGIN\n\n";
  echo "Digitado (email/usuario): {$email}\n";
  echo "Senha digitada (oculta)\n";
  echo "Comparando com:\n- owner_email: {$cfg['owner_email']}\n- owner_user: {$cfg['owner_user']}\n";
  echo "okIdentity: " . ($okIdentity ? 'true' : 'false') . "\n";
  echo "okPassHash: " . ($okPassHash ? 'true' : 'false') . "\n";
  echo "okPassText(dev): " . ($okPassText ? 'true' : 'false') . "\n";
  echo "okPass: " . ($okPass ? 'true' : 'false') . "\n";
  exit;
}

if ($okIdentity && $okPass) {
  $_SESSION['owner_logged'] = true;
  header("Location: {$base}/admin/dashboard.php");
  exit;
}

header("Location: {$base}/admin/login.php?err=1");
