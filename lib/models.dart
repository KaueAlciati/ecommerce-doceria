class Product {
  const Product({
    required this.id,
    required this.name,
    required this.price,
    required this.image,
    required this.category,
    required this.description,
  });

  final String id;
  final String name;
  final double price;
  final String image;
  final String category;
  final String description;
}

class Category {
  const Category({
    required this.id,
    required this.name,
  });

  final String id;
  final String name;
}

class Testimonial {
  const Testimonial({
    required this.name,
    required this.text,
    required this.rating,
  });

  final String name;
  final String text;
  final int rating;
}

class CartItem {
  CartItem({
    required this.product,
    this.quantity = 1,
  });

  final Product product;
  int quantity;

  double get total => product.price * quantity;
}
