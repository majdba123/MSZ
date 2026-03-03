class OrderModel {
  OrderModel({
    required this.id,
    this.orderNumber,
    this.status,
    this.paymentWay,
    this.subtotal,
    this.couponDiscount,
    this.total,
    this.couponCode,
    this.vendorName,
    this.vendorId,
    this.createdAt,
    this.items = const [],
  });

  final int id;
  final String? orderNumber;
  final String? status;
  final String? paymentWay;
  final double? subtotal;
  final double? couponDiscount;
  final double? total;
  final String? couponCode;
  final String? vendorName;
  final int? vendorId;
  final String? createdAt;
  final List<OrderItemModel> items;

  bool get canCancel => status != 'completed' && status != 'cancelled';

  factory OrderModel.fromJson(Map<String, dynamic> json) {
    final itemsList = json['items'] as List<dynamic>? ?? [];
    final vendor = json['vendor'] as Map<String, dynamic>?;
    return OrderModel(
      id: (json['id'] as num?)?.toInt() ?? 0,
      orderNumber: json['order_number']?.toString(),
      status: json['status']?.toString(),
      paymentWay: json['payment_way']?.toString(),
      subtotal: _toDouble(json['subtotal']),
      couponDiscount: _toDouble(json['coupon_discount']),
      total: _toDouble(json['total']),
      couponCode: json['coupon_code']?.toString(),
      vendorName: vendor?['store_name']?.toString() ?? json['vendor_name']?.toString(),
      vendorId: vendor != null ? (vendor['id'] as num?)?.toInt() : null,
      createdAt: json['created_at']?.toString(),
      items: itemsList.map((e) => OrderItemModel.fromJson(e as Map<String, dynamic>)).toList(),
    );
  }

  static double? _toDouble(dynamic v) {
    if (v == null) return null;
    if (v is num) return v.toDouble();
    if (v is String) return double.tryParse(v);
    return null;
  }
}

class OrderItemModel {
  OrderItemModel({
    required this.id,
    this.productName,
    this.productId,
    this.quantity = 1,
    this.unitPrice,
    this.lineTotal,
    this.discountAmount,
    this.photoUrl,
  });

  final int id;
  final String? productName;
  final int? productId;
  final int quantity;
  final double? unitPrice;
  final double? lineTotal;
  final double? discountAmount;
  final String? photoUrl;

  factory OrderItemModel.fromJson(Map<String, dynamic> json) {
    final product = json['product'] as Map<String, dynamic>?;
    return OrderItemModel(
      id: (json['id'] as num?)?.toInt() ?? 0,
      productName: product?['name']?.toString() ?? json['product_name']?.toString(),
      productId: product != null ? (product['id'] as num?)?.toInt() : (json['product_id'] as num?)?.toInt(),
      quantity: (json['quantity'] as num?)?.toInt() ?? 1,
      unitPrice: _toDouble(json['unit_price']),
      lineTotal: _toDouble(json['line_total']),
      discountAmount: _toDouble(json['discount_amount']),
      photoUrl: product?['first_photo_url']?.toString() ?? json['photo_url']?.toString(),
    );
  }

  static double? _toDouble(dynamic v) {
    if (v == null) return null;
    if (v is num) return v.toDouble();
    if (v is String) return double.tryParse(v);
    return null;
  }
}
