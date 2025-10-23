<?php
require __DIR__ . '/guard.php';
$cfg  = require __DIR__ . '/config.php';
$base = rtrim($cfg['base'] ?? '', '/');

// >>> usa PDO / funções do banco
require __DIR__ . '/db_store.php'; // db_orders_all(), db_orders_set_status()

/* ------------ AÇÕES (POST) ------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'set_status') {
  $id     = (int)($_POST['id'] ?? 0);
  $status = $_POST['status'] ?? 'preparing'; // preparing | delivered | cancelled

  db_orders_set_status($pdo, $id, $status);

  header("Location: {$base}/admin/pedidos.php?filter=" . urlencode($_GET['filter'] ?? 'all'));
  exit;
}

/* ------------ LISTAGEM (GET) ------------ */
$filter = $_GET['filter'] ?? 'all'; // all | preparing | delivered | cancelled
$orders = db_orders_all($pdo, $filter); // já vem ordenado por data DESC no db_store.php
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Pedidos • Painel</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root{
    --bg:#0f0f13; --panel:#15151c; --text:#f3ecf1; --muted:#b6a9b1;
    --border:#272536; --green:#45e39a; --yellow:#ffd36b; --red:#ff8ca6;
    --grad:linear-gradient(180deg,#ff8bb6,#ff5ea2);
  }
  *{box-sizing:border-box}
  body{margin:0;background:var(--bg);color:var(--text);font-family:Poppins,system-ui,Segoe UI,Roboto,Arial,sans-serif}
  a{color:#ff86b5;text-decoration:none}
  header{background:#1e1b28;padding:14px 16px;border-bottom:1px solid #2b2435}
  .wrap{max-width:1180px;margin:0 auto;padding:20px}

  h1{font-size:34px;margin:0 0 12px}
  .top{display:flex;gap:12px;flex-wrap:wrap;align-items:center;justify-content:space-between}

  .tabs{display:flex;gap:8px;flex-wrap:wrap}
  .tab{
    padding:10px 14px;border-radius:999px;border:1px solid #3a3448;background:#12121a;color:#cdb8c5;
    text-decoration:none;font-weight:600
  }
  .tab.active{background:#ff7fb0;color:#fff;border-color:#ff7fb0}

  .empty{background:#fff;border:1px solid #efdfeb;color:#6a4f5a;border-radius:20px;padding:24px;text-align:center}

  .table{background:var(--panel);border:1px solid var(--border);border-radius:20px;overflow:hidden}
  .thead,.row{display:grid;grid-template-columns:120px 1.2fr 2fr 140px 140px 220px;align-items:center}
  .thead{background:#191922;border-bottom:1px solid #2b2435;color:#cdb8c5;font-weight:700}
  .cell{padding:14px;border-bottom:1px solid #2a2435}
  .row:last-child .cell{border-bottom:0}
  .muted{color:var(--muted)}
  .badge{display:inline-block;padding:6px 10px;border-radius:999px;border:1px solid #3a3448;background:#111118}
  .preparing{border-color:#5b4f70;background:#201c2b;color:#ffd36b}
  .delivered{border-color:#357a57;background:#15221b;color:#45e39a}
  .cancelled{border-color:#7a354a;background:#2a161b;color:#ff8ca6}
  .btn{padding:8px 12px;border-radius:12px;border:1px solid #e7c7d6;background:#fff;color:#4a3b44;cursor:pointer}
  .btn-primary{border-color:#f2a7c2;background:var(--grad);color:#fff}
  .btn-danger{background:#ffe3ea;border-color:#ffc7d6;color:#b5315e}
  .row-actions{display:flex;gap:8px;flex-wrap:wrap}
  @media (max-width:1020px){
    .thead,.row{grid-template-columns:100px 1fr 1.4fr 120px 120px 1fr}
  }
  @media (max-width:720px){
    .thead{display:none}
    .row{grid-template-columns:1fr;gap:8px}
    .cell{border-bottom:0;padding:10px}
    .row{border:1px solid var(--border);border-radius:16px;margin:10px 0;background:#12121a}
  }
</style>
</head>
<body>
<header><div class="wrap"><a href="<?= $base ?>/admin/dashboard.php">← Voltar</a></div></header>

<div class="wrap">
  <div class="top">
    <h1>Gerenciar Pedidos</h1>
    <nav class="tabs">
      <?php
        $tabs = [
          'all'        => 'Todos',
          'preparing'  => 'Em preparo',
          'delivered'  => 'Entregues',
          'cancelled'  => 'Cancelados',
        ];
        foreach ($tabs as $k=>$label):
          $href = "{$base}/admin/pedidos.php?filter={$k}";
      ?>
        <a class="tab <?= $filter===$k?'active':'' ?>" href="<?= $href ?>"><?= $label ?></a>
      <?php endforeach; ?>
    </nav>
  </div>

  <?php if (!$orders): ?>
    <div class="empty">Nenhum pedido encontrado</div>
  <?php else: ?>
    <div class="table" style="margin-top:14px">
      <div class="thead">
        <div class="cell"># Pedido</div>
        <div class="cell">Data</div>
        <div class="cell">Itens</div>
        <div class="cell">Cliente</div>
        <div class="cell">Total</div>
        <div class="cell">Status / Ações</div>
      </div>

      <?php foreach ($orders as $o): ?>
        <?php
          $status     = $o['status'] ?? 'preparing';       // preparing|delivered|cancelled
          $badgeClass = $status==='delivered' ? 'delivered' : ($status==='cancelled' ? 'cancelled' : 'preparing');
          $itemsText  = implode(', ', array_map(fn($it)=> (int)$it['quantidade'].'x '.$it['nome'], $o['items'] ?? []));
          $date       = date('d/m/Y H:i', strtotime($o['data'] ?? date('Y-m-d H:i:s')));
        ?>
        <div class="row">
          <div class="cell"><strong>#<?= (int)$o['id'] ?></strong></div>
          <div class="cell"><?= $date ?></div>
          <div class="cell"><span class="muted"><?= htmlspecialchars($itemsText ?: '-') ?></span></div>
          <div class="cell"><span class="muted"><?= htmlspecialchars($o['cliente'] ?? '-') ?></span></div>
          <div class="cell"><strong>R$ <?= number_format((float)($o['total'] ?? 0),2,',','.') ?></strong></div>
          <div class="cell">
            <div class="row-actions">
              <span class="badge <?= $badgeClass ?>">
                <?= $status==='delivered' ? 'Entregue' : ($status==='cancelled'?'Cancelado':'Em preparo') ?>
              </span>

              <?php if ($status !== 'delivered'): ?>
              <form method="post" action="">
                <input type="hidden" name="action" value="set_status">
                <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                <input type="hidden" name="status" value="delivered">
                <button class="btn btn-primary" type="submit">Marcar entregue</button>
              </form>
              <?php endif; ?>

              <?php if ($status !== 'preparing'): ?>
              <form method="post" action="">
                <input type="hidden" name="action" value="set_status">
                <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                <input type="hidden" name="status" value="preparing">
                <button class="btn" type="submit">Voltar p/ preparo</button>
              </form>
              <?php endif; ?>

              <?php if ($status !== 'cancelled'): ?>
              <form method="post" action="" onsubmit="return confirm('Cancelar este pedido?')">
                <input type="hidden" name="action" value="set_status">
                <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                <input type="hidden" name="status" value="cancelled">
                <button class="btn btn-danger" type="submit">Cancelar</button>
              </form>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
