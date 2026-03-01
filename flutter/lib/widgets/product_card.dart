import 'package:flutter/material.dart';

import '../models/product_model.dart';
import '../utils/app_urls.dart';

/// One product card matching Blade design: image, vendor, name, stars, price, Add to Cart.
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

  @override
  Widget build(BuildContext context) {
    final photoUrl = product.firstPhotoUrl ?? '';
    final imageUrlRes = imageUrl(photoUrl.isEmpty ? null : photoUrl);
    final effectiveUrl = imageUrlRes ?? photoUrl;

    return Card(
      clipBehavior: Clip.antiAlias,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          GestureDetector(
            onTap: onTap,
            child: AspectRatio(
              aspectRatio: 4 / 5,
              child: Stack(
                fit: StackFit.expand,
                children: [
                  Container(
                    color: const Color(0xFFF9FAFB),
                    child: effectiveUrl.isNotEmpty
                        ? Image.network(
                            effectiveUrl,
                            fit: BoxFit.contain,
                            errorBuilder: (_, __, ___) => const _Placeholder(),
                          )
                        : const _Placeholder(),
                  ),
                  if (!product.inStock)
                    Container(
                      color: Colors.white70,
                      alignment: Alignment.center,
                      child: const Chip(
                        label: Text('Sold Out', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700)),
                        backgroundColor: Color(0xFFFEE2E2),
                      ),
                    ),
                  if (product.hasActiveDiscount)
                    Positioned(
                      left: 10,
                      top: 10,
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: Colors.red,
                          borderRadius: BorderRadius.circular(999),
                        ),
                        child: Text(
                          '-${product.discountPercentage?.toInt() ?? 0}%',
                          style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w700, color: Colors.white),
                        ),
                      ),
                    ),
                ],
              ),
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(12),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                if (product.vendor != null)
                  Text(
                    product.vendor!.storeName,
                    style: const TextStyle(fontSize: 11, color: Color(0xFF9CA3AF)),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                const SizedBox(height: 4),
                GestureDetector(
                  onTap: onTap,
                  child: Text(
                    product.name,
                    style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: Color(0xFF111827)),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
                const SizedBox(height: 6),
                Row(
                  children: [
                    _Stars(rating: product.averageRating),
                    const SizedBox(width: 6),
                    if (product.reviewCount > 0)
                      Text(
                        '${product.reviewCount} review${product.reviewCount == 1 ? '' : 's'}',
                        style: const TextStyle(fontSize: 11, color: Color(0xFF9CA3AF)),
                      ),
                  ],
                ),
                const SizedBox(height: 8),
                Row(
                  crossAxisAlignment: CrossAxisAlignment.baseline,
                  textBaseline: TextBaseline.alphabetic,
                  children: [
                    Text(
                      '${product.displayPrice.toStringAsFixed(0)}',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.w900,
                        color: product.hasActiveDiscount ? Colors.red : const Color(0xFF111827),
                      ),
                    ),
                    const Text(' SYP', style: TextStyle(fontSize: 11, color: Color(0xFF9CA3AF))),
                    if (product.hasActiveDiscount && product.discountedPrice != null) ...[
                      const SizedBox(width: 6),
                      Text(
                        '${product.price.toStringAsFixed(0)} SYP',
                        style: const TextStyle(fontSize: 11, color: Color(0xFF9CA3AF), decoration: TextDecoration.lineThrough),
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
                      backgroundColor: product.inStock ? const Color(0xFF111827) : const Color(0xFFF3F4F6),
                      foregroundColor: product.inStock ? Colors.white : const Color(0xFF9CA3AF),
                    ),
                    child: Text(product.inStock ? 'Add to Cart' : 'Sold Out'),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _Stars extends StatelessWidget {
  final double rating;

  const _Stars({this.rating = 0});

  @override
  Widget build(BuildContext context) {
    final r = rating.clamp(0, 5).round();
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: List.generate(5, (i) => Icon(i < r ? Icons.star : Icons.star_border, size: 14, color: Colors.amber)),
    );
  }
}

class _Placeholder extends StatelessWidget {
  const _Placeholder();

  @override
  Widget build(BuildContext context) {
    return const Center(child: Icon(Icons.image_outlined, size: 48, color: Color(0xFFE5E7EB)));
  }
}
