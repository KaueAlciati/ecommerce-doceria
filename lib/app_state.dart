import 'package:flutter/foundation.dart';

import 'models.dart';

class AppState extends ChangeNotifier {
  final Map<String, CartItem> _cart = {};

  List<CartItem> get items => _cart.values.toList(growable: false);

  int get itemCount => _cart.values.fold<int>(0, (sum, item) => sum + item.quantity);

  double get total => _cart.values.fold<double>(0, (sum, item) => sum + item.total);

  bool isInCart(Product product) => _cart.containsKey(product.id);

  void addProduct(Product product) {
    final existing = _cart[product.id];
    if (existing != null) {
      existing.quantity += 1;
    } else {
      _cart[product.id] = CartItem(product: product);
    }
    notifyListeners();
  }

  void removeProduct(Product product) {
    _cart.remove(product.id);
    notifyListeners();
  }

  void updateQuantity(Product product, int quantity) {
    if (quantity <= 0) {
      removeProduct(product);
      return;
    }

    final existing = _cart[product.id];
    if (existing != null) {
      existing.quantity = quantity;
      notifyListeners();
    }
  }

  void clear() {
    _cart.clear();
    notifyListeners();
  }
}
