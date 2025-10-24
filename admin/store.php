<?php
// admin/store.php — utilidades para ler/escrever produtos/pedidos em JSON

function products_file(): string {
  // volta duas pastas (admin/ -> raiz/) e vai para storage/products.json
  return dirname(__DIR__).'/storage/products.json';
}

function load_products(): array {
  $f = products_file();
  if (!file_exists($f)) return [];
  $j = @file_get_contents($f);
  if ($j === false || $j === '') return [];
  $arr = json_decode($j, true);
  return is_array($arr) ? $arr : [];
}

function save_products(array $list): bool {
  $f = products_file();
  @mkdir(dirname($f), 0777, true);
  $json = json_encode(array_values($list), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
  return (bool) @file_put_contents($f, $json);
}

function next_product_id(array $list): int {
  $max = 0;
  foreach ($list as $p) { $max = max($max, (int)($p['id'] ?? 0)); }
  return $max + 1;
}

/* ================= PROMOTIONS (JSON) ================== */
function promotions_file(): string {
  return dirname(__DIR__) . '/storage/promotions.json';
}
function load_promotions(): array {
  $f = promotions_file();
  if (!file_exists($f)) return [];
  $j = @file_get_contents($f);
  if ($j === false || $j === '') return [];
  $arr = json_decode($j, true);
  return is_array($arr) ? $arr : [];
}
function save_promotions(array $list): bool {
  $f = promotions_file();
  @mkdir(dirname($f), 0777, true);
  $json = json_encode(array_values($list), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
  return (bool) @file_put_contents($f, $json);
}
function next_promo_id(array $list): int {
  $max = 0;
  foreach ($list as $p) { $max = max($max, (int)($p['id'] ?? 0)); }
  return $max + 1;
}

/* ================= ORDERS (JSON) ====================== */
function orders_file(): string {
  return dirname(__DIR__) . '/storage/orders.json';
}
function load_orders(): array {
  $f = orders_file();
  if (!file_exists($f)) return [];
  $j = @file_get_contents($f);
  if ($j === false || $j === '') return [];
  $arr = json_decode($j, true);
  return is_array($arr) ? $arr : [];
}
function save_orders(array $list): bool {
  $f = orders_file();
  @mkdir(dirname($f), 0777, true);
  $json = json_encode(array_values($list), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
  return (bool) @file_put_contents($f, $json);
}
function next_order_id(array $list): int {
  $max = 0;
  foreach ($list as $o) { $max = max($max, (int)($o['id'] ?? 0)); }
  return $max + 1;
}

/**
 * Guarda um pedido vindo do site.
 * $items = [ [product_id, name, qty, unit_price], ... ]
 * Retorna o ID do pedido criado.
 */
function add_order(array $items, float $shipping = 0.0, string $customer = '', ?string $date = null): int {
  $orders = load_orders();
  $total = 0.0;
  foreach ($items as $it) {
    $total += (float)$it['unit_price'] * (int)$it['qty'];
  }
  $total += (float)$shipping;

  $order = [
    'id'        => next_order_id($orders),
    'date'      => $date ?: date('Y-m-d H:i:s'),
    'customer'  => $customer,
    'items'     => array_map(function($it){
      return [
        'product_id' => (int)$it['product_id'],
        'name'       => (string)$it['name'],
        'qty'        => (int)$it['qty'],
        'unit_price' => (float)$it['unit_price'],
        'subtotal'   => (float)$it['unit_price'] * (int)$it['qty'],
      ];
    }, $items),
    'shipping'  => (float)$shipping,
    'total'     => (float)$total,
    'status'    => 'paid' // normalizamos depois no painel, se quiser
  ];
  $orders[] = $order;
  save_orders($orders);
  return (int)$order['id'];
}

/* ===== LEDGER (RECEITAS/DESPESAS MANUAIS) ============ */
function ledger_file(): string {
  return dirname(__DIR__) . '/storage/ledger.json';
}
function load_ledger(): array {
  $f = ledger_file();
  if (!file_exists($f)) return [];
  $j = @file_get_contents($f);
  if ($j === false || $j === '') return [];
  $arr = json_decode($j, true);
  return is_array($arr) ? $arr : [];
}
function save_ledger(array $list): bool {
  $f = ledger_file();
  @mkdir(dirname($f), 0777, true);
  $json = json_encode(array_values($list), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
  return (bool) @file_put_contents($f, $json);
}
function next_ledger_id(array $list): int {
  $max = 0;
  foreach ($list as $r) { $max = max($max, (int)($r['id'] ?? 0)); }
  return $max + 1;
}
function add_ledger_entry(string $title, string $type, float $value, string $date, string $obs=''): int {
  // $type: 'income' | 'expense'
  $rows = load_ledger();
  $rows[] = [
    'id'     => next_ledger_id($rows),
    'title'  => $title,
    'type'   => $type,
    'value'  => $value,
    'date'   => $date ?: date('Y-m-d'),
    'obs'    => $obs,
    'source' => 'manual'
  ];
  save_ledger($rows);
  return (int)end($rows)['id'];
}
