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
      id: json['id'] as int,
      name: json['name'] as String? ?? '',
      firstPhotoUrl: json['first_photo_url'] as String?,
      price: (json['price'] as num?)?.toDouble() ?? 0,
      discountedPrice: (json['discounted_price'] as num?)?.toDouble(),
      hasActiveDiscount: json['has_active_discount'] == true,
      discountPercentage: (json['discount_percentage'] as num?)?.toDouble(),
      quantity: json['quantity'] as int? ?? 0,
      vendor: json['vendor'] != null ? VendorSummary.fromJson(json['vendor'] as Map<String, dynamic>) : null,
      averageRating: (json['average_rating'] as num?)?.toDouble() ?? 0,
      reviewCount: json['review_count'] as int? ?? 0,
    );
  }
}

class VendorSummary {
  final int id;
  final String storeName;

  VendorSummary({required this.id, required this.storeName});

  factory VendorSummary.fromJson(Map<String, dynamic> json) {
    return VendorSummary(
      id: json['id'] as int,
      storeName: json['store_name'] as String? ?? '',
    );
  }
}
