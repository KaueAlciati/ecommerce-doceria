<?php
$host = '127.0.0.1';   // ou 'localhost'
$db   = 'db_doceria';  // nome exato do seu banco no phpMyAdmin
$user = 'root';        // usuário padrão do XAMPP
$pass = '';            // senha (normalmente vazio no XAMPP)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('❌ Erro ao conectar ao banco: '.$e->getMessage());
}
