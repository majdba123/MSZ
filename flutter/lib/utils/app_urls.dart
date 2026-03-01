import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';

import '../config/api_config.dart';

/// Builds full URL for storage paths. Returns null if path is null/empty.
String? imageUrl(String? path) {
  if (path == null || path.isEmpty) return null;
  if (path.startsWith('http')) return path;
  return '$apiBaseUrl/storage/$path';
}

/// Cached, RTL-aware network image with placeholder and error. Use for all product/category/vendor images.
class AppNetworkImage extends StatelessWidget {
  const AppNetworkImage({
    super.key,
    required this.url,
    this.width,
    this.height,
    this.fit = BoxFit.cover,
    this.borderRadius,
  });

  final String? url;
  final double? width;
  final double? height;
  final BoxFit fit;
  final BorderRadius? borderRadius;

  @override
  Widget build(BuildContext context) {
    final effectiveUrl = url != null && url!.isNotEmpty ? (imageUrl(url) ?? url!) : null;
    final child = effectiveUrl != null && effectiveUrl.isNotEmpty
        ? CachedNetworkImage(
            imageUrl: effectiveUrl,
            width: width,
            height: height,
            fit: fit,
            memCacheWidth: width != null ? (width! * 2).toInt() : null,
            memCacheHeight: height != null ? (height! * 2).toInt() : null,
            placeholder: (_, __) => _Placeholder(width: width, height: height),
            errorWidget: (_, __, ___) => _Placeholder(width: width, height: height),
          )
        : _Placeholder(width: width, height: height);

    if (borderRadius != null) {
      return ClipRRect(borderRadius: borderRadius!, child: child);
    }
    return child;
  }
}

class _Placeholder extends StatelessWidget {
  const _Placeholder({this.width, this.height});

  final double? width;
  final double? height;

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final content = Container(
      width: width,
      height: height,
      color: colorScheme.surfaceContainerHighest.withValues(alpha: 0.5),
      alignment: Alignment.center,
      child: Icon(
        Icons.image_outlined,
        size: width != null && height != null
            ? (width! < height! ? width! * 0.4 : height! * 0.4).clamp(24.0, 64.0)
            : 48,
        color: colorScheme.outline.withValues(alpha: 0.5),
      ),
    );
    if (width == null && height == null) {
      return SizedBox.expand(child: content);
    }
    return content;
  }
}
