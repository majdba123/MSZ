import 'package:flutter/material.dart';

import '../l10n/app_strings.dart';
import '../models/category_model.dart';
import '../models/product_model.dart';
import '../services/client_api_service.dart';
import '../widgets/product_card.dart';

class SubcategoryDetailScreen extends StatefulWidget {
  const SubcategoryDetailScreen({super.key, required this.subcategoryId});

  final int subcategoryId;

  @override
  State<SubcategoryDetailScreen> createState() => _SubcategoryDetailScreenState();
}

class _SubcategoryDetailScreenState extends State<SubcategoryDetailScreen> {
  final ClientApiService _api = ClientApiService();
  SubcategoryModel? _subcategory;
  List<ProductModel> _products = [];
  bool _loading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final sub = await _api.getSubcategory(widget.subcategoryId);
      final products = await _api.getProducts(subcategoryId: widget.subcategoryId, perPage: 50);
      if (!mounted) return;
      setState(() {
        _subcategory = sub;
        _products = products;
        _loading = false;
        if (sub == null) _error = 'Subcategory not found';
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _loading = false;
        _error = e.toString();
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    if (_loading && _subcategory == null) {
      return Scaffold(
        appBar: AppBar(title: Text(AppStrings.tr('subcategory.title_generic'))),
        body: const Center(child: CircularProgressIndicator()),
      );
    }
    if (_error != null && _subcategory == null) {
      return Scaffold(
        appBar: AppBar(title: Text(AppStrings.tr('subcategory.title_generic'))),
        body: Center(
          child: Text(
            _error!,
            style: TextStyle(color: colorScheme.error),
          ),
        ),
      );
    }
    final sub = _subcategory!;
    return Scaffold(
      appBar: AppBar(title: Text(sub.name)),
      body: RefreshIndicator(
        onRefresh: _load,
        child: _products.isEmpty
            ? SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                child: Padding(
                  padding: const EdgeInsets.all(24),
                  child: Center(
                    child: Text(
                      AppStrings.tr('subcategory.empty_products'),
                      style: TextStyle(color: colorScheme.outline),
                    ),
                  ),
                ),
              )
            : GridView.builder(
                padding: const EdgeInsets.all(16),
                gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: 2,
                  childAspectRatio: 0.72,
                  crossAxisSpacing: 12,
                  mainAxisSpacing: 12,
                ),
                itemCount: _products.length,
                itemBuilder: (context, i) {
                  final product = _products[i];
                  return ProductCard(
                    key: ValueKey('sd_${product.id}'),
                    product: product,
                    onTap: () => Navigator.of(context).pushNamed('/product/${product.id}'),
                    onAddToCart: () {},
                  );
                },
              ),
      ),
    );
  }
}
