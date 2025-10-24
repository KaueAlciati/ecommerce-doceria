<?php
// notify_telegram.php
require_once __DIR__ . '/includes/telegram.php';

// Campos esperados no POST
$text   = trim($_POST['text']    ?? '');
$chatId = trim($_POST['chat_id'] ?? '');

if ($text === '') {
  http_response_code(400);
  echo 'Mensagem vazia.';
  exit;
}

// Envia
$ok = telegram_send($text, $chatId ?: null);

// Volta para a página anterior (sucesso ou erro simples)
$back = $_POST['back'] ?? 'index.php';
if ($ok) {
  header("Location: {$back}");
  exit;
}

http_response_code(500);
echo 'Falha ao enviar no Telegram.';
