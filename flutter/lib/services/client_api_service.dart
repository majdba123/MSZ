import 'dart:convert';

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

  Future<CategoryModel?> getCategory(int id) async {
    final res = await _client.get('/api/categories/$id');
    if (res.statusCode != 200) return null;
    final data = jsonDecode(res.body) as Map<String, dynamic>?;
    final d = data?['data'] as Map<String, dynamic>?;
    return d != null ? CategoryModel.fromJson(d) : null;
  }

  Future<List<ProductModel>> getProducts({
    int perPage = 15,
    int page = 1,
    String? sort,
    int? categoryId,
    int? subcategoryId,
    int? vendorId,
    bool? hasDiscount,
  }) async {
    final q = <String>['per_page=$perPage', 'page=$page'];
    if (sort != null && sort.isNotEmpty) q.add('sort=$sort');
    if (categoryId != null) q.add('category_id=$categoryId');
    if (subcategoryId != null) q.add('subcategory_id=$subcategoryId');
    if (vendorId != null) q.add('vendor_id=$vendorId');
    if (hasDiscount != null) q.add('has_discount=${hasDiscount ? '1' : '0'}');
    final res = await _client.get('/api/products?${q.join('&')}');
    if (res.statusCode != 200) return [];
    final data = jsonDecode(res.body) as Map<String, dynamic>?;
    final list = data?['data'] as List<dynamic>? ?? [];
    return list.map((e) => ProductModel.fromJson(e as Map<String, dynamic>)).toList();
  }

  Future<Map<String, dynamic>?> getProductsPaginated({
    int perPage = 15,
    int page = 1,
    String? sort,
    int? categoryId,
    int? subcategoryId,
    int? vendorId,
    bool? hasDiscount,
  }) async {
    final q = <String>['per_page=$perPage', 'page=$page'];
    if (sort != null && sort.isNotEmpty) q.add('sort=$sort');
    if (categoryId != null) q.add('category_id=$categoryId');
    if (subcategoryId != null) q.add('subcategory_id=$subcategoryId');
    if (vendorId != null) q.add('vendor_id=$vendorId');
    if (hasDiscount != null) q.add('has_discount=${hasDiscount ? '1' : '0'}');
    final res = await _client.get('/api/products?${q.join('&')}');
    if (res.statusCode != 200) return null;
    return jsonDecode(res.body) as Map<String, dynamic>?;
  }

  Future<ProductDetailModel?> getProduct(int id) async {
    final res = await _client.get('/api/products/$id');
    if (res.statusCode != 200) return null;
    final data = jsonDecode(res.body) as Map<String, dynamic>?;
    final d = data?['data'] as Map<String, dynamic>?;
    return d != null ? ProductDetailModel.fromJson(d) : null;
  }

  Future<SubcategoryModel?> getSubcategory(int id) async {
    final res = await _client.get('/api/subcategories/$id');
    if (res.statusCode != 200) return null;
    final data = jsonDecode(res.body) as Map<String, dynamic>?;
    final d = data?['data'] as Map<String, dynamic>?;
    return d != null ? SubcategoryModel.fromJson(d) : null;
  }

  Future<List<VendorModel>> getVendors() async {
    final res = await _client.get('/api/vendors');
    if (res.statusCode != 200) return [];
    final data = jsonDecode(res.body) as Map<String, dynamic>?;
    final list = data?['data'] as List<dynamic>? ?? [];
    return list.map((e) => VendorModel.fromJson(e as Map<String, dynamic>)).toList();
  }

  Future<VendorModel?> getVendor(int id) async {
    final res = await _client.get('/api/vendors/$id');
    if (res.statusCode != 200) return null;
    final data = jsonDecode(res.body) as Map<String, dynamic>?;
    final d = data?['data'] as Map<String, dynamic>?;
    return d != null ? VendorModel.fromJson(d) : null;
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
