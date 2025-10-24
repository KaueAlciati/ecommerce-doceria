<?php
/**
 * Helpers de carrinho + utilidades
 * (versão com banco de dados)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// conexão com o banco (usa $pdo)
require_once __DIR__ . '/db_connect.php';

/* =========================
   PRODUTOS (DB)
   ========================= */

/**
 * Busca um produto válido no banco.
 * Retorna array no formato usado pelo carrinho:
 * [
 *   'id'    => int,
 *   'name'  => string,
 *   'price' => float,
 *   'image' => string
 * ]
 */
function find_product(int $productId): ?array
{
    global $pdo;

    $st = $pdo->prepare("
        SELECT 
            p.id_produto      AS id,
            p.nome            AS name,
            p.preco           AS price,
            COALESCE(p.imagem, '') AS image
        FROM produtos p
        WHERE p.id_produto = ?
          AND (p.disponivel = 1 OR p.disponivel IS NULL)
        LIMIT 1
    ");
    $st->execute([$productId]);
    $row = $st->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return null;
    }

    // imagem padrão se vier vazia
    if (trim($row['image']) === '') {
        $row['image'] = 'assets/product-cupcakes.jpg';
    }

    // garante tipos
    $row['id']    = (int)$row['id'];
    $row['price'] = (float)$row['price'];

    return $row;
}

/* =========================
   CARRINHO (SESSION)
   ========================= */

function get_cart(): array
{
    return $_SESSION['cart'] ?? [];
}

function save_cart(array $cart): void
{
    $_SESSION['cart'] = $cart;
}

/**
 * Adiciona N unidades do produto ao carrinho.
 */
function add_to_cart(int $productId, int $qty = 1): void
{
    $qty = max(1, $qty);
    $product = find_product($productId);
    if (!$product) return;

    $cart = get_cart();
    $key  = (int)$productId; // padroniza a chave

    if (isset($cart[$key])) {
        $cart[$key]['quantity'] += $qty;
    } else {
        $cart[$key] = [
            'product'  => $product, // id, name, price, image
            'quantity' => $qty,
        ];
    }

    save_cart($cart);
}

function remove_from_cart(int $productId): void
{
    $cart = get_cart();
    $key  = (int)$productId;

    if (isset($cart[$key])) {
        unset($cart[$key]);
        save_cart($cart);
    }
}

/**
 * Define a quantidade exata do item.
 * Se quantity <= 0, remove o item.
 */
function update_quantity(int $productId, int $quantity): void
{
    $key = (int)$productId;

    if ($quantity <= 0) {
        remove_from_cart($key);
        return;
    }

    $cart = get_cart();
    if (isset($cart[$key])) {
        $cart[$key]['quantity'] = $quantity;
        save_cart($cart);
    }
}

function clear_cart(): void
{
    unset($_SESSION['cart']);
}

/**
 * Retorna totais do carrinho.
 * [
 *   'count' => int (nº de itens),
 *   'total' => float (valor total)
 * ]
 */
function cart_totals(): array
{
    $cart  = get_cart();
    $count = 0;
    $total = 0.0;

    foreach ($cart as $item) {
        $q   = (int)$item['quantity'];
        $pr  = (float)$item['product']['price'];
        $count += $q;
        $total += $pr * $q;
    }

    return [
        'count' => $count,
        'total' => $total,
    ];
}

/* =========================
   UTIL
   ========================= */

function format_currency(float $value): string
{
    return 'R$ ' . number_format($value, 2, ',', '.');
}
