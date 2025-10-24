<?php
require_once __DIR__ . '/includes/functions.php';

// Carrinho
$cart = get_cart();
if (count($cart) === 0) {
    header('Location: cart.php');
    exit;
}

// Mantém itens do pedido em memória mesmo após limpar o carrinho
$cartItems = $cart;
$totals    = cart_totals();

// Dados do formulário (pré)
$formData = [
    'name'           => '',
    'email'          => '',
    'phone'          => '',
    'address'        => '',
    'payment_method' => 'pix',
];

$orderConfirmed = false;
$errorMessage   = '';
$orderSubtotal  = (float)$totals['total'];
$discountValue  = 0.0;
$finalTotal     = $orderSubtotal;

// Para usar no sucesso
$orderId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['name']           = trim($_POST['name'] ?? '');
    $formData['email']          = trim($_POST['email'] ?? '');
    $formData['phone']          = trim($_POST['phone'] ?? '');
    $formData['address']        = trim($_POST['address'] ?? '');
    $formData['payment_method'] = $_POST['payment_method'] ?? 'pix';

    if (!$formData['name'] || !$formData['email'] || !$formData['phone'] || !$formData['address']) {
        $errorMessage = 'Por favor, preencha todos os campos obrigatórios.';
    } else {
        // Desconto PIX (5%)
        if ($formData['payment_method'] === 'pix') {
            $discountValue = $orderSubtotal * 0.05;
        }
        $finalTotal = $orderSubtotal - $discountValue;

        // ===== Registrar pedido (lido por Renda) =====
        require_once __DIR__ . '/admin/store.php';

        $items = [];
        foreach ($cartItems as $item) {
            $p = $item['product'];
            $items[] = [
                'product_id' => (int)($p['id'] ?? 0),
                'name'       => (string)$p['name'],
                'qty'        => (int)$item['quantity'],
                'unit_price' => (float)$p['price'],
            ];
        }

        $shippingValue = 0.0;
        $customer      = $formData['name'];

        // Pega ID do pedido retornado
        $orderId = add_order($items, $shippingValue, $customer);

        // Limpa carrinho e mostra recibo
        clear_cart();
        $orderConfirmed = true;
    }
}

$pageTitle  = 'Checkout - Doce Encanto';
$activePage = '';

include __DIR__ . '/includes/header.php';

// valores para o resumo dinâmico (lado do formulário)
$isPix        = ($formData['payment_method'] ?? 'pix') === 'pix';
$discountPix  = $orderSubtotal * 0.05;
$initialTotal = $isPix ? ($orderSubtotal - $discountPix) : $orderSubtotal;
?>
<div class="container mx-auto px-4 py-12">
  <?php if ($orderConfirmed): ?>
    <div class="max-w-3xl mx-auto bg-card rounded-3xl shadow-card p-10 text-center space-y-6">
      <h1 class="text-4xl font-bold text-primary">Pedido realizado com sucesso! 🎉</h1>
      <p class="text-muted-foreground text-lg">
        Em breve você receberá a confirmação por e-mail. Obrigado por escolher a Doce Encanto!
      </p>

      <div class="bg-muted/40 rounded-2xl p-6 text-left space-y-3">
        <h2 class="text-2xl font-semibold">Resumo do Pedido</h2>

        <?php foreach ($cartItems as $item): ?>
          <?php $product = $item['product']; ?>
          <div class="flex justify-between text-muted-foreground">
            <span><?= htmlspecialchars($product['name']) ?> x<?= (int)$item['quantity'] ?></span>
            <span><?= format_currency($product['price'] * $item['quantity']) ?></span>
          </div>
        <?php endforeach; ?>

        <div class="border-t border-border pt-4 space-y-1">
          <div class="flex justify-between">
            <span>Subtotal</span>
            <span><?= format_currency($orderSubtotal) ?></span>
          </div>

          <?php if ($discountValue > 0): ?>
            <div class="flex justify-between text-green-600">
              <span>Desconto PIX (5%)</span>
              <span>- <?= format_currency($discountValue) ?></span>
            </div>
          <?php endif; ?>

          <div class="flex justify-between items-center text-xl font-bold">
            <span>Total</span>
            <span class="text-primary"><?= format_currency($finalTotal) ?></span>
          </div>
        </div>
      </div>

      <?php
        // === TEXTO PARA O TELEGRAM (formato do seu print) ===
        $linhas = [];
        foreach ($cartItems as $it) {
          $p = $it['product'];
          $linhas[] = "{$it['quantity']}x {$p['name']} — " . format_currency($p['price'] * $it['quantity']);
        }
        $itensTxt = implode("\n", $linhas);

        $forma = ($formData['payment_method']==='pix')
                 ? 'PIX (5% off)'
                 : (($formData['payment_method']==='card') ? 'Cartão de Crédito' : 'Dinheiro na Entrega');

        $tgText =
          "🍰 *Doce Encanto informa:*\n".
          "Seu pedido foi *confirmado* com sucesso! 🎉\n".
          "Agradecemos a preferência, *{$formData['name']}* 💖\n\n".
          "*Pedido:* #".($orderId ?? '—')."\n".
          "*Itens:*\n{$itensTxt}\n\n".
          "*Pagamento:* {$forma}\n".
          "*Total:* ".format_currency($finalTotal)."\n\n".
          "_Endereço:_ {$formData['address']}";
      ?>

      <!-- Envia a confirmação pelo Telegram -->
      <form method="post" action="notify_telegram.php" class="inline-block">
        <input type="hidden" name="text" value="<?= htmlspecialchars($tgText) ?>">
        <!-- Se você quiser enviar para um chat específico do cliente, inclua chat_id aqui:
        <input type="hidden" name="chat_id" value="">
        -->
        <input type="hidden" name="back" value="checkout.php">
        <button type="submit"
                class="inline-flex items-center justify-center rounded-full border border-border px-8 py-3 text-lg font-semibold hover:text-primary transition">
          Receber confirmação no Telegram
        </button>
      </form>

      <a href="index.php" class="inline-flex items-center justify-center rounded-full gradient-primary hover:opacity-90 text-primary-foreground px-8 py-3 text-lg font-semibold transition">
        Voltar para a página inicial
      </a>
    </div>

  <?php else: ?>
    <h1 class="text-4xl font-bold mb-8">
      <span class="text-primary font-cookie text-5xl">Finalizar</span> Pedido
    </h1>

    <?php if ($errorMessage): ?>
      <div class="mb-6 rounded-2xl border border-destructive/30 bg-destructive/10 text-destructive px-4 py-3">
        <?= htmlspecialchars($errorMessage) ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <div class="grid lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
          <div class="bg-card rounded-3xl shadow-card">
            <div class="border-b border-border px-6 py-4">
              <h2 class="text-xl font-semibold">Dados Pessoais</h2>
            </div>
            <div class="p-6 space-y-4">
              <div>
                <label for="name" class="block text-sm font-medium text-foreground mb-1">Nome Completo</label>
                <input id="name" name="name" type="text" required value="<?= htmlspecialchars($formData['name']) ?>" placeholder="Seu nome" class="w-full rounded-xl border border-border px-4 py-3 bg-card focus:outline-none focus:ring-2 focus:ring-ring" />
              </div>
              <div>
                <label for="email" class="block text-sm font-medium text-foreground mb-1">E-mail</label>
                <input id="email" name="email" type="email" required value="<?= htmlspecialchars($formData['email']) ?>" placeholder="seu@email.com" class="w-full rounded-xl border border-border px-4 py-3 bg-card focus:outline-none focus:ring-2 focus:ring-ring" />
              </div>
              <div>
                <label for="phone" class="block text-sm font-medium text-foreground mb-1">Telefone</label>
                <input id="phone" name="phone" type="text" required value="<?= htmlspecialchars($formData['phone']) ?>" placeholder="(11) 99999-9999" class="w-full rounded-xl border border-border px-4 py-3 bg-card focus:outline-none focus:ring-2 focus:ring-ring" />
              </div>
              <div>
                <label for="address" class="block text-sm font-medium text-foreground mb-1">Endereço de Entrega</label>
                <input id="address" name="address" type="text" required value="<?= htmlspecialchars($formData['address']) ?>" placeholder="Rua, número, bairro, cidade" class="w-full rounded-xl border border-border px-4 py-3 bg-card focus:outline-none focus:ring-2 focus:ring-ring" />
              </div>
            </div>
          </div>

          <div class="bg-card rounded-3xl shadow-card">
            <div class="border-b border-border px-6 py-4">
              <h2 class="text-xl font-semibold">Forma de Pagamento</h2>
            </div>
            <div class="p-6 space-y-3">
              <?php
                $paymentOptions = [
                  'pix'  => 'PIX (Desconto de 5%)',
                  'card' => 'Cartão de Crédito',
                  'cash' => 'Dinheiro na Entrega',
                ];
              ?>
              <?php foreach ($paymentOptions as $value => $label): ?>
                <label class="flex items-center gap-3 p-4 border border-border rounded-2xl hover:bg-muted/50 cursor-pointer">
                  <input
                    type="radio"
                    name="payment_method"
                    value="<?= $value ?>"
                    class="h-4 w-4 text-primary focus:ring-primary js-pay"
                    <?= $formData['payment_method'] === $value ? 'checked' : '' ?>
                  />
                  <span class="font-medium text-foreground"><?= $label ?></span>
                </label>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <div class="lg:col-span-1">
          <div class="bg-card rounded-3xl shadow-card sticky top-24">
            <div class="border-b border-border px-6 py-4">
              <h2 class="text-xl font-semibold">Resumo do Pedido</h2>
            </div>
            <div class="p-6 space-y-4">
              <div class="space-y-2">
                <?php foreach ($cartItems as $item): ?>
                  <?php $product = $item['product']; ?>
                  <div class="flex justify-between text-sm text-muted-foreground">
                    <span><?= htmlspecialchars($product['name']) ?> x<?= (int)$item['quantity'] ?></span>
                    <span><?= format_currency($product['price'] * $item['quantity']) ?></span>
                  </div>
                <?php endforeach; ?>
              </div>

              <?php
                $subtotalData = number_format($orderSubtotal, 2, '.', '');
                $discountData = number_format($discountPix,   2, '.', '');
              ?>

              <div class="border-t border-border pt-4 space-y-2">
                <div class="flex justify-between">
                  <span>Subtotal</span>
                  <span id="subtotalValue" data-subtotal="<?= htmlspecialchars($subtotalData) ?>">
                    <?= format_currency($orderSubtotal) ?>
                  </span>
                </div>

                <div
                  id="discountRow"
                  class="flex justify-between text-green-600 <?= $isPix ? '' : 'hidden' ?>"
                  data-discount="<?= htmlspecialchars($discountData) ?>"
                >
                  <span>Desconto PIX (5%)</span>
                  <span id="discountValue">- <?= format_currency($discountPix) ?></span>
                </div>

                <div class="flex justify-between items-center pt-2 border-t border-border">
                  <span class="text-xl font-bold">Total</span>
                  <span class="text-2xl font-bold text-primary" id="totalValue">
                    <?= format_currency($initialTotal) ?>
                  </span>
                </div>
              </div>

              <button type="submit" class="w-full gradient-primary hover:opacity-90 text-primary-foreground text-lg py-6 rounded-full font-semibold transition">
                Confirmar Pedido
              </button>
            </div>
          </div>
        </div>
      </div>
    </form>
  <?php endif; ?>
</div>

<!-- Atualiza desconto/total ao trocar forma de pagamento -->
<script>
(function () {
  const radios       = document.querySelectorAll('input.js-pay[name="payment_method"]');
  const subtotalEl   = document.getElementById('subtotalValue');
  const discountRow  = document.getElementById('discountRow');
  const discountEl   = document.getElementById('discountValue');
  const totalEl      = document.getElementById('totalValue');

  if (!radios.length || !subtotalEl || !discountRow || !discountEl || !totalEl) return;

  const parseMoney = (v) => Number(String(v).replace(',', '.'));
  const formatBRL  = (n) => n.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

  const baseSubtotal = parseMoney(subtotalEl.dataset.subtotal);
  const baseDiscount = parseMoney(discountRow.dataset.discount);

  function updateSummary(method) {
    if (method === 'pix') {
      discountRow.classList.remove('hidden');
      discountEl.textContent = '- ' + formatBRL(baseDiscount);
      totalEl.textContent    = formatBRL(baseSubtotal - baseDiscount);
    } else {
      discountRow.classList.add('hidden');
      totalEl.textContent = formatBRL(baseSubtotal);
    }
  }

  const checked = document.querySelector('input.js-pay[name="payment_method"]:checked');
  updateSummary(checked ? checked.value : 'pix');

  radios.forEach(r => r.addEventListener('change', () => updateSummary(r.value)));
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
