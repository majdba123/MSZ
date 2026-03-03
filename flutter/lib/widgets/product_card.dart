import 'package:flutter/material.dart';

import '../l10n/app_strings.dart';
import '../models/product_model.dart';
import '../utils/app_urls.dart';

class ProductCard extends StatelessWidget {
  const ProductCard({
    super.key,
    required this.product,
    this.onTap,
    this.onAddToCart,
    this.isFavourite = false,
    this.onToggleFavourite,
  });

  final ProductModel product;
  final VoidCallback? onTap;
  final VoidCallback? onAddToCart;
  final bool isFavourite;
  final VoidCallback? onToggleFavourite;

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return RepaintBoundary(
      child: Container(
        decoration: BoxDecoration(
          color: colorScheme.surface,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: colorScheme.outlineVariant.withValues(alpha: 0.5)),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.04),
              blurRadius: 8,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        clipBehavior: Clip.antiAlias,
        child: Material(
          color: Colors.transparent,
          child: InkWell(
            onTap: onTap,
            borderRadius: BorderRadius.circular(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                Expanded(
                  child: _ImageArea(
                    photoUrl: product.firstPhotoUrl,
                    inStock: product.inStock,
                    hasDiscount: product.hasActiveDiscount,
                    discountPercent: product.discountPercentage?.toInt() ?? 0,
                    isFavourite: isFavourite,
                    onToggleFavourite: onToggleFavourite,
                  ),
                ),
                _InfoArea(
                  product: product,
                  onAddToCart: onAddToCart,
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _ImageArea extends StatelessWidget {
  const _ImageArea({
    required this.photoUrl,
    required this.inStock,
    required this.hasDiscount,
    required this.discountPercent,
    required this.isFavourite,
    this.onToggleFavourite,
  });

  final String? photoUrl;
  final bool inStock;
  final bool hasDiscount;
  final int discountPercent;
  final bool isFavourite;
  final VoidCallback? onToggleFavourite;

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return Container(
      color: colorScheme.surfaceContainerHighest.withValues(alpha: 0.2),
      child: Stack(
        fit: StackFit.expand,
        children: [
          AppNetworkImage(url: photoUrl, fit: BoxFit.cover),
          if (!inStock)
            Container(
              color: colorScheme.surface.withValues(alpha: 0.85),
              alignment: Alignment.center,
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
                decoration: BoxDecoration(
                  color: colorScheme.errorContainer,
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Text(
                  AppStrings.tr('common.sold_out'),
                  style: TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.w700,
                    color: colorScheme.onErrorContainer,
                  ),
                ),
              ),
            ),
          if (hasDiscount && discountPercent > 0)
            Positioned.directional(
              textDirection: Directionality.of(context),
              start: 8,
              top: 8,
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [Color(0xFFDC2626), Color(0xFFEF4444)],
                  ),
                  borderRadius: BorderRadius.circular(8),
                  boxShadow: [
                    BoxShadow(
                      color: const Color(0xFFDC2626).withValues(alpha: 0.3),
                      blurRadius: 4,
                      offset: const Offset(0, 2),
                    ),
                  ],
                ),
                child: Text(
                  '-$discountPercent%',
                  style: const TextStyle(
                    fontSize: 10,
                    fontWeight: FontWeight.w800,
                    color: Colors.white,
                  ),
                ),
              ),
            ),
          if (onToggleFavourite != null)
            Positioned.directional(
              textDirection: Directionality.of(context),
              end: 6,
              top: 6,
              child: GestureDetector(
                onTap: onToggleFavourite,
                child: Container(
                  width: 34,
                  height: 34,
                  decoration: BoxDecoration(
                    color: colorScheme.surface.withValues(alpha: 0.9),
                    shape: BoxShape.circle,
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withValues(alpha: 0.08),
                        blurRadius: 4,
                        offset: const Offset(0, 1),
                      ),
                    ],
                  ),
                  alignment: Alignment.center,
                  child: Icon(
                    isFavourite ? Icons.favorite_rounded : Icons.favorite_border_rounded,
                    size: 18,
                    color: isFavourite ? const Color(0xFFEF4444) : colorScheme.outline,
                  ),
                ),
              ),
            ),
        ],
      ),
    );
  }
}

class _InfoArea extends StatelessWidget {
  const _InfoArea({required this.product, this.onAddToCart});

  final ProductModel product;
  final VoidCallback? onAddToCart;

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return Padding(
      padding: const EdgeInsets.fromLTRB(10, 10, 10, 10),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisSize: MainAxisSize.min,
        children: [
          if (product.vendor != null)
            Padding(
              padding: const EdgeInsets.only(bottom: 2),
              child: Text(
                product.vendor!.storeName,
                style: TextStyle(fontSize: 10, color: colorScheme.outline, fontWeight: FontWeight.w500),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),
            ),
          Text(
            product.name,
            style: TextStyle(
              fontSize: 13,
              fontWeight: FontWeight.w700,
              color: colorScheme.onSurface,
              height: 1.3,
            ),
            maxLines: 2,
            overflow: TextOverflow.ellipsis,
          ),
          const SizedBox(height: 4),
          _Stars(rating: product.averageRating, count: product.reviewCount),
          const SizedBox(height: 6),
          Row(
            children: [
              Text(
                product.displayPrice.toStringAsFixed(0),
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.w900,
                  color: product.hasActiveDiscount ? const Color(0xFFDC2626) : colorScheme.onSurface,
                  letterSpacing: -0.3,
                ),
              ),
              const SizedBox(width: 3),
              Text(
                'SYP',
                style: TextStyle(fontSize: 10, color: colorScheme.outline, fontWeight: FontWeight.w500),
              ),
              if (product.hasActiveDiscount && product.discountedPrice != null) ...[
                const SizedBox(width: 6),
                Flexible(
                  child: Text(
                    product.price.toStringAsFixed(0),
                    style: TextStyle(
                      fontSize: 11,
                      color: colorScheme.outline,
                      decoration: TextDecoration.lineThrough,
                      decorationColor: colorScheme.outline,
                    ),
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
              ],
            ],
          ),
          const SizedBox(height: 8),
          SizedBox(
            width: double.infinity,
            height: 34,
            child: FilledButton(
              onPressed: product.inStock ? onAddToCart : null,
              style: FilledButton.styleFrom(
                padding: const EdgeInsets.symmetric(horizontal: 8),
                textStyle: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                disabledBackgroundColor: colorScheme.surfaceContainerHighest,
                disabledForegroundColor: colorScheme.outline,
              ),
              child: Text(
                product.inStock
                    ? AppStrings.tr('common.add_to_cart')
                    : AppStrings.tr('common.sold_out'),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _Stars extends StatelessWidget {
  const _Stars({this.rating = 0, this.count = 0});

  final double rating;
  final int count;

  @override
  Widget build(BuildContext context) {
    final r = rating.clamp(0.0, 5.0).round();
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        ...List.generate(
          5,
          (i) => Icon(
            i < r ? Icons.star_rounded : Icons.star_outline_rounded,
            size: 13,
            color: const Color(0xFFF59E0B),
          ),
        ),
        if (count > 0) ...[
          const SizedBox(width: 3),
          Text(
            '($count)',
            style: TextStyle(
              fontSize: 10,
              color: Theme.of(context).colorScheme.outline,
            ),
          ),
        ],
      ],
    );
  }
}
