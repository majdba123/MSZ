import 'package:flutter/material.dart';

import '../l10n/app_strings.dart';
import '../models/product_model.dart';
import '../utils/app_urls.dart';

/// Product card: cached image, theme-aware, RTL-safe. Isolated repaint for scroll performance.
class ProductCard extends StatelessWidget {
  const ProductCard({
    super.key,
    required this.product,
    this.onTap,
    this.onAddToCart,
  });

  final ProductModel product;
  final VoidCallback? onTap;
  final VoidCallback? onAddToCart;

  /// Builds full image URL. API may return full URL (http...) or storage path.
  static String? _effectivePhotoUrl(String? photoUrl) {
    if (photoUrl == null || photoUrl.trim().isEmpty) return null;
    final t = photoUrl.trim();
    if (t.startsWith('http://') || t.startsWith('https://')) return t;
    return imageUrl(t) ?? t;
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final colorScheme = theme.colorScheme;
    final effectiveUrl = _effectivePhotoUrl(product.firstPhotoUrl);

    return RepaintBoundary(
      child: Card(
        margin: EdgeInsets.zero,
        elevation: 0,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
        ),
        clipBehavior: Clip.antiAlias,
        color: colorScheme.surface,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          mainAxisSize: MainAxisSize.min,
          children: [
            GestureDetector(
              onTap: onTap,
              child: AspectRatio(
                aspectRatio: 4 / 5,
                child: Container(
                  decoration: BoxDecoration(
                    color: colorScheme.surfaceVariant.withValues(alpha: 0.4),
                  ),
                  child: Stack(
                    fit: StackFit.expand,
                    children: [
                      if (effectiveUrl != null)
                        AppNetworkImage(
                          url: effectiveUrl,
                          fit: BoxFit.cover,
                        )
                      else
                        Center(
                          child: Icon(
                            Icons.image_outlined,
                            size: 32,
                            color: colorScheme.outline,
                          ),
                        ),
                      if (!product.inStock)
                        Container(
                          color: colorScheme.surface.withValues(alpha: 0.85),
                          alignment: Alignment.center,
                          child: Chip(
                            label: Text(
                              AppStrings.tr('common.sold_out'),
                              style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w700),
                            ),
                            backgroundColor: colorScheme.errorContainer,
                          ),
                        ),
                      if (product.hasActiveDiscount)
                        Positioned.directional(
                          textDirection: Directionality.of(context),
                          start: 10,
                          top: 10,
                          child: Container(
                            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                            decoration: BoxDecoration(
                              color: colorScheme.error,
                              borderRadius: BorderRadius.circular(999),
                            ),
                            child: Text(
                              '-${product.discountPercentage?.toInt() ?? 0}%',
                              style: TextStyle(fontSize: 10, fontWeight: FontWeight.w700, color: colorScheme.onError),
                            ),
                          ),
                        ),
                    ],
                  ),
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(12),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisSize: MainAxisSize.min,
                children: [
                  if (product.vendor != null)
                    Text(
                      product.vendor!.storeName,
                      style: TextStyle(fontSize: 11, color: colorScheme.outline),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  if (product.vendor != null) const SizedBox(height: 4),
                  GestureDetector(
                    onTap: onTap,
                    child: Text(
                      product.name,
                      style: TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: colorScheme.onSurface),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                  const SizedBox(height: 6),
                  Row(
                    children: [
                      _Stars(rating: product.averageRating),
                      if (product.reviewCount > 0) ...[
                        const SizedBox(width: 6),
                        Text(
                          product.reviewCount == 1
                              ? '${product.reviewCount} ${AppStrings.tr('common.review_one')}'
                              : '${product.reviewCount} ${AppStrings.tr('common.reviews')}',
                          style: TextStyle(fontSize: 11, color: colorScheme.outline),
                        ),
                      ],
                    ],
                  ),
                  const SizedBox(height: 8),
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.baseline,
                    textBaseline: TextBaseline.alphabetic,
                    children: [
                      Text(
                        product.displayPrice.toStringAsFixed(0),
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.w900,
                          color: product.hasActiveDiscount ? colorScheme.error : colorScheme.onSurface,
                        ),
                      ),
                      Text(' SYP', style: TextStyle(fontSize: 11, color: colorScheme.outline)),
                      if (product.hasActiveDiscount && product.discountedPrice != null) ...[
                        const SizedBox(width: 6),
                        Text(
                          '${product.price.toStringAsFixed(0)} SYP',
                          style: TextStyle(
                            fontSize: 11,
                            color: colorScheme.outline,
                            decoration: TextDecoration.lineThrough,
                          ),
                        ),
                      ],
                    ],
                  ),
                  const SizedBox(height: 12),
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      onPressed: product.inStock ? onAddToCart : null,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: product.inStock ? colorScheme.inverseSurface : colorScheme.surfaceContainerHighest,
                        foregroundColor: product.inStock ? colorScheme.onInverseSurface : colorScheme.outline,
                      ),
                      child: Text(
                        product.inStock ? AppStrings.tr('common.add_to_cart') : AppStrings.tr('common.sold_out'),
                      ),
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
        (i) => Icon(
          i < r ? Icons.star : Icons.star_border,
          size: 14,
          color: Colors.amber,
        ),
      ),
    );
  }
}
