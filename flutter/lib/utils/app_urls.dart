import 'package:flutter/material.dart';

import '../config/api_config.dart';

/// Resolves a storage path or full URL into an absolute image URL.
/// Idempotent: calling it multiple times on the same input always returns the
/// same result.
String? imageUrl(String? path) {
  if (path == null || path.trim().isEmpty) return null;
  final p = path.trim();
  if (p.startsWith('http://') || p.startsWith('https://')) return p;

  String clean = p;
  if (clean.startsWith('/storage/')) {
    clean = clean.substring(9);
  } else if (clean.startsWith('storage/')) {
    clean = clean.substring(8);
  } else if (clean.startsWith('/')) {
    clean = clean.substring(1);
  }
  return '$apiBaseUrl/storage/$clean';
}

/// Network image widget that works on both web and mobile.
/// Uses [Image.network] with [WebHtmlElementStrategy.prefer] on web
/// to bypass CORS restrictions when loading cross-origin images.
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
    final resolved = imageUrl(url);
    if (resolved == null || resolved.isEmpty) {
      return _Placeholder(width: width, height: height);
    }

    Widget child = Image.network(
      resolved,
      width: width,
      height: height,
      fit: fit,
      webHtmlElementStrategy: WebHtmlElementStrategy.prefer,
      loadingBuilder: (context, child, loadingProgress) {
        if (loadingProgress == null) return child;
        return _Placeholder(width: width, height: height);
      },
      errorBuilder: (context, error, stackTrace) {
        return _ErrorPlaceholder(width: width, height: height);
      },
    );

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
      color: colorScheme.surfaceContainerHighest.withValues(alpha: 0.3),
      alignment: Alignment.center,
      child: Icon(
        Icons.image_outlined,
        size: _iconSize,
        color: colorScheme.outline.withValues(alpha: 0.4),
      ),
    );
    if (width == null && height == null) {
      return SizedBox.expand(child: content);
    }
    return content;
  }

  double get _iconSize {
    if (width != null && height != null) {
      return (width! < height! ? width! * 0.35 : height! * 0.35).clamp(20.0, 48.0);
    }
    return 36;
  }
}

class _ErrorPlaceholder extends StatelessWidget {
  const _ErrorPlaceholder({this.width, this.height});

  final double? width;
  final double? height;

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final content = Container(
      width: width,
      height: height,
      color: colorScheme.surfaceContainerHighest.withValues(alpha: 0.3),
      alignment: Alignment.center,
      child: Icon(
        Icons.broken_image_outlined,
        size: _iconSize,
        color: colorScheme.outline.withValues(alpha: 0.4),
      ),
    );
    if (width == null && height == null) {
      return SizedBox.expand(child: content);
    }
    return content;
  }

  double get _iconSize {
    if (width != null && height != null) {
      return (width! < height! ? width! * 0.35 : height! * 0.35).clamp(20.0, 48.0);
    }
    return 36;
  }
}
