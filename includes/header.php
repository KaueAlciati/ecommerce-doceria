<?php
$totals = cart_totals();
$cartCount = $totals['count'];
$pageTitle = $pageTitle ?? 'Doce Encanto';
$activePage = $activePage ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cookie&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['Poppins', 'sans-serif'],
              cookie: ['Cookie', 'cursive'],
            },
            colors: {
              background: 'hsl(35, 40%, 98%)',
              foreground: 'hsl(330, 30%, 25%)',
              card: 'hsl(0, 0%, 100%)',
              'card-foreground': 'hsl(330, 30%, 25%)',
              popover: 'hsl(0, 0%, 100%)',
              'popover-foreground': 'hsl(330, 30%, 25%)',
              primary: 'hsl(340, 82%, 75%)',
              'primary-foreground': 'hsl(0, 0%, 100%)',
              'primary-dark': 'hsl(340, 75%, 65%)',
              secondary: 'hsl(35, 50%, 92%)',
              'secondary-foreground': 'hsl(330, 30%, 25%)',
              muted: 'hsl(340, 50%, 96%)',
              'muted-foreground': 'hsl(330, 15%, 45%)',
              accent: 'hsl(45, 80%, 75%)',
              'accent-foreground': 'hsl(330, 30%, 25%)',
              destructive: 'hsl(0, 84.2%, 60.2%)',
              'destructive-foreground': 'hsl(0, 0%, 100%)',
              border: 'hsl(340, 30%, 90%)',
              input: 'hsl(340, 30%, 90%)',
              ring: 'hsl(340, 82%, 75%)',
            },
            boxShadow: {
              soft: '0 4px 20px rgba(255, 182, 193, 0.15)',
              card: '0 8px 30px rgba(255, 182, 193, 0.2)',
              hover: '0 12px 40px rgba(255, 182, 193, 0.3)',
            },
          },
        },
      };
    </script>
    <style type="text/tailwindcss">
      @layer base {
        body { @apply bg-background text-foreground font-sans; }
        h1,h2,h3,h4,h5,h6 { @apply font-semibold; }
      }
      @layer utilities {
        .font-cookie { font-family: 'Cookie', cursive; }
        .hover-lift { @apply transition-all duration-300 hover:-translate-y-2 hover:shadow-[0_12px_40px_rgba(255,182,193,0.3)]; }
        .gradient-primary { background: linear-gradient(135deg, hsl(340, 82%, 75%) 0%, hsl(340, 75%, 65%) 100%); }
        .gradient-hero { background: linear-gradient(135deg, hsl(340, 82%, 85%) 0%, hsl(340, 75%, 75%) 50%, hsl(45, 80%, 80%) 100%); }
      }
    </style>
  </head>
  <body class="min-h-screen flex flex-col">
    <nav class="sticky top-0 z-50 bg-card/95 backdrop-blur-sm border-b border-border shadow-sm">
      <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
          <!-- logo -->
          <a href="index.php" class="flex items-center gap-2">
            <span class="text-2xl md:text-3xl font-cookie text-primary">Doce Encanto</span>
          </a>

          <!-- menu desktop -->
          <div class="hidden md:flex items-center gap-6">
            <a href="index.php" class="transition-colors font-medium <?= $activePage === 'index' ? 'text-primary' : 'text-foreground hover:text-primary' ?>">Início</a>
            <a href="products.php" class="transition-colors font-medium <?= $activePage === 'products' ? 'text-primary' : 'text-foreground hover:text-primary' ?>">Produtos</a>
            <a href="cart.php" class="transition-colors font-medium <?= $activePage === 'cart' ? 'text-primary' : 'text-foreground hover:text-primary' ?>">Carrinho</a>
          </div>

          <!-- ações (carrinho + login + menu mobile) -->
          <div class="flex items-center gap-3 md:gap-4">
            <!-- carrinho -->
            <a href="cart.php" class="relative inline-flex h-10 w-10 items-center justify-center rounded-full border border-border text-foreground transition-colors hover:text-primary" aria-label="Carrinho">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l1.6-8H5.4M7 13L5.4 5M7 13l-2 9m12-9l2 9m-6 0a1 1 0 11-2 0m2 0a1 1 0 11-2 0" />
              </svg>
              <?php if ($cartCount > 0): ?>
                <span class="absolute -top-2 -right-2 bg-primary text-primary-foreground text-xs w-5 h-5 rounded-full flex items-center justify-center font-semibold"><?= $cartCount ?></span>
              <?php endif; ?>
            </a>

            <!-- login -->
            <a href="admin/login.php" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-border text-foreground transition-colors hover:text-primary" title="Entrar" aria-label="Entrar">
              <!-- ícone usuário -->
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 1115 0v.75H4.5v-.75z" />
              </svg>
            </a>

            <!-- menu mobile -->
            <button id="mobile-menu-button" class="md:hidden inline-flex h-10 w-10 items-center justify-center rounded-full border border-border text-foreground transition-colors hover:text-primary" aria-label="Menu">
              <svg id="mobile-menu-icon-open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
              <svg id="mobile-menu-icon-close" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- menu mobile -->
        <div id="mobile-menu" class="md:hidden py-4 border-t border-border hidden">
          <div class="flex flex-col gap-4">
            <a href="index.php" class="transition-colors font-medium <?= $activePage === 'index' ? 'text-primary' : 'text-foreground hover:text-primary' ?>">Início</a>
            <a href="products.php" class="transition-colors font-medium <?= $activePage === 'products' ? 'text-primary' : 'text-foreground hover:text-primary' ?>">Produtos</a>
            <a href="cart.php" class="transition-colors font-medium <?= $activePage === 'cart' ? 'text-primary' : 'text-foreground hover:text-primary' ?>">Carrinho</a>
            <a href="admin/login.php" class="transition-colors font-medium text-foreground hover:text-primary">Entrar</a>
          </div>
        </div>
      </div>
    </nav>

    <script>
      // abre/fecha menu mobile
      const btn = document.getElementById('mobile-menu-button');
      const menu = document.getElementById('mobile-menu');
      const icoOpen = document.getElementById('mobile-menu-icon-open');
      const icoClose = document.getElementById('mobile-menu-icon-close');

      if (btn) {
        btn.addEventListener('click', () => {
          const show = menu.classList.contains('hidden');
          menu.classList.toggle('hidden', !show);
          icoOpen.classList.toggle('hidden', show);
          icoClose.classList.toggle('hidden', !show);
        });
      }
    </script>

    <main class="flex-1">
