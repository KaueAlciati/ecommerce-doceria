import 'models.dart';

const products = <Product>[
  Product(
    id: '1',
    name: 'Bolo de Morango',
    price: 89.90,
    image: 'assets/product-cake-1.jpg',
    category: 'bolos',
    description: 'Bolo de chocolate com recheio de morango e cobertura rosa',
  ),
  Product(
    id: '2',
    name: 'Cupcakes Variados',
    price: 45.00,
    image: 'assets/product-cupcakes.jpg',
    category: 'cupcakes',
    description: 'Kit com 6 cupcakes de sabores variados',
  ),
  Product(
    id: '3',
    name: 'Macarons Premium',
    price: 38.00,
    image: 'assets/product-macarons.jpg',
    category: 'doces',
    description: 'Caixa com 12 macarons franceses em cores pastéis',
  ),
  Product(
    id: '4',
    name: 'Bolo de Casamento',
    price: 450.00,
    image: 'assets/product-wedding-cake.jpg',
    category: 'bolos',
    description: 'Bolo elegante com flores de açúcar para casamentos',
  ),
  Product(
    id: '5',
    name: 'Brigadeiros Gourmet',
    price: 35.00,
    image: 'assets/product-brigadeiros.jpg',
    category: 'doces',
    description: 'Bandeja com 15 brigadeiros decorados',
  ),
  Product(
    id: '6',
    name: 'Bolo de Aniversário',
    price: 95.00,
    image: 'assets/product-birthday-cake.jpg',
    category: 'bolos',
    description: 'Bolo festivo com confeitos coloridos',
  ),
];

const categories = <Category>[
  Category(id: 'todos', name: 'Todos'),
  Category(id: 'bolos', name: 'Bolos'),
  Category(id: 'cupcakes', name: 'Cupcakes'),
  Category(id: 'doces', name: 'Doces'),
];

const testimonials = <Testimonial>[
  Testimonial(
    name: 'Maria Silva',
    text: 'Os doces mais deliciosos que já provei! Perfeitos para minha festa.',
    rating: 5,
  ),
  Testimonial(
    name: 'João Santos',
    text: 'Bolo de casamento maravilhoso! Todos os convidados adoraram.',
    rating: 5,
  ),
  Testimonial(
    name: 'Ana Costa',
    text: 'Atendimento impecável e produtos de altíssima qualidade!',
    rating: 5,
  ),
];
