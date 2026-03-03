import 'package:flutter/material.dart';

import '../l10n/app_strings.dart';
import '../models/category_model.dart';
import '../models/product_model.dart';
import '../services/cart_service.dart';
import '../services/client_api_service.dart';
import '../services/favourite_service.dart';
import '../widgets/product_card.dart';
import '../widgets/responsive_product_grid.dart';

class CategoryDetailScreen extends StatefulWidget {
  const CategoryDetailScreen({
    super.key,
    required this.categoryId,
    this.cartService,
    this.favouriteService,
  });

  final int categoryId;
  final CartService? cartService;
  final FavouriteService? favouriteService;

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
    widget.favouriteService?.addListener(_refresh);
  }

  @override
  void dispose() {
    widget.favouriteService?.removeListener(_refresh);
    super.dispose();
  }

  void _refresh() {
    if (mounted) setState(() {});
  }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final results = await Future.wait([
        _api.getCategory(widget.categoryId),
        _api.getProducts(categoryId: widget.categoryId, perPage: 50),
      ]);
      if (!mounted) return;
      setState(() {
        _category = results[0] as CategoryModel?;
        _products = results[1] as List<ProductModel>;
        _loading = false;
        if (_category == null) _error = 'Category not found';
      });
    } catch (e) {
      if (!mounted) return;
      setState(() { _loading = false; _error = e.toString(); });
    }
  }

  void _addToCart(ProductModel p) {
    widget.cartService?.addItem(productId: p.id, name: p.name, price: p.displayPrice, photo: p.firstPhotoUrl);
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(AppStrings.tr('common.added_to_cart')), duration: const Duration(seconds: 1)),
    );
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    if (_loading && _category == null) {
      return Scaffold(appBar: AppBar(title: Text(AppStrings.tr('category.title_generic'))), body: const Center(child: CircularProgressIndicator()));
    }
    if (_error != null && _category == null) {
      return Scaffold(appBar: AppBar(title: Text(AppStrings.tr('category.title_generic'))), body: Center(child: Text(_error!, style: TextStyle(color: colorScheme.error))));
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
                  padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
                  child: Text(AppStrings.tr('home.subcategories_title'), style: Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w700)),
                ),
                SizedBox(
                  height: 48,
                  child: ListView.builder(
                    scrollDirection: Axis.horizontal,
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    itemCount: cat.subcategories.length,
                    itemBuilder: (context, i) {
                      final sub = cat.subcategories[i];
                      return Padding(
                        padding: const EdgeInsetsDirectional.only(end: 10),
                        child: ActionChip(label: Text(sub.name), avatar: const Icon(Icons.label_outline, size: 16), onPressed: () => Navigator.of(context).pushNamed('/subcategory/${sub.id}')),
                      );
                    },
                  ),
                ),
                const SizedBox(height: 16),
              ],
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                child: Text(AppStrings.tr('nav.products'), style: Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w700)),
              ),
              const SizedBox(height: 8),
              _products.isEmpty
                  ? Padding(padding: const EdgeInsets.all(24), child: Center(child: Text(AppStrings.tr('category.empty_products'), style: TextStyle(color: colorScheme.outline))))
                  : GridView.builder(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      padding: const EdgeInsets.all(16),
                      gridDelegate: responsiveProductGrid(context),
                      itemCount: _products.length,
                      itemBuilder: (context, i) {
                        final product = _products[i];
                        return ProductCard(
                          key: ValueKey('cd_${product.id}'),
                          product: product,
                          isFavourite: widget.favouriteService?.isFavourite(product.id) ?? false,
                          onToggleFavourite: widget.favouriteService != null ? () => widget.favouriteService!.toggle(product.id) : null,
                          onTap: () => Navigator.of(context).pushNamed('/product/${product.id}'),
                          onAddToCart: () => _addToCart(product),
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
