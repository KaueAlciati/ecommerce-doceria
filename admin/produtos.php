<?php
require __DIR__ . '/guard.php';
$cfg  = require __DIR__ . '/config.php';
$base = rtrim($cfg['base'] ?? '', '/');

// agora usamos o banco de dados via PDO:
require __DIR__ . '/db_store.php'; // funções db_products_all, db_products_add etc.

/* --------- Ações (POST) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'add') {
    $name      = trim($_POST['name'] ?? '');
    $price     = (float)($_POST['price'] ?? 0);
    $category  = trim($_POST['category'] ?? '');
    $status    = in_array($_POST['status'] ?? 'available', ['available','unavailable']) ? $_POST['status'] : 'available';
    $image     = trim($_POST['image'] ?? '');
    $desc      = trim($_POST['description'] ?? '');

    if ($name !== '') {
      db_products_add($pdo, [
        'nome'       => $name,
        'preco'      => $price,
        'categoria'  => $category,
        'status'     => $status,
        'imagem'     => $image,
        'descricao'  => $desc,
      ]);
      header("Location: {$base}/admin/produtos.php?ok=1");
      exit;
    }
  }

  if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    db_products_delete($pdo, $id);
    header("Location: {$base}/admin/produtos.php?deleted=1");
    exit;
  }

  if ($action === 'toggle') {
    $id = (int)($_POST['id'] ?? 0);
    db_products_toggle($pdo, $id);
    header("Location: {$base}/admin/produtos.php?toggled=1");
    exit;
  }
}

/* --------- Listagem (GET) ---------- */
$q       = trim($_GET['q'] ?? '');
$filter  = $_GET['filter'] ?? 'all'; // all | available | unavailable

$products = db_products_all($pdo);

// filtros de busca e status
$view = array_filter($products, function($p) use ($q, $filter) {
  $ok = true;
  if ($q !== '') {
    $hay = mb_strtolower(($p['nome'] ?? '') . ' ' . ($p['categoria'] ?? ''));
    $ok = $ok && str_contains($hay, mb_strtolower($q));
  }
  if ($filter === 'available')   $ok = $ok && (($p['status'] ?? 'available') === 'available');
  if ($filter === 'unavailable') $ok = $ok && (($p['status'] ?? 'available') === 'unavailable');
  return $ok;
});

// ordena por id desc (mais novo primeiro)
usort($view, fn($a,$b) => (int)$b['id'] <=> (int)$a['id']);
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Produtos • Painel</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root{
    --bg:#fdf9fb; --text:#3b2b33; --muted:#8b6f7b;
    --card:#ffffff; --border:#f3d6e2; --ring:#f4c6d5; --shadow:0 14px 40px rgba(181,102,138,.14);
    --grad: linear-gradient(180deg,#ff8bb6,#ff5ea2);
    --radius:16px;
  }
  *{box-sizing:border-box}
  body{margin:0;background:#fff7fb;color:var(--text);font-family:Poppins,system-ui,Segoe UI,Roboto,Arial,sans-serif}
  header{background:#ffe0ef;padding:14px 16px;border-bottom:1px solid #f3dbe6}
  .wrap{max-width:1180px;margin:0 auto;padding:20px}
  h1{font-size:38px;margin:0 0 18px}
  .toolbar{display:flex;gap:14px;flex-wrap:wrap;margin-bottom:14px}
  .input,.select{
    border:1.6px solid var(--border); border-radius:14px; padding:12px 14px; background:#fff; outline:none; transition:.15s; color:#4d3b44;
  }
  .input:focus,.select:focus{border-color:var(--ring); box-shadow:0 0 0 4px rgba(240,143,176,.15)}
  .btn{border-radius:14px; padding:12px 16px; border:1px solid #f2a7c2; background:var(--grad); color:#fff; font-weight:700; cursor:pointer}
  .panel{background:var(--card); border:1px solid var(--border); border-radius:20px; box-shadow:var(--shadow); padding:18px; margin-bottom:18px}
  .grid2{display:grid; grid-template-columns:1fr 120px 1fr 170px; gap:14px}
  .grid1{display:grid; grid-template-columns:1fr; gap:14px; margin-top:10px}
  .list{background:var(--card); border:1px solid var(--border); border-radius:20px; box-shadow:var(--shadow); padding:12px}
  .item{display:grid; grid-template-columns:70px 1fr 120px 140px 170px; gap:12px; align-items:center; border-bottom:1px solid #f2e2ea; padding:10px 8px}
  .item:last-child{border-bottom:0}
  .thumb{width:70px; height:70px; border-radius:12px; object-fit:cover; background:#f6eaf0; border:1px solid var(--border)}
  .muted{color:var(--muted)}
  .pill{display:inline-block; padding:6px 10px; border-radius:999px; border:1px solid #e7c7d6; background:#fff}
  .row{display:flex; gap:8px; flex-wrap:wrap}
  .danger{background:#ffe3ea; border-color:#ffc7d6; color:#b5315e}
  .ok{background:#eafff1; border-color:#c9f1d7; color:#1f8a4d}
  .btn-sm{padding:8px 10px; border-radius:12px; border:1px solid #e7c7d6; background:#fff; cursor:pointer}
  .right{display:flex; justify-content:flex-end}
  @media (max-width:980px){
    .item{grid-template-columns:70px 1fr 110px 1fr; grid-auto-rows:auto}
  }
</style>
</head>
<body>
<header><div class="wrap"><a href="<?= $base ?>/admin/dashboard.php" style="text-decoration:none;color:#cf2f76">← Painel</a></div></header>
<div class="wrap">
  <h1>Produtos</h1>

  <!-- Filtro -->
  <form class="toolbar" method="get" action="">
    <input class="input" type="search" name="q" placeholder="Buscar por nome..." value="<?= htmlspecialchars($q) ?>" style="min-width:260px">
    <select class="select" name="filter">
      <option value="all"        <?= $filter==='all'?'selected':'' ?>>Todos</option>
      <option value="available"  <?= $filter==='available'?'selected':'' ?>>Disponível</option>
      <option value="unavailable"<?= $filter==='unavailable'?'selected':'' ?>>Indisponível</option>
    </select>
    <button class="btn" type="submit">Atualizar</button>
  </form>

  <!-- Form de cadastro -->
  <form class="panel" method="post" action="">
    <input type="hidden" name="action" value="add">
    <div class="grid2">
      <input class="input" name="name" placeholder="Nome" required>
      <input class="input" name="price" placeholder="0" value="0" type="number" step="0.01" min="0">
      <input class="input" name="category" placeholder="Categoria">
      <select class="select" name="status">
        <option value="available" selected>Disponível</option>
        <option value="unavailable">Indisponível</option>
      </select>
    </div>
    <div class="grid1">
      <input class="input" name="image" placeholder="URL da imagem">
      <input class="input" name="description" placeholder="Descrição">
    </div>
    <div class="right" style="margin-top:12px">
      <button class="btn" type="submit">Adicionar produto</button>
    </div>
  </form>

  <!-- Lista -->
  <div class="list">
    <?php if (!$view): ?>
      <div class="panel muted">Nenhum produto cadastrado.</div>
    <?php else: ?>
      <?php foreach ($view as $p): ?>
        <div class="item">
          <img class="thumb" src="<?= htmlspecialchars($p['imagem'] ?: $base.'/assets/product-cupcakes.jpg') ?>" alt="">
          <div>
            <div style="font-weight:700"><?= htmlspecialchars($p['nome']) ?></div>
            <div class="muted"><?= htmlspecialchars($p['categoria'] ?: 'Sem categoria') ?></div>
          </div>
          <div><span class="pill"><?= 'R$ '.number_format((float)$p['preco'],2,',','.') ?></span></div>
          <div>
            <?php if(($p['status'] ?? 'available') === 'available'): ?>
              <span class="pill ok">Disponível</span>
            <?php else: ?>
              <span class="pill danger">Indisponível</span>
            <?php endif; ?>
          </div>
          <div class="row">
            <form method="post" action="">
              <input type="hidden" name="action" value="toggle">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <button class="btn-sm" type="submit">
                <?= ($p['status'] ?? 'available') === 'available' ? 'Indisponibilizar' : 'Disponibilizar' ?>
              </button>
            </form>
            <form method="post" action="" onsubmit="return confirm('Tem certeza que deseja excluir este produto?')">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <button class="btn-sm danger" type="submit">Excluir</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
