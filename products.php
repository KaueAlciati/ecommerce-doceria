<?php
require_once __DIR__ . '/includes/functions.php';

$selectedCategory = $_GET['categoria'] ?? 'todos';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedCategory = $_POST['selected_category'] ?? $selectedCategory;
    $action = $_POST['action'] ?? '';
    $productId = $_POST['product_id'] ?? '';

    if ($action === 'add_to_cart' && $productId !== '') {
        add_to_cart($productId);
    }

    $params = [];
    if ($selectedCategory !== 'todos') {
        $params['categoria'] = $selectedCategory;
    }

    $query = $params ? ('?' . http_build_query($params)) : '';
    header('Location: products.php' . $query);
    exit;
}

$filteredProducts = $selectedCategory === 'todos'
    ? $products
    : array_values(array_filter($products, fn($product) => $product['category'] === $selectedCategory));

$pageTitle = 'Produtos - Doce Encanto';
$activePage = 'products';

include __DIR__ . '/includes/header.php';
?>
<div class="container mx-auto px-4 py-12">
  <div class="text-center mb-12">
    <h1 class="text-4xl md:text-5xl font-bold mb-4">
      Nossos <span class="text-primary font-cookie text-5xl md:text-6xl">Produtos</span>
    </h1>
    <p class="text-muted-foreground max-w-2xl mx-auto">
      Explore nossa deliciosa seleção de doces artesanais
    </p>
  </div>

  <div class="flex flex-wrap justify-center gap-3 mb-12">
    <?php foreach ($categories as $category): ?>
      <?php $isActive = $selectedCategory === $category['id']; ?>
      <a
        href="<?= $category['id'] === 'todos' ? 'products.php' : 'products.php?categoria=' . urlencode($category['id']) ?>"
        class="px-5 py-2 rounded-full border border-border font-medium transition <?= $isActive ? 'gradient-primary text-primary-foreground hover:opacity-90' : 'text-foreground hover:text-primary' ?>"
      >
        <?= htmlspecialchars($category['name']) ?>
      </a>
    <?php endforeach; ?>
  </div>

  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php foreach ($filteredProducts as $product): ?>
      <div class="hover-lift overflow-hidden group bg-gradient-to-b from-card to-muted/20 rounded-3xl shadow-card flex flex-col">
        <div class="aspect-square overflow-hidden bg-muted">
          <img
            src="<?= htmlspecialchars($product['image']) ?>"
            alt="<?= htmlspecialchars($product['name']) ?>"
            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
          />
        </div>
        <div class="p-4 flex-1 flex flex-col">
          <h3 class="font-semibold text-lg mb-1 text-foreground"><?= htmlspecialchars($product['name']) ?></h3>
          <p class="text-sm text-muted-foreground mb-2 flex-1"><?= htmlspecialchars($product['description']) ?></p>
          <p class="text-2xl font-bold text-primary mb-4">
            <?= format_currency($product['price']) ?>
          </p>
          <form method="post" class="mt-auto">
            <input type="hidden" name="action" value="add_to_cart" />
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>" />
            <input type="hidden" name="selected_category" value="<?= htmlspecialchars($selectedCategory) ?>" />
            <button type="submit" class="w-full gap-2 gradient-primary hover:opacity-90 inline-flex items-center justify-center rounded-full py-3 text-primary-foreground font-medium transition">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l1.6-8H5.4M7 13L5.4 5M7 13l-2 9m12-9l2 9m-6 0a1 1 0 11-2 0m2 0a1 1 0 11-2 0" />
              </svg>
              Adicionar
            </button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <?php if (count($filteredProducts) === 0): ?>
    <div class="text-center py-20">
      <p class="text-muted-foreground text-lg">
        Nenhum produto encontrado nesta categoria.
      </p>
    </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>