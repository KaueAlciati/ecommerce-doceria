<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['owner_logged'])) {
  $cfg = require __DIR__ . '/config.php';
  $base = rtrim($cfg['base'] ?? '', '/');
  header("Location: {$base}/login");
  exit;
}
