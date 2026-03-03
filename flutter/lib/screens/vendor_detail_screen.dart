import 'package:flutter/material.dart';

import '../l10n/app_strings.dart';
import '../models/product_model.dart';
import '../models/vendor_model.dart';
import '../services/cart_service.dart';
import '../services/client_api_service.dart';
import '../services/favourite_service.dart';
import '../utils/app_urls.dart';
import '../widgets/product_card.dart';
import '../widgets/responsive_product_grid.dart';

class VendorDetailScreen extends StatefulWidget {
  const VendorDetailScreen({
    super.key,
    required this.vendorId,
    this.cartService,
    this.favouriteService,
  });

  final int vendorId;
  final CartService? cartService;
  final FavouriteService? favouriteService;

  @override
  State<VendorDetailScreen> createState() => _VendorDetailScreenState();
}

class _VendorDetailScreenState extends State<VendorDetailScreen> {
  final ClientApiService _api = ClientApiService();
  VendorModel? _vendor;
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
        _api.getVendor(widget.vendorId),
        _api.getProducts(vendorId: widget.vendorId, perPage: 50),
      ]);
      if (!mounted) return;
      setState(() {
        _vendor = results[0] as VendorModel?;
        _products = results[1] as List<ProductModel>;
        _loading = false;
        if (_vendor == null) _error = 'Store not found';
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
    if (_loading && _vendor == null) {
      return Scaffold(appBar: AppBar(title: Text(AppStrings.tr('vendor.title_generic'))), body: const Center(child: CircularProgressIndicator()));
    }
    if (_error != null && _vendor == null) {
      return Scaffold(appBar: AppBar(title: Text(AppStrings.tr('vendor.title_generic'))), body: Center(child: Text(_error!, style: TextStyle(color: colorScheme.error))));
    }
    final vendor = _vendor!;
    return Scaffold(
      appBar: AppBar(title: Text(vendor.storeName)),
      body: RefreshIndicator(
        onRefresh: _load,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              _buildVendorHeader(colorScheme, vendor),
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                child: Text(AppStrings.tr('nav.products'), style: Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w700)),
              ),
              const SizedBox(height: 8),
              _products.isEmpty
                  ? Padding(padding: const EdgeInsets.all(24), child: Center(child: Text(AppStrings.tr('vendor.empty_products'), style: TextStyle(color: colorScheme.outline))))
                  : GridView.builder(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      padding: const EdgeInsets.all(16),
                      gridDelegate: responsiveProductGrid(context),
                      itemCount: _products.length,
                      itemBuilder: (context, i) {
                        final product = _products[i];
                        return ProductCard(
                          key: ValueKey('vd_${product.id}'),
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

  Widget _buildVendorHeader(ColorScheme colorScheme, VendorModel vendor) {
    return Container(
      padding: const EdgeInsets.all(16),
      child: Row(
        children: [
          ClipRRect(
            borderRadius: BorderRadius.circular(16),
            child: SizedBox(
              width: 64,
              height: 64,
              child: AppNetworkImage(url: vendor.logo, fit: BoxFit.cover),
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(vendor.storeName, style: TextStyle(fontSize: 18, fontWeight: FontWeight.w700, color: colorScheme.onSurface)),
                if (vendor.description != null && vendor.description!.trim().isNotEmpty)
                  Padding(
                    padding: const EdgeInsets.only(top: 4),
                    child: Text(vendor.description!, style: TextStyle(fontSize: 13, color: colorScheme.outline, height: 1.4), maxLines: 3, overflow: TextOverflow.ellipsis),
                  ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
