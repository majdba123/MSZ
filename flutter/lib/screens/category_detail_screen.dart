import 'package:flutter/material.dart';

import '../models/category_model.dart';
import '../models/product_model.dart';
import '../services/client_api_service.dart';
import '../widgets/product_card.dart';

class CategoryDetailScreen extends StatefulWidget {
  const CategoryDetailScreen({super.key, required this.categoryId});

  final int categoryId;

  @override
  State<CategoryDetailScreen> createState() => _CategoryDetailScreenState();
}

class _CategoryDetailScreenState extends State<CategoryDetailScreen> {
  final ClientApiService _api = ClientApiService();
  CategoryModel? _category;
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
      final cat = await _api.getCategory(widget.categoryId);
      final products = await _api.getProducts(categoryId: widget.categoryId, perPage: 50);
      if (!mounted) return;
      setState(() {
        _category = cat;
        _products = products;
        _loading = false;
        if (cat == null) _error = 'Category not found';
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
    if (_loading && _category == null) {
      return Scaffold(appBar: AppBar(title: const Text('Category')), body: const Center(child: CircularProgressIndicator()));
    }
    if (_error != null && _category == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('Category')),
        body: Center(child: Text(_error!, style: TextStyle(color: colorScheme.error))),
      );
    }
    final cat = _category!;
    return Scaffold(
      appBar: AppBar(title: Text(cat.name)),
      body: RefreshIndicator(
        onRefresh: _load,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              if (cat.subcategories.isNotEmpty) ...[
                Padding(
                  padding: const EdgeInsets.all(16),
                  child: Text(
                    'Subcategories',
                    style: Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w700),
                  ),
                ),
                SizedBox(
                  height: 100,
                  child: ListView.builder(
                    scrollDirection: Axis.horizontal,
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    itemCount: cat.subcategories.length,
                    itemBuilder: (context, i) {
                      final sub = cat.subcategories[i];
                      return Padding(
                        padding: const EdgeInsets.only(right: 12),
                        child: Card(
                          child: InkWell(
                            onTap: () => Navigator.of(context).pushNamed('/subcategory/${sub.id}'),
                            borderRadius: BorderRadius.circular(16),
                            child: Padding(
                              padding: const EdgeInsets.all(12),
                              child: Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  Container(
                                    width: 48,
                                    height: 48,
                                    decoration: BoxDecoration(
                                      color: colorScheme.surfaceContainerHighest,
                                      borderRadius: BorderRadius.circular(12),
                                    ),
                                    child: const Icon(Icons.label_outline),
                                  ),
                                  const SizedBox(width: 12),
                                  Text(sub.name, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w700)),
                                ],
                              ),
                            ),
                          ),
                        ),
                      );
                    },
                  ),
                ),
                const SizedBox(height: 24),
              ],
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                child: Text(
                  'Products',
                  style: Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w700),
                ),
              ),
              const SizedBox(height: 12),
              _products.isEmpty
                  ? Padding(
                      padding: const EdgeInsets.all(24),
                      child: Center(child: Text('No products in this category', style: TextStyle(color: colorScheme.outline))),
                    )
                  : GridView.builder(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
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
                          key: ValueKey('cd_${product.id}'),
                          product: product,
                          onTap: () => Navigator.of(context).pushNamed('/product/${product.id}'),
                          onAddToCart: () {},
                        );
                      },
                    ),
              const SizedBox(height: 32),
            ],
          ),
        ),
      ),
    );
  }
}
