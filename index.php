<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Doce Encanto - Doces Artesanais';
$activePage = 'index';
$featuredProducts = array_slice($products, 0, 3);

include __DIR__ . '/includes/header.php';
?>
<section class="relative overflow-hidden">
  <div class="absolute inset-0 gradient-hero opacity-30"></div>
  <div class="container mx-auto px-4 py-20 md:py-32 relative z-10">
    <div class="grid md:grid-cols-2 gap-12 items-center">
      <div class="space-y-6">
        <h1 class="text-5xl md:text-6xl font-bold leading-tight">
          Doces Artesanais
          <span class="block text-primary font-cookie text-6xl md:text-7xl mt-2">Feitos com Amor</span>
        </h1>
        <p class="text-lg text-muted-foreground">
          Transforme seus momentos especiais em memórias deliciosas com nossos doces artesanais.
        </p>
        <div class="flex gap-4">
          <a href="products.php" class="inline-flex items-center justify-center rounded-full gradient-primary hover:opacity-90 text-lg px-8 py-3 text-primary-foreground transition">
            Ver Produtos
          </a>
          <a href="cart.php" class="inline-flex items-center justify-center rounded-full border border-border text-lg px-8 py-3 hover:text-primary transition">
            Carrinho
          </a>
        </div>
      </div>
      <div class="relative">
        <img src="assets/hero-sweets.jpg" alt="Doces artesanais" class="rounded-3xl shadow-2xl hover-lift w-full" />
      </div>
    </div>
  </div>
</section>

<section class="container mx-auto px-4 py-20">
  <div class="text-center mb-12">
    <h2 class="text-4xl font-bold mb-4">
      Nossos <span class="text-primary font-cookie text-5xl">Destaques</span>
    </h2>
    <p class="text-muted-foreground max-w-2xl mx-auto">
      Conheça alguns dos nossos produtos mais queridos pelos clientes
    </p>
  </div>
  <div class="grid md:grid-cols-3 gap-8 mb-8">
    <?php foreach ($featuredProducts as $product): ?>
      <div class="bg-card rounded-3xl shadow-card hover-lift overflow-hidden flex flex-col">
        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="h-56 w-full object-cover" />
        <div class="p-6 space-y-3 flex-1 flex flex-col">
          <div class="flex items-center justify-between">
            <span class="px-3 py-1 rounded-full bg-primary/15 text-primary text-sm font-medium capitalize">
              <?= htmlspecialchars($product['category']) ?>
            </span>
            <span class="text-lg font-semibold text-foreground"><?= format_currency($product['price']) ?></span>
          </div>
          <h3 class="text-2xl font-semibold text-foreground"><?= htmlspecialchars($product['name']) ?></h3>
          <p class="text-muted-foreground flex-1">
            <?= htmlspecialchars($product['description']) ?>
          </p>
          <form method="post" action="products.php" class="pt-4">
            <input type="hidden" name="action" value="add_to_cart" />
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>" />
            <button type="submit" class="w-full inline-flex items-center justify-center rounded-full gradient-primary text-primary-foreground py-3 font-medium transition hover:opacity-90">
              Adicionar ao Carrinho
            </button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="text-center">
    <a href="products.php" class="inline-flex items-center justify-center rounded-full border border-border px-6 py-3 font-medium transition hover:text-primary">
      Ver Todos os Produtos
    </a>
  </div>
</section>

<section class="bg-muted/30 py-20">
  <div class="container mx-auto px-4">
    <div class="grid md:grid-cols-2 gap-12 items-center">
      <div class="space-y-6">
        <h2 class="text-4xl font-bold">
          Sobre a <span class="text-primary font-cookie text-5xl">Doce Encanto</span>
        </h2>
        <p class="text-muted-foreground leading-relaxed">
          Há mais de 10 anos criando doces artesanais que encantam e conquistam corações. Nossa missão é transformar ingredientes selecionados em verdadeiras obras de arte comestíveis.
        </p>
        <p class="text-muted-foreground leading-relaxed">
          Cada produto é feito com carinho, atenção aos detalhes e os melhores ingredientes, garantindo sabor incomparável e apresentação impecável.
        </p>
        <div class="flex gap-6 pt-4">
          <div class="text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4.97-4.043-8-7.358-8-10.5a5 5 0 1110 0 5 5 0 0110 0c0 3.142-3.03 6.457-8 10.5z" />
            </svg>
            <p class="font-semibold">Feito com Amor</p>
          </div>
          <div class="text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904l-1.43 4.29a.75.75 0 001.086.868l3.39-1.97a.75.75 0 01.72 0l3.39 1.97a.75.75 0 001.086-.868l-1.43-4.29a.75.75 0 01.243-.823l3.357-3.356a.75.75 0 00-.411-1.28l-4.637-.674a.75.75 0 01-.564-.41l-2.072-4.2a.75.75 0 00-1.346 0l-2.072 4.2a.75.75 0 01-.564.41l-4.637.674a.75.75 0 00-.411 1.28l3.357 3.356a.75.75 0 01.243.823z" />
            </svg>
            <p class="font-semibold">Ingredientes Premium</p>
          </div>
          <div class="text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 17l-3.09 1.623a.75.75 0 01-1.09-.79l.59-3.44-2.5-2.438a.75.75 0 01.416-1.28l3.454-.502 1.545-3.132a.75.75 0 011.344 0l1.545 3.132 3.454.503a.75.75 0 01.416 1.279l-2.5 2.438.59 3.44a.75.75 0 01-1.09.79L12 17z" />
            </svg>
            <p class="font-semibold">Qualidade Garantida</p>
          </div>
        </div>
      </div>
      <div class="relative h-96 rounded-3xl overflow-hidden shadow-2xl hover-lift">
        <img src="assets/hero-sweets.jpg" alt="Nossa doceria" class="w-full h-full object-cover" />
      </div>
    </div>
  </div>
</section>

<section class="container mx-auto px-4 py-20">
  <div class="text-center mb-12">
    <h2 class="text-4xl font-bold mb-4">
      O Que Dizem <span class="text-primary font-cookie text-5xl">Nossos Clientes</span>
    </h2>
  </div>
  <div class="grid md:grid-cols-3 gap-8">
    <?php foreach ($testimonials as $testimonial): ?>
      <div class="bg-gradient-to-b from-card to-muted/20 rounded-3xl shadow-soft hover-lift">
        <div class="p-6 space-y-4">
          <div class="flex gap-1">
            <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-accent text-accent" viewBox="0 0 24 24">
                <path d="M12 17.27L18.18 21 16.54 13.97 22 9.24l-7.19-.62L12 2 9.19 8.62 2 9.24l5.46 4.73L5.82 21z" />
              </svg>
            <?php endfor; ?>
          </div>
          <p class="text-muted-foreground italic">&ldquo;<?= htmlspecialchars($testimonial['text']) ?>&rdquo;</p>
          <p class="font-semibold text-primary">— <?= htmlspecialchars($testimonial['name']) ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>