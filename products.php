<?php
// products.php
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/db_connect.php';

$pageTitle  = 'Produtos - Doce Encanto';
$activePage = 'products';

// categoria vinda do filtro
$cat = trim($_GET['cat'] ?? '');

// 1) Carrega categorias distintas
$cats = $pdo->query("
  SELECT DISTINCT categoria
  FROM produtos
  WHERE categoria IS NOT NULL AND categoria <> ''
  ORDER BY categoria
")->fetchAll(PDO::FETCH_COLUMN);

// 2) Carrega produtos (só os disponíveis)
$params = [];
$where  = 'WHERE (disponivel = 1 OR disponivel IS NULL)';
if ($cat !== '') {
  $where .= ' AND categoria = ?';
  $params[] = $cat;
}

$sql = "
  SELECT id_produto, nome, descricao, preco, imagem, categoria, disponivel, data_cadastro
  FROM produtos
  $where
  ORDER BY COALESCE(data_cadastro, NOW()) DESC, id_produto DESC
";
$st = $pdo->prepare($sql);
$st->execute($params);
$products = $st->fetchAll();

// utilitário de moeda (fallback se não existir)
if (!function_exists('format_currency')) {
  function format_currency($v) {
    return 'R$ ' . number_format((float)$v, 2, ',', '.');
  }
}

include __DIR__ . '/includes/header.php';
?>

<div class="container mx-auto px-4 py-12">
  <!-- Título -->
  <div class="text-center mb-10">
    <h1 class="text-4xl md:text-5xl font-bold">
      Nossos <span class="font-cookie text-primary text-5xl">Produtos</span>
    </h1>
    <p class="text-muted-foreground mt-3">
      Explore nossa deliciosa seleção de doces artesanais
    </p>
  </div>

  <!-- Filtros -->
  <div class="flex flex-wrap gap-3 justify-center mb-10">
    <?php
      // helper pra marcar ativo
      $isActive = fn($c) => ($c==='' && $cat==='') || ($c!=='' && $cat===$c);
      $pill = function($label, $href, $active) {
        $baseClass = 'px-4 py-2 rounded-full border transition font-medium';
        $off = 'border-border text-foreground hover:text-primary';
        $on  = 'border-transparent text-primary-foreground';
        $bg  = $active
          ? " {$on} " . 'gradient-primary'
          : " {$off} bg-card";
        echo "<a href=\"{$href}\" class=\"{$baseClass}{$bg}\">{$label}</a>";
      };

      // "Todos"
      $pill('Todos', 'products.php', $isActive(''));

      // demais categorias do banco
      foreach ($cats as $c) {
        $pill(htmlspecialchars($c), 'products.php?cat=' . urlencode($c), $isActive($c));
      }
    ?>
  </div>

  <!-- Lista de produtos -->
  <?php if (!$products): ?>
    <div class="max-w-2xl mx-auto bg-card border border-border rounded-3xl p-10 text-center">
      <p class="text-muted-foreground">Nenhum produto encontrado<?= $cat ? " na categoria <strong>" . htmlspecialchars($cat) . "</strong>" : '' ?>.</p>
    </div>
  <?php else: ?>
    <div class="grid gap-8 md:gap-10 grid-cols-1 md:grid-cols-2 xl:grid-cols-3">
      <?php foreach ($products as $p): ?>
        <?php
          $img  = $p['imagem'] ?: 'assets/product-cupcakes.jpg'; // placeholder se não tiver
          $nome = $p['nome'];
          $desc = $p['descricao'] ?: '';
          $preco = $p['preco'];
          $categoria = $p['categoria'] ?: 'Outros';
        ?>
        <div class="bg-card border border-border rounded-3xl overflow-hidden shadow-card hover:shadow-[0_12px_40px_rgba(255,182,193,0.3)] transition">
          <div class="aspect-[4/3] overflow-hidden">
            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($nome) ?>" class="w-full h-full object-cover">
          </div>
          <div class="p-6 space-y-3">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold"><?= htmlspecialchars($nome) ?></h3>
              <span class="text-sm px-3 py-1 rounded-full bg-muted text-muted-foreground"><?= htmlspecialchars($categoria) ?></span>
            </div>
            <?php if ($desc): ?>
              <p class="text-sm text-muted-foreground line-clamp-2"><?= htmlspecialchars($desc) ?></p>
            <?php endif; ?>
            <div class="flex items-center justify-between pt-2">
              <div class="text-xl font-bold text-primary"><?= format_currency($preco) ?></div>

              <!-- Botão (exemplo simples) -->
              <form method="post" action="cart.php" class="m-0">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?= (int)$p['id_produto'] ?>">
                <input type="hidden" name="qty" value="1">
                <button type="submit"
                        class="rounded-full px-4 py-2 font-semibold text-primary-foreground gradient-primary hover:opacity-90 transition">
                  Adicionar
                </button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
