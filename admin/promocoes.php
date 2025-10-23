<?php
require __DIR__ . '/guard.php';
$cfg  = require __DIR__ . '/config.php';
$base = rtrim($cfg['base'] ?? '', '/');

require __DIR__ . '/store.php';

// ============ AÇÕES (POST) ============
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  $promos = load_promotions();
  $products = load_products();

  if ($action === 'create') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $title      = trim($_POST['title'] ?? '');
    $price_promo= (float)($_POST['price_promo'] ?? 0);
    $start      = trim($_POST['start'] ?? '');
    $end        = trim($_POST['end'] ?? '');
    $featured   = isset($_POST['featured']) ? true : false;
    $active     = isset($_POST['active']) ? true : false;

    // pega preço original do produto
    $product = null;
    foreach ($products as $p) { if ((int)$p['id'] === $product_id) { $product = $p; break; } }

    if ($product && $title !== '' && $price_promo > 0) {
      $promos[] = [
        'id'            => next_promo_id($promos),
        'product_id'    => $product_id,
        'product_name'  => $product['name'] ?? 'Produto',
        'price_orig'    => (float)($product['price'] ?? 0),
        'price_promo'   => $price_promo,
        'title'         => $title,
        'start'         => $start,
        'end'           => $end,
        'featured'      => $featured,
        'active'        => $active,
        'created_at'    => date('Y-m-d H:i:s'),
      ];
      save_promotions($promos);
      header("Location: {$base}/admin/promocoes.php?ok=1");
      exit;
    } else {
      header("Location: {$base}/admin/promocoes.php?new=1&err=1");
      exit;
    }
  }

  if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    $promos = array_values(array_filter($promos, fn($x) => (int)$x['id'] !== $id));
    save_promotions($promos);
    header("Location: {$base}/admin/promocoes.php?deleted=1");
    exit;
  }

  if ($action === 'toggle') {
    $id = (int)($_POST['id'] ?? 0);
    foreach ($promos as &$pr) {
      if ((int)$pr['id'] === $id) {
        $pr['active'] = !($pr['active'] ?? false);
        break;
      }
    }
    unset($pr);
    save_promotions($promos);
    header("Location: {$base}/admin/promocoes.php?toggled=1");
    exit;
  }

  if ($action === 'toggle_featured') {
    $id = (int)($_POST['id'] ?? 0);
    foreach ($promos as &$pr) {
      if ((int)$pr['id'] === $id) {
        $pr['featured'] = !($pr['featured'] ?? false);
        break;
      }
    }
    unset($pr);
    save_promotions($promos);
    header("Location: {$base}/admin/promocoes.php?feat=1");
    exit;
  }
}

// ============ LISTAGEM (GET) ============
$promos = load_promotions();
$products = load_products();

// ordena por id desc
usort($promos, fn($a,$b) => (int)$b['id'] <=> (int)$a['id']);

// abre modal se ?new=1
$openModal = isset($_GET['new']);
$errModal  = isset($_GET['err']);
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Promoções • Painel</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root{
    --bg:#fff7fb; --text:#3b2b33; --muted:#8b6f7b;
    --card:#ffffff; --border:#f3d6e2; --ring:#f4c6d5; --shadow:0 14px 40px rgba(181,102,138,.14);
    --grad: linear-gradient(180deg,#ff8bb6,#ff5ea2); --radius:16px;
  }
  *{box-sizing:border-box}
  body{margin:0;background:var(--bg);color:var(--text);font-family:Poppins,system-ui,Segoe UI,Roboto,Arial,sans-serif}
  header{background:#ffe0ef;padding:14px 16px;border-bottom:1px solid #f3dbe6}
  .wrap{max-width:1180px;margin:0 auto;padding:20px}
  h1{font-size:34px;margin:0 0 18px}
  .btn{border-radius:14px; padding:12px 16px; border:1px solid #f2a7c2; background:var(--grad); color:#fff; font-weight:700; cursor:pointer}
  .btn-sm{padding:8px 10px; border-radius:12px; border:1px solid #e7c7d6; background:#fff; cursor:pointer}
  .btn-danger{background:#ffe3ea;border-color:#ffc7d6;color:#b5315e}
  .btn-ghost{background:#fff;border:1px solid #e7c7d6}
  .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px}
  .table{background:var(--card);border:1px solid var(--border);border-radius:20px;box-shadow:var(--shadow);overflow:hidden}
  .thead,.row{display:grid;grid-template-columns:2fr 2fr 1.2fr 1.2fr 1.4fr 1.2fr 1.2fr 1.6fr}
  .thead{background:#fff;border-bottom:1px solid #f0d8e2;color:#6a4f5a;font-weight:700}
  .cell{padding:14px 16px;border-bottom:1px solid #f6e7ef}
  .row:last-child .cell{border-bottom:0}
  .muted{color:var(--muted)}
  .badge{display:inline-block;padding:6px 10px;border-radius:999px;border:1px solid #e7c7d6;background:#fff}
  .ok{background:#eafff1;border-color:#c9f1d7;color:#1f8a4d}
  .danger{background:#ffe3ea;border-color:#ffc7d6;color:#b5315e}
  /* Modal */
  .overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);display:none;align-items:center;justify-content:center;padding:16px}
  .modal{width:100%;max-width:720px;background:#fff;border:1px solid #f0d8e2;border-radius:20px;box-shadow:var(--shadow)}
  .modal-head{display:flex;justify-content:space-between;align-items:center;padding:16px 18px;border-bottom:1px solid #f5e4ec}
  .modal-body{padding:18px}
  .input,.select{width:100%;border:1.6px solid var(--border);border-radius:14px;padding:12px 14px;background:#fff;outline:none}
  .input:focus,.select:focus{border-color:var(--ring);box-shadow:0 0 0 4px rgba(240,143,176,.15)}
  .grid2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
  .row-actions{display:flex;gap:8px;flex-wrap:wrap}
  .foot{display:flex;gap:10px;justify-content:flex-end;padding:14px 18px;border-top:1px solid #f5e4ec}
  .overlay.show{display:flex}
</style>
</head>
<body>
<header><div class="wrap"><a href="<?= $base ?>/admin/dashboard.php" style="text-decoration:none;color:#cf2f76">← Voltar</a></div></header>
<div class="wrap">
  <div class="topbar">
    <h1>Gerenciar Promoções</h1>
    <a class="btn" href="<?= $base ?>/admin/promocoes.php?new=1">+ Nova Promoção</a>
  </div>

  <div class="table">
    <div class="thead">
      <div class="cell">Produto</div>
      <div class="cell">Nome da Promoção</div>
      <div class="cell">Preço Original</div>
      <div class="cell">Preço Promocional</div>
      <div class="cell">Período</div>
      <div class="cell">Destaque</div>
      <div class="cell">Status</div>
      <div class="cell">Ações</div>
    </div>

    <?php if (!$promos): ?>
      <div class="row"><div class="cell muted" style="grid-column:1/-1">Nenhuma promoção cadastrada.</div></div>
    <?php else: ?>
      <?php foreach ($promos as $pr): ?>
        <div class="row">
          <div class="cell"><strong><?= htmlspecialchars($pr['product_name']) ?></strong></div>
          <div class="cell"><?= htmlspecialchars($pr['title']) ?></div>
          <div class="cell">R$ <?= number_format((float)$pr['price_orig'], 2, ',', '.') ?></div>
          <div class="cell">R$ <?= number_format((float)$pr['price_promo'], 2, ',', '.') ?></div>
          <div class="cell">
            <?php
              $p1 = $pr['start'] ? date('d/m/Y', strtotime(str_replace('/','-',$pr['start']))) : '-';
              $p2 = $pr['end']   ? date('d/m/Y', strtotime(str_replace('/','-',$pr['end'])))   : '-';
              echo $p1 . ' a ' . $p2;
            ?>
          </div>
          <div class="cell">
            <?php if (!empty($pr['featured'])): ?>
              <span class="badge ok">Sim</span>
            <?php else: ?>
              <span class="badge">Não</span>
            <?php endif; ?>
          </div>
          <div class="cell">
            <?php if (!empty($pr['active'])): ?>
              <span class="badge ok">Ativa</span>
            <?php else: ?>
              <span class="badge danger">Inativa</span>
            <?php endif; ?>
          </div>
          <div class="cell">
            <div class="row-actions">
              <form method="post" action="">
                <input type="hidden" name="action" value="toggle_featured">
                <input type="hidden" name="id" value="<?= (int)$pr['id'] ?>">
                <button class="btn-sm" type="submit"><?= !empty($pr['featured']) ? 'Tirar destaque' : 'Destacar' ?></button>
              </form>
              <form method="post" action="">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= (int)$pr['id'] ?>">
                <button class="btn-sm" type="submit"><?= !empty($pr['active']) ? 'Desativar' : 'Ativar' ?></button>
              </form>
              <form method="post" action="" onsubmit="return confirm('Remover promoção?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$pr['id'] ?>">
                <button class="btn-sm btn-danger" type="submit">Excluir</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<!-- MODAL: Nova Promoção -->
<div class="overlay <?= $openModal ? 'show' : '' ?>">
  <div class="modal">
    <div class="modal-head">
      <strong>Nova Promoção</strong>
      <a href="<?= $base ?>/admin/promocoes.php" style="text-decoration:none;color:#cf2f76">✕</a>
    </div>
    <form method="post" action="">
      <input type="hidden" name="action" value="create">
      <div class="modal-body">
        <?php if ($errModal): ?>
          <div style="background:#ffe8ee;border:1px solid #ffd0dc;color:#b9315d;padding:10px 12px;border-radius:12px;margin-bottom:10px">
            Preencha os campos corretamente.
          </div>
        <?php endif; ?>

        <label>Produto</label>
        <select class="select" name="product_id" required>
          <option value="">Selecione um produto</option>
          <?php foreach ($products as $p): ?>
            <option value="<?= (int)$p['id'] ?>">
              <?= htmlspecialchars($p['name']) ?> — R$ <?= number_format((float)$p['price'],2,',','.') ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label style="margin-top:10px">Nome da Promoção</label>
        <input class="input" name="title" placeholder="Ex: Promoção de Aniversário" required>

        <label style="margin-top:10px">Preço Promocional (R$)</label>
        <input class="input" type="number" step="0.01" min="0" name="price_promo" required>

        <div class="grid2" style="margin-top:10px">
          <div>
            <label>Data de Início</label>
            <input class="input" type="date" name="start">
          </div>
          <div>
            <label>Data de Fim</label>
            <input class="input" type="date" name="end">
          </div>
        </div>

        <div style="margin-top:12px;display:flex;gap:18px;align-items:center;color:#6a4f5a">
          <label style="display:flex;gap:8px;align-items:center;cursor:pointer">
            <input type="checkbox" name="featured" style="accent-color:#ff6aa8"> Destacar na página inicial
          </label>
          <label style="display:flex;gap:8px;align-items:center;cursor:pointer">
            <input type="checkbox" name="active" checked style="accent-color:#ff6aa8"> Promoção ativa
          </label>
        </div>
      </div>
      <div class="foot">
        <a class="btn-sm btn-ghost" href="<?= $base ?>/admin/promocoes.php">Cancelar</a>
        <button class="btn" type="submit">Criar</button>
      </div>
    </form>
  </div>
</div>

<script>
  // fecha modal com ESC
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') window.location.href = '<?= $base ?>/admin/promocoes.php';
  });
</script>
</body>
</html>
