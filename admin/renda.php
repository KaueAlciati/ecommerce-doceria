<?php
require __DIR__ . '/guard.php';
$cfg  = require __DIR__ . '/config.php';
$base = rtrim($cfg['base'] ?? '', '/');

require __DIR__ . '/store.php'; // precisa das funções: load_orders, load_ledger, add_ledger_entry etc.

// ----- mês selecionado (YYYY-MM) -----
$ym = $_GET['month'] ?? date('Y-m');
if (!preg_match('/^\d{4}-\d{2}$/', $ym)) $ym = date('Y-m');

$start = $ym . '-01';
$end   = date('Y-m-t', strtotime($start));

// ----- adicionar lançamento manual -----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_ledger') {
  $title = trim($_POST['title'] ?? '');
  $type  = ($_POST['type'] ?? 'income') === 'expense' ? 'expense' : 'income';
  $value = (float)($_POST['value'] ?? 0);
  $date  = $_POST['date'] ?? date('Y-m-d');
  $obs   = trim($_POST['obs'] ?? '');

  if ($title !== '' && $value > 0) {
    add_ledger_entry($title, $type, $value, $date, $obs);
  }
  header("Location: {$base}/admin/renda.php?month={$ym}");
  exit;
}

// ----- carrega dados -----
$orders = load_orders();
$ledger = load_ledger();

// filtra por mês
$orders_m = array_filter($orders, function ($o) use ($start,$end) {
  $d = substr($o['date'], 0, 10);
  return $d >= $start && $d <= $end;
});
$ledger_m = array_filter($ledger, function ($r) use ($start,$end) {
  $d = substr($r['date'], 0, 10);
  return $d >= $start && $d <= $end;
});

// totais
$receitas_pedidos = 0.0;
foreach ($orders_m as $o) $receitas_pedidos += (float)$o['total'];

$receitas_man = 0.0;
$despesas_man = 0.0;
foreach ($ledger_m as $r) {
  if (($r['type'] ?? 'income') === 'expense') $despesas_man += (float)$r['value'];
  else $receitas_man += (float)$r['value'];
}

$receitas = $receitas_pedidos + $receitas_man;
$despesas = $despesas_man;
$saldo    = $receitas - $despesas;

// tabela unificada (pedidos + manuais)
$rows = [];
foreach ($orders_m as $o) {
  $rows[] = [
    'date'  => substr($o['date'],0,10),
    'title' => 'Pedido #' . (int)$o['id'],
    'type'  => 'income',
    'value' => (float)$o['total'],
    'obs'   => 'Itens: ' . implode(', ', array_map(fn($it)=> "{$it['qty']}x {$it['name']}", $o['items'] ?? [])),
    'source'=> 'order'
  ];
}
foreach ($ledger_m as $r) {
  $rows[] = [
    'date'  => $r['date'],
    'title' => $r['title'],
    'type'  => $r['type'],
    'value' => (float)$r['value'],
    'obs'   => $r['obs'] ?? '',
    'source'=> 'manual'
  ];
}
usort($rows, fn($a,$b)=> strcmp($b['date'], $a['date']));
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Renda • Painel</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root{
    --bg:#0f0f13; --panel:#15151c; --text:#f3ecf1; --muted:#b6a9b1;
    --green:#45e39a; --red:#ff8ca6; --border:#272536;
    --grad:linear-gradient(180deg,#ff8bb6,#ff5ea2);
    --radius:16px;
  }
  *{box-sizing:border-box}
  body{margin:0;background:var(--bg);color:var(--text);font-family:Poppins,system-ui,Segoe UI,Roboto,Arial,sans-serif}
  a{color:#ff86b5;text-decoration:none}
  header{background:#1e1b28;padding:14px 16px;border-bottom:1px solid #2b2435}
  .wrap{max-width:1180px;margin:0 auto;padding:20px}
  h1{font-size:38px;margin:0 0 6px}
  .muted{color:var(--muted)}
  .top{display:flex;gap:12px;flex-wrap:wrap;align-items:center;justify-content:space-between}
  .kpis{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin:16px 0 18px}
  .kpi{background:var(--panel);border:1px solid var(--border);border-radius:20px;padding:18px}
  .kpi .label{font-size:14px;color:#cdb8c5;margin-bottom:8px}
  .kpi .value{font-size:28px;font-weight:800}
  .green{color:var(--green)} .red{color:var(--red)}
  .panel{background:var(--panel);border:1px solid var(--border);border-radius:20px;padding:14px 16px;margin-bottom:16px}
  .input,.select{width:100%;border:1.6px solid #3a3448;border-radius:14px;padding:12px 14px;background:#0f0f16;color:#e9dfe6;outline:none}
  .input:focus,.select:focus{box-shadow:0 0 0 3px rgba(255,134,181,.15);border-color:#4b3f5c}
  .btn{border-radius:14px;padding:12px 16px;border:1px solid #f2a7c2;background:var(--grad);color:#fff;font-weight:700;cursor:pointer}
  .grid4{display:grid;grid-template-columns:1.8fr 1fr 1fr 1fr;gap:12px}
  .grid1{display:grid;grid-template-columns:1fr;gap:12px;margin-top:10px}
  table{width:100%;border-collapse:collapse;background:var(--panel);border:1px solid var(--border);border-radius:20px;overflow:hidden}
  th,td{padding:14px;border-bottom:1px solid #2a2435;text-align:left}
  th{color:#cdb8c5}
  .right{text-align:right}
</style>
</head>
<body>
<header><div class="wrap"><a href="<?= $base ?>/admin/dashboard.php">← Voltar</a></div></header>

<div class="wrap">
  <div class="top">
    <div>
      <h1>Renda</h1>
      <div class="muted">Visualize receitas, despesas e lucro mensal.</div>
    </div>
    <form method="get" action="<?= $base ?>/admin/renda.php">
      <label class="muted" style="font-size:14px">Mês</label><br>
      <input class="input" type="month" name="month" value="<?= htmlspecialchars($ym) ?>" style="min-width:240px">
    </form>
  </div>

  <div class="kpis">
    <div class="kpi">
      <div class="label">Receitas</div>
      <div class="value green">R$ <?= number_format($receitas,2,',','.') ?></div>
    </div>
    <div class="kpi">
      <div class="label">Despesas</div>
      <div class="value red">R$ <?= number_format($despesas,2,',','.') ?></div>
    </div>
    <div class="kpi">
      <div class="label">Saldo</div>
      <div class="value">R$ <?= number_format($saldo,2,',','.') ?></div>
    </div>
  </div>

  <!-- Lançamento manual -->
  <form class="panel" method="post" action="">
    <input type="hidden" name="action" value="add_ledger">
    <div class="grid4">
      <input class="input" name="title" placeholder="Título (ex.: Pedido #1023 / Compra de insumos)" required>
      <select class="select" name="type">
        <option value="income">Receita</option>
        <option value="expense">Despesa</option>
      </select>
      <input class="input" type="number" name="value" step="0.01" min="0" placeholder="0" required>
      <input class="input" type="date" name="date" value="<?= date('Y-m-d') ?>">
    </div>
    <div class="grid1">
      <input class="input" name="obs" placeholder="Observações">
    </div>
    <div style="margin-top:10px"><button class="btn" type="submit">Adicionar lançamento</button></div>
  </form>

  <!-- Tabela -->
  <table>
    <thead>
      <tr>
        <th>Data</th>
        <th>Título</th>
        <th>Tipo</th>
        <th class="right">Valor</th>
        <th>Obs.</th>
        <th>Origem</th>
      </tr>
    </thead>
    <tbody>
      <?php if(!$rows): ?>
        <tr><td class="muted" colspan="6">Sem lançamentos neste mês.</td></tr>
      <?php else: ?>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= date('d/m/Y', strtotime($r['date'])) ?></td>
            <td><?= htmlspecialchars($r['title']) ?></td>
            <td><?= $r['type']==='expense'?'Despesa':'Receita' ?></td>
            <td class="right">R$ <?= number_format($r['value'],2,',','.') ?></td>
            <td><?= htmlspecialchars($r['obs']) ?></td>
            <td><?= $r['source']==='order'?'Pedido':'Manual' ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <div style="margin-top:12px">
    <a class="btn" href="<?= $base ?>/admin/renda_export.php?month=<?= htmlspecialchars($ym) ?>">Exportar CSV do mês</a>
  </div>
</div>
</body>
</html>
