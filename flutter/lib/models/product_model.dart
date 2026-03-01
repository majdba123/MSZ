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

/// Full product for detail screen: includes description, photos, category, subcategory.
class ProductDetailModel extends ProductModel {
  const ProductDetailModel({
    required super.id,
    required super.name,
    super.firstPhotoUrl,
    required super.price,
    super.discountedPrice,
    super.hasActiveDiscount = false,
    super.discountPercentage,
    super.quantity = 0,
    super.vendor,
    super.averageRating = 0,
    super.reviewCount = 0,
    this.description,
    this.photos = const [],
    this.category,
    this.subcategory,
  }) : super();

  final String? description;
  final List<ProductPhotoModel> photos;
  final ProductCategoryRef? category;
  final ProductSubcategoryRef? subcategory;

  factory ProductDetailModel.fromJson(Map<String, dynamic> json) {
    final photosList = toList(json['photos']);
    final parsedPhotos = photosList
        ?.map((e) => ProductPhotoModel.fromJson(toMap(e) ?? <String, dynamic>{}))
        .toList() ?? [];
    final firstPhotoUrlFromPhotos = parsedPhotos.isNotEmpty ? parsedPhotos.first.url : null;
    return ProductDetailModel(
      id: toInt(json['id']),
      name: toStringVal(json['name']),
      firstPhotoUrl: toStringOrNull(json['first_photo_url']) ?? firstPhotoUrlFromPhotos,
      price: toDouble(json['price']),
      discountedPrice: toDoubleOrNull(json['discounted_price']),
      hasActiveDiscount: json['has_active_discount'] == true,
      discountPercentage: toDoubleOrNull(json['discount_percentage']),
      quantity: toInt(json['quantity']),
      vendor: toMap(json['vendor']) != null ? VendorSummary.fromJson(toMap(json['vendor'])!) : null,
      averageRating: toDouble(json['average_rating']),
      reviewCount: toInt(json['review_count']),
      description: toStringOrNull(json['description']),
      photos: parsedPhotos,
      category: toMap(json['category']) != null ? ProductCategoryRef.fromJson(toMap(json['category'])!) : null,
      subcategory: toMap(json['subcategory']) != null ? ProductSubcategoryRef.fromJson(toMap(json['subcategory'])!) : null,
    );
  }

  String? get primaryPhotoUrl {
    final primary = photos.where((p) => p.isPrimary).firstOrNull;
    if (primary != null) return primary.url;
    if (photos.isNotEmpty) return photos.first.url;
    return firstPhotoUrl;
  }
}

extension _FirstOrNull<E> on Iterable<E> {
  E? get firstOrNull => isEmpty ? null : first;
}

class ProductPhotoModel {
  ProductPhotoModel({required this.id, required this.path, required this.url, this.isPrimary = false});
  final int id;
  final String path;
  final String url;
  final bool isPrimary;

  factory ProductPhotoModel.fromJson(Map<String, dynamic> json) {
    return ProductPhotoModel(
      id: toInt(json['id']),
      path: toStringVal(json['path']),
      url: toStringVal(json['url']),
      isPrimary: json['is_primary'] == true,
    );
  }
}

class ProductCategoryRef {
  ProductCategoryRef({required this.id, required this.name});
  final int id;
  final String name;
  factory ProductCategoryRef.fromJson(Map<String, dynamic> json) =>
      ProductCategoryRef(id: toInt(json['id']), name: toStringVal(json['name']));
}

class ProductSubcategoryRef {
  ProductSubcategoryRef({required this.id, required this.name, this.categoryId});
  final int id;
  final String name;
  final int? categoryId;
  factory ProductSubcategoryRef.fromJson(Map<String, dynamic> json) => ProductSubcategoryRef(
        id: toInt(json['id']),
        name: toStringVal(json['name']),
        categoryId: toIntOrNull(json['category_id']),
      );
}
