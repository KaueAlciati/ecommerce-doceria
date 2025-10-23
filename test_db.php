<?php
require __DIR__ . '/includes/db_connect.php';

$stmt = $pdo->query("SHOW TABLES");
echo "<h2>Conexão bem-sucedida!</h2><ul>";
while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
  echo "<li>{$row[0]}</li>";
}
echo "</ul>";
