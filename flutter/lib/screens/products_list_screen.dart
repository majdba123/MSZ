import 'package:flutter/material.dart';

import '../l10n/app_strings.dart';
import '../models/category_model.dart';
import '../models/product_model.dart';
import '../models/vendor_model.dart';
import '../services/client_api_service.dart';
import '../widgets/product_card.dart';

class ProductsListScreen extends StatefulWidget {
  const ProductsListScreen({
    super.key,
    this.categoryId,
    this.subcategoryId,
    this.vendorId,
    this.title,
  });

  final int? categoryId;
  final int? subcategoryId;
  final int? vendorId;
  final String? title;

  @override
  State<ProductsListScreen> createState() => _ProductsListScreenState();
}

class _ProductsListScreenState extends State<ProductsListScreen> {
  final ClientApiService _api = ClientApiService();
  List<ProductModel> _products = [];
  List<CategoryModel> _categories = [];
  List<VendorModel> _vendors = [];
  int _page = 1;
  int? _lastPage;
  bool _loading = true;
  String? _error;
  int? _selectedCategoryId;
  int? _selectedVendorId;

  @override
  void initState() {
    super.initState();
    _selectedCategoryId = widget.categoryId;
    _selectedVendorId = widget.vendorId;
    _loadCategoriesAndVendors();
    _loadProducts();
  }

  Future<void> _loadCategoriesAndVendors() async {
    final cats = await _api.getCategories();
    final vendors = await _api.getVendors();
    if (!mounted) return;
    setState(() {
      _categories = cats;
      _vendors = vendors;
    });
  }

  Future<void> _loadProducts({bool reset = false}) async {
    if (reset) _page = 1;
    setState(() {
      _loading = true;
      _error = null;
      if (reset) _products = [];
    });
    try {
      final data = await _api.getProductsPaginated(
        page: _page,
        perPage: 15,
        categoryId: _selectedCategoryId ?? widget.categoryId,
        subcategoryId: widget.subcategoryId,
        vendorId: _selectedVendorId ?? widget.vendorId,
      );
      if (!mounted) return;
      final list = (data?['data'] as List<dynamic>?)?.map((e) => ProductModel.fromJson(e as Map<String, dynamic>)).toList() ?? [];
      final meta = data?['meta'] as Map<String, dynamic>?;
      final lastPage = meta != null ? toInt(meta['last_page']) : 1;
      setState(() {
        _products = reset ? list : [..._products, ...list];
        _lastPage = lastPage;
        _loading = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _loading = false;
        _error = e.toString();
      });
    }
  }

  int toInt(dynamic v) => v is int ? v : int.tryParse(v?.toString() ?? '') ?? 1;

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final title = widget.title ?? AppStrings.tr('products.title_generic');

    return Scaffold(
      appBar: AppBar(
        title: Text(title),
        actions: [
          IconButton(
            icon: const Icon(Icons.filter_list),
            onPressed: () => _showFilters(context),
          ),
        ],
      ),
      body: Column(
        children: [
          if (_categories.isNotEmpty || _vendors.isNotEmpty)
            SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              child: Row(
                children: [
                  if (_categories.isNotEmpty)
                    DropdownButton<int>(
                      value: _selectedCategoryId,
                      hint: Text(AppStrings.tr('nav.categories')),
                      items: [
                        DropdownMenuItem<int>(value: null, child: Text(AppStrings.tr('nav.categories'))),
                        ..._categories.map((c) => DropdownMenuItem<int>(value: c.id, child: Text(c.name))),
                      ],
                      onChanged: (v) {
                        setState(() => _selectedCategoryId = v);
                        _loadProducts(reset: true);
                      },
                    ),
                  const SizedBox(width: 16),
                  if (_vendors.isNotEmpty)
                    DropdownButton<int>(
                      value: _selectedVendorId,
                      hint: Text(AppStrings.tr('nav.stores')),
                      items: [
                        DropdownMenuItem<int>(value: null, child: Text(AppStrings.tr('nav.stores'))),
                        ..._vendors.map((v) => DropdownMenuItem<int>(value: v.id, child: Text(v.storeName))),
                      ],
                      onChanged: (v) {
                        setState(() => _selectedVendorId = v);
                        _loadProducts(reset: true);
                      },
                    ),
                ],
              ),
            ),
          Expanded(
            child: _error != null && _products.isEmpty
                ? Center(
                    child: Text(
                      AppStrings.tr('products.error_generic'),
                      style: TextStyle(color: colorScheme.error),
                    ),
                  )
                : _products.isEmpty && !_loading
                    ? Center(
                        child: Text(
                          AppStrings.tr('products.empty_list'),
                          style: TextStyle(color: colorScheme.outline),
                        ),
                      )
                    : RefreshIndicator(
                        onRefresh: () => _loadProducts(reset: true),
                        child: GridView.builder(
                          padding: const EdgeInsets.all(16),
                          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                            crossAxisCount: 2,
                            childAspectRatio: 0.72,
                            crossAxisSpacing: 12,
                            mainAxisSpacing: 12,
                          ),
                          itemCount: _products.length + (_loading && _products.isEmpty ? 1 : 0) + (_hasMore && _products.isNotEmpty ? 1 : 0),
                          itemBuilder: (context, i) {
                            if (i >= _products.length) {
                              if (_loading && _products.isEmpty)
                                return const Center(child: Padding(padding: EdgeInsets.all(24), child: CircularProgressIndicator()));
                              if (_hasMore && _products.isNotEmpty) {
                                if (i == _products.length) {
                                  WidgetsBinding.instance.addPostFrameCallback((_) => _loadMore());
                                  return const Center(child: Padding(padding: EdgeInsets.all(16), child: CircularProgressIndicator()));
                                }
                              }
                              return const SizedBox.shrink();
                            }
                            final product = _products[i];
                            return ProductCard(
                              key: ValueKey('pl_${product.id}'),
                              product: product,
                              onTap: () => Navigator.of(context).pushNamed('/product/${product.id}'),
                              onAddToCart: () {},
                            );
                          },
                        ),
                      ),
          ),
        ],
      ),
    );
  }

  bool get _hasMore => _lastPage != null && _page < _lastPage!;

  Future<void> _loadMore() async {
    if (_loading || _lastPage == null || _page >= _lastPage!) return;
    setState(() => _page++);
    await _loadProducts(reset: false);
  }

  void _showFilters(BuildContext context) {
    setState(() {
      _selectedCategoryId = widget.categoryId;
      _selectedVendorId = widget.vendorId;
    });
    _loadProducts(reset: true);
  }
}
