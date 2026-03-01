import 'package:flutter/material.dart';

import '../l10n/app_strings.dart';
import '../models/product_model.dart';
import '../services/client_api_service.dart';
import '../utils/app_urls.dart';

class ProductDetailScreen extends StatefulWidget {
  const ProductDetailScreen({super.key, required this.productId});

  final int productId;

  @override
  State<ProductDetailScreen> createState() => _ProductDetailScreenState();
}

class _ProductDetailScreenState extends State<ProductDetailScreen> {
  ProductDetailModel? _product;
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
      final api = ClientApiService();
      final product = await api.getProduct(widget.productId);
      if (!mounted) return;
      setState(() {
        _product = product;
        _loading = false;
        if (product == null) _error = 'Product not found';
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
    if (_loading && _product == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('Product')),
        body: const Center(child: CircularProgressIndicator()),
      );
    }
    if (_error != null && _product == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('Product')),
        body: Center(
          child: Padding(
            padding: const EdgeInsets.all(24),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(_error!, style: TextStyle(color: colorScheme.error), textAlign: TextAlign.center),
                const SizedBox(height: 16),
                FilledButton(onPressed: () => Navigator.of(context).pop(), child: const Text('Back')),
              ],
            ),
          ),
        ),
      );
    }
    final p = _product!;
    final rawUrl = p.primaryPhotoUrl ?? p.firstPhotoUrl;
    final photoUrl = rawUrl != null && rawUrl.trim().isNotEmpty
        ? (rawUrl.startsWith('http') ? rawUrl : imageUrl(rawUrl) ?? rawUrl)
        : null;

    return Scaffold(
      appBar: AppBar(
        title: Text(p.name, overflow: TextOverflow.ellipsis),
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            AspectRatio(
              aspectRatio: 1,
              child: Container(
                color: colorScheme.surfaceContainerHighest.withValues(alpha: 0.3),
                child: AppNetworkImage(
                  url: photoUrl,
                  fit: BoxFit.contain,
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    p.name,
                    style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                          fontWeight: FontWeight.w800,
                          color: colorScheme.onSurface,
                        ),
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      _Stars(rating: p.averageRating),
                      if (p.reviewCount > 0) ...[
                        const SizedBox(width: 8),
                        Text(
                          '${p.reviewCount} ${AppStrings.tr('common.reviews')}',
                          style: TextStyle(fontSize: 14, color: colorScheme.outline),
                        ),
                      ],
                    ],
                  ),
                  if (p.vendor != null) ...[
                    const SizedBox(height: 12),
                    Text(
                      'Sold by ${p.vendor!.storeName}',
                      style: TextStyle(fontSize: 14, color: colorScheme.primary, fontWeight: FontWeight.w600),
                    ),
                  ],
                  const SizedBox(height: 16),
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.baseline,
                    textBaseline: TextBaseline.alphabetic,
                    children: [
                      Text(
                        p.displayPrice.toStringAsFixed(0),
                        style: TextStyle(
                          fontSize: 28,
                          fontWeight: FontWeight.w900,
                          color: p.hasActiveDiscount ? colorScheme.error : colorScheme.onSurface,
                        ),
                      ),
                      Text(' SYP', style: TextStyle(fontSize: 14, color: colorScheme.outline)),
                      if (p.hasActiveDiscount && p.discountedPrice != null) ...[
                        const SizedBox(width: 12),
                        Text(
                          '${p.price.toStringAsFixed(0)} SYP',
                          style: TextStyle(
                            fontSize: 14,
                            color: colorScheme.outline,
                            decoration: TextDecoration.lineThrough,
                          ),
                        ),
                      ],
                    ],
                  ),
                  if (p.description != null && p.description!.trim().isNotEmpty) ...[
                    const SizedBox(height: 24),
                    Text(
                      'Description',
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w700,
                        color: colorScheme.onSurface,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      p.description!,
                      style: TextStyle(fontSize: 14, color: colorScheme.onSurfaceVariant, height: 1.5),
                    ),
                  ],
                  const SizedBox(height: 24),
                  SizedBox(
                    width: double.infinity,
                    child: FilledButton(
                      onPressed: p.inStock ? () {} : null,
                      child: Text(p.inStock ? AppStrings.tr('common.add_to_cart') : AppStrings.tr('common.sold_out')),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _Stars extends StatelessWidget {
  const _Stars({this.rating = 0});

  final double rating;

  @override
  Widget build(BuildContext context) {
    final r = rating.clamp(0.0, 5.0).round();
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: List.generate(
        5,
        (i) => Icon(i < r ? Icons.star : Icons.star_border, size: 20, color: Colors.amber),
      ),
    );
  }
}
