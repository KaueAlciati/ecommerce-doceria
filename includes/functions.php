<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/data.php';

function find_product(string $productId): ?array
{
    global $products;

    foreach ($products as $product) {
        if ($product['id'] === $productId) {
            return $product;
        }
    }

    return null;
}

function get_cart(): array
{
    return $_SESSION['cart'] ?? [];
}

function save_cart(array $cart): void
{
    $_SESSION['cart'] = $cart;
}

function add_to_cart(string $productId): void
{
    $product = find_product($productId);

    if (!$product) {
        return;
    }

    $cart = get_cart();

    if (isset($cart[$productId])) {
        $cart[$productId]['quantity'] += 1;
    } else {
        $cart[$productId] = [
            'product' => $product,
            'quantity' => 1,
        ];
    }

    save_cart($cart);
}

function remove_from_cart(string $productId): void
{
    $cart = get_cart();

    if (isset($cart[$productId])) {
        unset($cart[$productId]);
        save_cart($cart);
    }
}

function update_quantity(string $productId, int $quantity): void
{
    if ($quantity <= 0) {
        remove_from_cart($productId);
        return;
    }

    $cart = get_cart();

    if (isset($cart[$productId])) {
        $cart[$productId]['quantity'] = $quantity;
        save_cart($cart);
    }
}

function clear_cart(): void
{
    unset($_SESSION['cart']);
}

function cart_totals(): array
{
    $cart = get_cart();
    $count = 0;
    $total = 0.0;

    foreach ($cart as $item) {
        $quantity = $item['quantity'];
        $price = $item['product']['price'];
        $count += $quantity;
        $total += $price * $quantity;
    }

    return [
        'count' => $count,
        'total' => $total,
    ];
}

function format_currency(float $value): string
{
    return 'R$ ' . number_format($value, 2, ',', '.');
}