import '../utils/json_parsers.dart';

class ProductModel {
  final int id;
  final String name;
  final String? firstPhotoUrl;
  final double price;
  final double? discountedPrice;
  final bool hasActiveDiscount;
  final double? discountPercentage;
  final int quantity;
  final VendorSummary? vendor;
  final double averageRating;
  final int reviewCount;

  const ProductModel({
    required this.id,
    required this.name,
    this.firstPhotoUrl,
    required this.price,
    this.discountedPrice,
    this.hasActiveDiscount = false,
    this.discountPercentage,
    this.quantity = 0,
    this.vendor,
    this.averageRating = 0,
    this.reviewCount = 0,
  });

  bool get inStock => quantity > 0;
  double get displayPrice => hasActiveDiscount && discountedPrice != null ? discountedPrice! : price;

  factory ProductModel.fromJson(Map<String, dynamic> json) {
    return ProductModel(
      id: toInt(json['id']),
      name: toStringVal(json['name']),
      firstPhotoUrl: toStringOrNull(json['first_photo_url']),
      price: toDouble(json['price']),
      discountedPrice: toDoubleOrNull(json['discounted_price']),
      hasActiveDiscount: json['has_active_discount'] == true,
      discountPercentage: toDoubleOrNull(json['discount_percentage']),
      quantity: toInt(json['quantity']),
      vendor: toMap(json['vendor']) != null ? VendorSummary.fromJson(toMap(json['vendor'])!) : null,
      averageRating: toDouble(json['average_rating']),
      reviewCount: toInt(json['review_count']),
    );
  }
}

class VendorSummary {
  final int id;
  final String storeName;

  VendorSummary({required this.id, required this.storeName});

  factory VendorSummary.fromJson(Map<String, dynamic> json) {
    return VendorSummary(
      id: toInt(json['id']),
      storeName: toStringVal(json['store_name']),
    );
  }
}
