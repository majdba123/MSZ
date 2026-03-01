import 'dart:convert';

import '../config/api_config.dart';
import '../models/category_model.dart';
import '../models/product_model.dart';
import '../models/vendor_model.dart';
import 'api_client.dart';

class ClientApiService {
  ClientApiService({ApiClient? client}) : _client = client ?? ApiClient();

  final ApiClient _client;

  Future<List<CategoryModel>> getCategories() async {
    final res = await _client.get('/api/categories');
    if (res.statusCode != 200) return [];
    final data = jsonDecode(res.body) as Map<String, dynamic>?;
    final list = data?['data'] as List<dynamic>? ?? [];
    return list.map((e) => CategoryModel.fromJson(e as Map<String, dynamic>)).toList();
  }

  Future<List<ProductModel>> getProducts({int perPage = 5, String? sort}) async {
    var path = '/api/products?per_page=$perPage';
    if (sort != null && sort.isNotEmpty) path += '&sort=$sort';
    final res = await _client.get(path);
    if (res.statusCode != 200) return [];
    final data = jsonDecode(res.body) as Map<String, dynamic>?;
    final list = data?['data'] as List<dynamic>? ?? [];
    return list.map((e) => ProductModel.fromJson(e as Map<String, dynamic>)).toList();
  }

  Future<List<VendorModel>> getVendors() async {
    final res = await _client.get('/api/vendors');
    if (res.statusCode != 200) return [];
    final data = jsonDecode(res.body) as Map<String, dynamic>?;
    final list = data?['data'] as List<dynamic>? ?? [];
    return list.map((e) => VendorModel.fromJson(e as Map<String, dynamic>)).toList();
  }

  Future<bool> sendContact({String? name, required String email, required String message}) async {
    final res = await _client.post('/api/contact', {
      'name': name?.trim(),
      'email': email.trim(),
      'message': message.trim(),
    });
    return res.statusCode >= 200 && res.statusCode < 300;
  }
}
