import 'dart:convert';

import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'api_client.dart';

class CartItem {
  CartItem({
    required this.productId,
    required this.name,
    required this.price,
    this.photo,
    this.quantity = 1,
  });

  final int productId;
  final String name;
  final double price;
  final String? photo;
  int quantity;

  double get lineTotal => price * quantity;

  Map<String, dynamic> toJson() => {
        'product_id': productId,
        'name': name,
        'price': price,
        'photo': photo,
        'quantity': quantity,
      };

  factory CartItem.fromJson(Map<String, dynamic> json) => CartItem(
        productId: (json['product_id'] as num).toInt(),
        name: json['name'] as String? ?? '',
        price: (json['price'] as num?)?.toDouble() ?? 0,
        photo: json['photo'] as String?,
        quantity: (json['quantity'] as num?)?.toInt() ?? 1,
      );
}

/// Client-side cart stored in SharedPreferences, matching the website's
/// localStorage approach. Checkout calls `POST /api/orders/checkout`.
class CartService extends ChangeNotifier {
  CartService() {
    _load();
  }

  static const _key = 'cart';
  final List<CartItem> _items = [];

  List<CartItem> get items => List.unmodifiable(_items);
  int get itemCount => _items.fold(0, (sum, e) => sum + e.quantity);
  double get subtotal => _items.fold(0.0, (sum, e) => sum + e.lineTotal);
  bool get isEmpty => _items.isEmpty;

  Future<void> _load() async {
    final prefs = await SharedPreferences.getInstance();
    final raw = prefs.getString(_key);
    if (raw != null) {
      try {
        final list = jsonDecode(raw) as List<dynamic>;
        _items.clear();
        _items.addAll(list.map((e) => CartItem.fromJson(e as Map<String, dynamic>)));
        notifyListeners();
      } catch (e) {
        debugPrint('CartService._load error: $e');
      }
    }
  }

  Future<void> _save() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_key, jsonEncode(_items.map((e) => e.toJson()).toList()));
  }

  void addItem({
    required int productId,
    required String name,
    required double price,
    String? photo,
  }) {
    final existing = _items.where((e) => e.productId == productId).firstOrNull;
    if (existing != null) {
      existing.quantity++;
    } else {
      _items.add(CartItem(productId: productId, name: name, price: price, photo: photo));
    }
    _save();
    notifyListeners();
  }

  void removeItem(int productId) {
    _items.removeWhere((e) => e.productId == productId);
    _save();
    notifyListeners();
  }

  void updateQuantity(int productId, int qty) {
    if (qty <= 0) {
      removeItem(productId);
      return;
    }
    final item = _items.where((e) => e.productId == productId).firstOrNull;
    if (item != null) {
      item.quantity = qty;
      _save();
      notifyListeners();
    }
  }

  void clear() {
    _items.clear();
    _save();
    notifyListeners();
  }

  /// Checkout via API. Returns order data on success, throws on error.
  Future<Map<String, dynamic>> checkout({String? couponCode}) async {
    final client = ApiClient();
    final body = <String, dynamic>{
      'items': _items.map((e) => {'product_id': e.productId, 'quantity': e.quantity}).toList(),
      'payment_way': 'cash',
    };
    if (couponCode != null && couponCode.trim().isNotEmpty) {
      body['coupon_code'] = couponCode.trim();
    }
    final res = await client.post('/api/orders/checkout', body);
    final data = jsonDecode(res.body) as Map<String, dynamic>;
    if (res.statusCode >= 200 && res.statusCode < 300) {
      clear();
      return data;
    }
    final message = data['message'] as String? ?? 'Checkout failed';
    throw Exception(message);
  }
}
