import 'package:flutter/material.dart';

import '../l10n/app_strings.dart';
import '../models/category_model.dart';
import '../models/product_model.dart';
import '../services/cart_service.dart';
import '../services/client_api_service.dart';
import '../services/favourite_service.dart';
import '../widgets/product_card.dart';
import '../widgets/responsive_product_grid.dart';

class SubcategoryDetailScreen extends StatefulWidget {
  const SubcategoryDetailScreen({
    super.key,
    required this.subcategoryId,
    this.cartService,
    this.favouriteService,
  });

  final int subcategoryId;
  final CartService? cartService;
  final FavouriteService? favouriteService;

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
        _api.getSubcategory(widget.subcategoryId),
        _api.getProducts(subcategoryId: widget.subcategoryId, perPage: 50),
      ]);
      if (!mounted) return;
      setState(() {
        _subcategory = results[0] as SubcategoryModel?;
        _products = results[1] as List<ProductModel>;
        _loading = false;
        if (_subcategory == null) _error = 'Subcategory not found';
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
    if (_loading && _subcategory == null) {
      return Scaffold(appBar: AppBar(title: Text(AppStrings.tr('subcategory.title_generic'))), body: const Center(child: CircularProgressIndicator()));
    }
    if (_error != null && _subcategory == null) {
      return Scaffold(appBar: AppBar(title: Text(AppStrings.tr('subcategory.title_generic'))), body: Center(child: Text(_error!, style: TextStyle(color: colorScheme.error))));
    }
    final sub = _subcategory!;
    return Scaffold(
      appBar: AppBar(title: Text(sub.name)),
      body: RefreshIndicator(
        onRefresh: _load,
        child: _products.isEmpty
            ? SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                child: Padding(padding: const EdgeInsets.all(24), child: Center(child: Text(AppStrings.tr('subcategory.empty_products'), style: TextStyle(color: colorScheme.outline)))),
              )
            : GridView.builder(
                padding: const EdgeInsets.all(16),
                gridDelegate: responsiveProductGrid(context),
                itemCount: _products.length,
                itemBuilder: (context, i) {
                  final product = _products[i];
                  return ProductCard(
                    key: ValueKey('sd_${product.id}'),
                    product: product,
                    isFavourite: widget.favouriteService?.isFavourite(product.id) ?? false,
                    onToggleFavourite: widget.favouriteService != null ? () => widget.favouriteService!.toggle(product.id) : null,
                    onTap: () => Navigator.of(context).pushNamed('/product/${product.id}'),
                    onAddToCart: () => _addToCart(product),
                  );
                },
              ),
      ),
    );
  }
}
