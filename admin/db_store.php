<?php
require_once __DIR__ . '/../includes/db_connect.php';

/* ===========================================================
   Helpers para descobrir colunas/PK dinamicamente (robustos)
   =========================================================== */

function db_column_exists(PDO $pdo, string $table, string $column): bool {
  $sql = "SELECT COUNT(*)
          FROM INFORMATION_SCHEMA.COLUMNS
          WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND COLUMN_NAME = ?";
  $st = $pdo->prepare($sql);
  $st->execute([$table, $column]);
  return (bool)$st->fetchColumn();
}

/** Retorna o primeiro nome de coluna que existir na tabela. */
function db_pick_column(PDO $pdo, string $table, array $candidates, string $fallback): string {
  foreach ($candidates as $c) {
    if (db_column_exists($pdo, $table, $c)) return $c;
  }
  return $fallback;
}

/** PKs “comuns” por tabela */
function db_pk_produtos(PDO $pdo): string {
  return db_pick_column($pdo, 'produtos', ['id','id_produto','produto_id','codigo','cod'], 'id');
}
function db_pk_pedidos(PDO $pdo): string {
  return db_pick_column($pdo, 'pedidos', ['id','id_pedido','pedido_id','codigo','cod'], 'id');
}
function db_pk_itens(PDO $pdo): string {
  return db_pick_column($pdo, 'itens_pedido', ['id','id_item','item_id','codigo','cod'], 'id');
}
function db_pk_promos(PDO $pdo): string {
  return db_pick_column($pdo, 'promocoes', ['id','id_promocao','promocao_id'], 'id');
}
function db_pk_renda(PDO $pdo): string {
  return db_pick_column($pdo, 'renda', ['id','id_lanc','lanc_id'], 'id');
}

/* ===========================================================
   PRODUTOS
   =========================================================== */

function db_products_all(PDO $pdo): array {
  $pk = db_pk_produtos($pdo);

  // Melhor coluna para ordenação
  $orderCol = $pk;
  if (!db_column_exists($pdo, 'produtos', $orderCol)) {
    $orderCol = db_pick_column($pdo, 'produtos', ['created_at','nome'], 'nome');
  }

  // Trazendo a PK como "id" (alias) para o restante do código funcionar
  $sql = "SELECT p.*, p.`{$orderCol}` AS order_col, p.`{$pk}` AS id
          FROM produtos p
          ORDER BY p.`{$orderCol}` DESC";
  return $pdo->query($sql)->fetchAll();
}

function db_products_add(PDO $pdo, array $p): int {
  // Certifique-se de que as colunas existem na sua tabela (ajuste se necessário)
  $sql = "INSERT INTO produtos (nome, preco, categoria, status, imagem, descricao, created_at)
          VALUES (:nome, :preco, :categoria, :status, :imagem, :descricao, NOW())";
  $pdo->prepare($sql)->execute([
    ':nome'      => $p['nome'],
    ':preco'     => $p['preco'],
    ':categoria' => $p['categoria'],
    ':status'    => $p['status'],      // 'available' | 'unavailable'
    ':imagem'    => $p['imagem'],
    ':descricao' => $p['descricao'],
  ]);
  return (int)$pdo->lastInsertId();
}

function db_products_toggle(PDO $pdo, int $id): void {
  $pk = db_pk_produtos($pdo);

  $cur = $pdo->prepare("SELECT status FROM produtos WHERE `{$pk}`=?");
  $cur->execute([$id]);
  $status = $cur->fetchColumn() ?: 'available';

  $new = $status === 'available' ? 'unavailable' : 'available';
  $pdo->prepare("UPDATE produtos SET status=? WHERE `{$pk}`=?")->execute([$new, $id]);
}

function db_products_delete(PDO $pdo, int $id): void {
  $pk = db_pk_produtos($pdo);
  $pdo->prepare("DELETE FROM produtos WHERE `{$pk}`=?")->execute([$id]);
}

/* ===========================================================
   PROMOÇÕES
   =========================================================== */

function db_promos_all(PDO $pdo): array {
  $prodPk = db_pk_produtos($pdo);
  $promoPk = db_pk_promos($pdo);

  $sql = "SELECT pr.*, p.nome AS produto_nome, p.preco AS preco_original,
                 p.`{$prodPk}` AS produto_pk
          FROM promocoes pr
          JOIN produtos p ON p.`{$prodPk}` = pr.produto_id
          ORDER BY pr.`{$promoPk}` DESC";
  return $pdo->query($sql)->fetchAll();
}

function db_promos_add(PDO $pdo, array $pr): int {
  $sql = "INSERT INTO promocoes
            (produto_id, titulo, preco_promocional, data_inicio, data_fim, destaque, ativa, created_at)
          VALUES
            (:produto_id, :titulo, :preco_promocional, :data_inicio, :data_fim, :destaque, :ativa, NOW())";
  $pdo->prepare($sql)->execute([
    ':produto_id'        => $pr['produto_id'],
    ':titulo'            => $pr['titulo'],
    ':preco_promocional' => $pr['preco_promocional'],
    ':data_inicio'       => $pr['data_inicio'] ?: null,
    ':data_fim'          => $pr['data_fim'] ?: null,
    ':destaque'          => $pr['destaque'] ? 1 : 0,
    ':ativa'             => $pr['ativa'] ? 1 : 0,
  ]);
  return (int)$pdo->lastInsertId();
}

function db_promos_toggle_active(PDO $pdo, int $id): void {
  $promoPk = db_pk_promos($pdo);
  $st = $pdo->prepare("UPDATE promocoes SET ativa = 1 - ativa WHERE `{$promoPk}`=?");
  $st->execute([$id]);
}

function db_promos_toggle_featured(PDO $pdo, int $id): void {
  $promoPk = db_pk_promos($pdo);
  $st = $pdo->prepare("UPDATE promocoes SET destaque = 1 - destaque WHERE `{$promoPk}`=?");
  $st->execute([$id]);
}

function db_promos_delete(PDO $pdo, int $id): void {
  $promoPk = db_pk_promos($pdo);
  $pdo->prepare("DELETE FROM promocoes WHERE `{$promoPk}`=?")->execute([$id]);
}

/* ===========================================================
   PEDIDOS
   =========================================================== */

/**
 * $items: [
 *   ['product_id'=>..,'name'=>..,'qty'=>..,'unit_price'=>..],
 *   ...
 * ]
 */
function db_orders_add(PDO $pdo, array $items, float $shipping, string $customer): int {
  $pdo->beginTransaction();
  try {
    $total = array_reduce($items, fn($s,$it)=> $s + (float)$it['unit_price']*(int)$it['qty'], 0.0);
    $total += (float)$shipping;

    $pdo->prepare("INSERT INTO pedidos (data, cliente, total, status) VALUES (NOW(), ?, ?, 'preparing')")
        ->execute([$customer, $total]);
    $orderId = (int)$pdo->lastInsertId();

    $ins = $pdo->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, nome, quantidade, preco_unit, subtotal)
                          VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($items as $it) {
      $subtotal = (float)$it['unit_price'] * (int)$it['qty'];
      $ins->execute([
        $orderId,
        (int)$it['product_id'],
        (string)$it['name'],
        (int)$it['qty'],
        (float)$it['unit_price'],
        $subtotal
      ]);
    }

    $pdo->commit();
    return $orderId;
  } catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
  }
}

function db_orders_all(PDO $pdo, string $filter='all'): array {
  $orderPk = db_pk_pedidos($pdo);
  $itemPk  = db_pk_itens($pdo);

  $where = '';
  $params = [];
  if ($filter !== 'all') { $where = "WHERE status = ?"; $params[] = $filter; }

  $sql = "SELECT * FROM pedidos {$where} ORDER BY `{$orderPk}` DESC";
  $st = $pdo->prepare($sql);
  $st->execute($params);
  $orders = $st->fetchAll();

  // carrega itens (ordena pela melhor PK disponível)
  $itSt = $pdo->prepare("SELECT * FROM itens_pedido WHERE pedido_id=? ORDER BY `{$itemPk}` ASC");
  foreach ($orders as &$o) {
    $itSt->execute([$o[$orderPk]]);
    $o['items'] = $itSt->fetchAll();
  }
  return $orders;
}

function db_orders_set_status(PDO $pdo, int $id, string $status): void {
  $allowed = ['preparing','delivered','cancelled'];
  if (!in_array($status,$allowed,true)) $status='preparing';

  $orderPk = db_pk_pedidos($pdo);
  $pdo->prepare("UPDATE pedidos SET status=? WHERE `{$orderPk}`=?")->execute([$status,$id]);
}

/* ===========================================================
   RENDA
   =========================================================== */

function db_income_month(PDO $pdo, string $ym): float {
  // Soma os pedidos do mês (exceto cancelados)
  $st = $pdo->prepare("SELECT COALESCE(SUM(total),0)
                       FROM pedidos
                       WHERE DATE_FORMAT(data,'%Y-%m')=? AND status <> 'cancelled'");
  $st->execute([$ym]);
  return (float)$st->fetchColumn();
}

function db_ledger_month(PDO $pdo, string $ym): array {
  $rendaPk = db_pk_renda($pdo);
  $st = $pdo->prepare("SELECT * FROM renda
                       WHERE DATE_FORMAT(data,'%Y-%m')=?
                       ORDER BY `{$rendaPk}` DESC, data DESC");
  $st->execute([$ym]);
  return $st->fetchAll();
}

function db_ledger_add(PDO $pdo, array $r): int {
  $sql = "INSERT INTO renda (titulo, tipo, valor, data, obs, origem)
          VALUES (:titulo,:tipo,:valor,:data,:obs,:origem)";
  $pdo->prepare($sql)->execute([
    ':titulo' => $r['titulo'],
    ':tipo'   => $r['tipo'],   // 'income' | 'expense'
    ':valor'  => $r['valor'],
    ':data'   => $r['data'],
    ':obs'    => $r['obs'] ?? '',
    ':origem' => $r['origem'] ?? 'manual',
  ]);
  return (int)$pdo->lastInsertId();
}
