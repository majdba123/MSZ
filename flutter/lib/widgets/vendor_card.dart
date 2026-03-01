import 'package:flutter/material.dart';

import '../models/vendor_model.dart';
import '../utils/app_urls.dart';

/// Vendor card matching Blade: banner/logo, store name, by user, description, arrow.
class VendorCard extends StatelessWidget {
  const VendorCard({
    super.key,
    required this.vendor,
    this.onTap,
  });

  final VendorModel vendor;
  final VoidCallback? onTap;

  static const _gradients = [
    [Color(0xFFF97316), Color(0xFFEA580C)],
    [Color(0xFFA855F7), Color(0xFF9333EA)],
    [Color(0xFF3B82F6), Color(0xFF2563EB)],
    [Color(0xFF10B981), Color(0xFF059669)],
    [Color(0xFFEC4899), Color(0xFFDB2777)],
  ];

  @override
  Widget build(BuildContext context) {
    final logoUrl = vendor.logo != null && vendor.logo!.startsWith('http') ? vendor.logo! : imageUrl(vendor.logo);
    final hasLogo = logoUrl != null && logoUrl.isNotEmpty;
    final initial = vendor.storeName.isNotEmpty ? vendor.storeName[0].toUpperCase() : 'S';
    final gradient = _gradients[vendor.id % _gradients.length];

    return Card(
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: onTap,
        child: SizedBox(
          width: 280,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            mainAxisSize: MainAxisSize.min,
            children: [
              SizedBox(
                height: 140,
                child: Stack(
                  fit: StackFit.expand,
                  children: [
                    if (hasLogo)
                      Image.network(
                        logoUrl,
                        fit: BoxFit.cover,
                        errorBuilder: (_, __, ___) => _GradientBox(colors: gradient, child: Text(initial, style: const TextStyle(fontSize: 48, fontWeight: FontWeight.w900, color: Colors.white70))),
                      )
                    else
                      _GradientBox(
                        colors: gradient,
                        child: Text(initial, style: const TextStyle(fontSize: 48, fontWeight: FontWeight.w900, color: Colors.white70)),
                      ),
                    Container(
                      decoration: BoxDecoration(
                        gradient: LinearGradient(
                          begin: Alignment.topCenter,
                          end: Alignment.bottomCenter,
                          colors: [Colors.transparent, Colors.black54],
                        ),
                      ),
                    ),
                    Positioned(
                      left: 16,
                      right: 16,
                      bottom: 12,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            vendor.storeName,
                            style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: Colors.white),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                          if (vendor.user != null)
                            Text(
                              'by ${vendor.user!.name}',
                              style: const TextStyle(fontSize: 11, color: Colors.white70),
                            ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
              Padding(
                padding: const EdgeInsets.all(12),
                child: Row(
                  children: [
                    Expanded(
                      child: Text(
                        vendor.description ?? 'Explore our products',
                        style: const TextStyle(fontSize: 12, color: Color(0xFF6B7280)),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                    const Icon(Icons.arrow_forward_ios, size: 12, color: Color(0xFFD1D5DB)),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _GradientBox extends StatelessWidget {
  final List<Color> colors;
  final Widget child;

  const _GradientBox({required this.colors, required this.child});

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: colors,
        ),
      ),
      alignment: Alignment.center,
      child: child,
    );
  }
}
