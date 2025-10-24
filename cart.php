<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

/* =========================
   AÇÕES DO CARRINHO (POST)
   ========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action    = $_POST['action']     ?? '';
  $productId = (int)($_POST['product_id'] ?? 0);

  /* ---- ADICIONAR ITEM ---- */
  if ($action === 'add' && $productId > 0) {
    $qty = max(1, (int)($_POST['qty'] ?? 1));

    if (function_exists('add_to_cart')) {
      // se você criou um helper que já busca no banco e injeta na sessão
      add_to_cart($productId, $qty);
    } else {
      // Fallback manual: busca do banco e injeta na sessão
      $st = $pdo->prepare("
        SELECT 
          id_produto AS id,
          nome,
          preco,
          imagem
        FROM produtos
        WHERE id_produto = ?
          AND (disponivel = 1 OR disponivel IS NULL)
        LIMIT 1
      ");
      $st->execute([$productId]);
      $p = $st->fetch(PDO::FETCH_ASSOC);

      if ($p) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

        if (isset($_SESSION['cart'][$productId])) {
          $_SESSION['cart'][$productId]['quantity'] += $qty;
        } else {
          $_SESSION['cart'][$productId] = [
            'product' => [
              'id'    => (int)$p['id'],
              'name'  => $p['nome'],
              'price' => (float)$p['preco'],
              'image' => $p['imagem'] ?: 'assets/product-cupcakes.jpg',
            ],
            'quantity' => $qty,
          ];
        }
      }
    }

    header('Location: cart.php');
    exit;
  }

  /* ---- ATUALIZAR QUANTIDADE ---- */
  if ($action === 'update_quantity' && $productId > 0) {
    $quantity = max(0, (int)($_POST['quantity'] ?? 0));
    update_quantity($productId, $quantity);
    header('Location: cart.php');
    exit;
  }

  /* ---- REMOVER ITEM ---- */
  if ($action === 'remove_item' && $productId > 0) {
    remove_from_cart($productId);
    header('Location: cart.php');
    exit;
  }

  /* ---- LIMPAR CARRINHO ---- */
  if ($action === 'clear_cart') {
    clear_cart();
    header('Location: cart.php');
    exit;
  }
}

/* =========================
   EXIBIÇÃO
   ========================= */
$cart   = get_cart();
$totals = cart_totals();

$pageTitle  = 'Carrinho - Doce Encanto';
$activePage = 'cart';

include __DIR__ . '/includes/header.php';
?>

<?php if (count($cart) === 0): ?>
  <div class="container mx-auto px-4 py-20">
    <div class="max-w-md mx-auto text-center p-12 bg-card rounded-3xl shadow-card">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-muted-foreground mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l1.6-8H5.4M7 13L5.4 5M7 13l-2 9m12-9l2 9m-6 0a1 1 0 11-2 0m2 0a1 1 0 11-2 0" />
      </svg>
      <h2 class="text-2xl font-bold mb-2">Seu carrinho está vazio</h2>
      <p class="text-muted-foreground mb-6">
        Adicione alguns doces deliciosos ao seu carrinho!
      </p>
      <a href="products.php" class="inline-flex items-center justify-center rounded-full gradient-primary hover:opacity-90 text-primary-foreground px-6 py-3 font-medium transition">
        Ver Produtos
      </a>
    </div>
  </div>
<?php else: ?>
  <div class="container mx-auto px-4 py-12">
    <h1 class="text-4xl font-bold mb-8">
      Seu <span class="text-primary font-cookie text-5xl">Carrinho</span>
    </h1>
    <div class="grid lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2 space-y-4">
        <?php foreach ($cart as $item): ?>
          <?php $product = $item['product']; ?>
          <div class="bg-card rounded-3xl shadow-card overflow-hidden">
            <div class="p-4">
              <div class="flex gap-4">
                <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-24 h-24 object-cover rounded-lg" />
                <div class="flex-1">
                  <h3 class="font-semibold text-lg text-foreground mb-1"><?= htmlspecialchars($product['name']) ?></h3>
                  <p class="text-primary font-bold mb-2"><?= format_currency($product['price']) ?></p>
                  <div class="flex items-center gap-3 mt-3">
                    <div class="flex items-center gap-2">
                      <form method="post">
                        <input type="hidden" name="action" value="update_quantity" />
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>" />
                        <input type="hidden" name="quantity" value="<?= max(0, $item['quantity'] - 1) ?>" />
                        <button type="submit" class="h-9 w-9 inline-flex items-center justify-center rounded-full border border-border text-foreground hover:text-primary transition">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
                          </svg>
                        </button>
                      </form>
                      <span class="w-12 text-center font-semibold"><?= $item['quantity'] ?></span>
                      <form method="post">
                        <input type="hidden" name="action" value="update_quantity" />
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>" />
                        <input type="hidden" name="quantity" value="<?= $item['quantity'] + 1 ?>" />
                        <button type="submit" class="h-9 w-9 inline-flex items-center justify-center rounded-full border border-border text-foreground hover:text-primary transition">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                          </svg>
                        </button>
                      </form>
                    </div>
                    <form method="post" class="ml-auto">
                      <input type="hidden" name="action" value="remove_item" />
                      <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>" />
                      <button type="submit" class="h-9 w-9 inline-flex items-center justify-center rounded-full text-destructive hover:bg-destructive/10 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12M10 11v6m4-6v6m-9 4h14a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="lg:col-span-1">
        <div class="bg-card rounded-3xl shadow-card p-6 space-y-4 sticky top-24">
          <h3 class="text-xl font-bold">Resumo do Pedido</h3>
          <div class="space-y-2 text-sm">
            <?php foreach ($cart as $item): ?>
              <?php $product = $item['product']; ?>
              <div class="flex justify-between text-muted-foreground">
                <span><?= htmlspecialchars($product['name']) ?> x<?= $item['quantity'] ?></span>
                <span><?= format_currency($product['price'] * $item['quantity']) ?></span>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="border-t border-border pt-4">
            <div class="flex justify-between items-center mb-6">
              <span class="text-xl font-bold">Total</span>
              <span class="text-2xl font-bold text-primary"><?= format_currency($totals['total']) ?></span>
            </div>
            <a href="checkout.php" class="w-full block text-center gradient-primary hover:opacity-90 text-primary-foreground text-lg py-4 rounded-full font-semibold transition">
              Finalizar Pedido
            </a>
            <form method="post" class="pt-4 text-center">
              <input type="hidden" name="action" value="clear_cart" />
              <button type="submit" class="text-sm text-muted-foreground hover:text-destructive transition">
                Esvaziar carrinho
              </button>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
