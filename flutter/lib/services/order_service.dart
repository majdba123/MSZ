import 'dart:convert';

import '../models/order_model.dart';
import 'api_client.dart';

/// Manages order API calls.
class OrderService {
  Future<OrderListResult> getOrders({int page = 1, String? status, int? vendorId, String? search}) async {
    final q = <String>['page=$page'];
    if (status != null && status.isNotEmpty) q.add('status=$status');
    if (vendorId != null) q.add('vendor_id=$vendorId');
    if (search != null && search.isNotEmpty) q.add('search=$search');
    final res = await ApiClient().get('/api/orders?${q.join('&')}');
    if (res.statusCode != 200) return OrderListResult(orders: [], lastPage: 1);
    final data = jsonDecode(res.body) as Map<String, dynamic>;
    final list = (data['data'] as List<dynamic>? ?? [])
        .map((e) => OrderModel.fromJson(e as Map<String, dynamic>))
        .toList();
    final meta = data['meta'] as Map<String, dynamic>? ?? {};
    return OrderListResult(
      orders: list,
      lastPage: (meta['last_page'] as num?)?.toInt() ?? 1,
    );
  }

  Future<OrderModel?> getOrder(int id) async {
    final res = await ApiClient().get('/api/orders/$id');
    if (res.statusCode != 200) return null;
    final data = jsonDecode(res.body) as Map<String, dynamic>;
    final d = data['data'] as Map<String, dynamic>?;
    return d != null ? OrderModel.fromJson(d) : null;
  }

  Future<bool> cancelOrder(int id) async {
    final res = await ApiClient().patch('/api/orders/$id/cancel');
    return res.statusCode >= 200 && res.statusCode < 300;
  }
}

class OrderListResult {
  OrderListResult({required this.orders, required this.lastPage});

  final List<OrderModel> orders;
  final int lastPage;
}
