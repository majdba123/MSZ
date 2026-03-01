import 'package:flutter/material.dart';

import '../models/product_model.dart';
import '../models/vendor_model.dart';
import '../services/client_api_service.dart';
import '../widgets/product_card.dart';

class VendorDetailScreen extends StatefulWidget {
  const VendorDetailScreen({super.key, required this.vendorId});

  final int vendorId;

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
  }

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final vendor = await _api.getVendor(widget.vendorId);
      final products = await _api.getProducts(vendorId: widget.vendorId, perPage: 50);
      if (!mounted) return;
      setState(() {
        _vendor = vendor;
        _products = products;
        _loading = false;
        if (vendor == null) _error = 'Store not found';
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
    if (_loading && _vendor == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('Store')),
        body: const Center(child: CircularProgressIndicator()),
      );
    }
    if (_error != null && _vendor == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('Store')),
        body: Center(child: Text(_error!, style: TextStyle(color: colorScheme.error))),
      );
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
              if (vendor.description != null && vendor.description!.trim().isNotEmpty)
                Padding(
                  padding: const EdgeInsets.all(16),
                  child: Text(
                    vendor.description!,
                    style: TextStyle(fontSize: 14, color: colorScheme.onSurfaceVariant, height: 1.5),
                  ),
                ),
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
                      child: Center(
                        child: Text('No products from this store', style: TextStyle(color: colorScheme.outline)),
                      ),
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
                          key: ValueKey('vd_${product.id}'),
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
