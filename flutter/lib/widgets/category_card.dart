import 'package:flutter/material.dart';

import '../models/category_model.dart';
import '../utils/app_urls.dart';

/// Category card: cached image, theme-aware, RTL-safe.
class CategoryCard extends StatelessWidget {
  const CategoryCard({
    super.key,
    required this.category,
    this.onTap,
  });

  final CategoryModel category;
  final VoidCallback? onTap;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final colorScheme = theme.colorScheme;
    final logoUrl = imageUrl(category.logo);
    final subs = category.subcategories;

    return RepaintBoundary(
      child: Card(
        clipBehavior: Clip.antiAlias,
        color: colorScheme.surface,
        child: InkWell(
          onTap: onTap,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            mainAxisSize: MainAxisSize.min,
            children: [
              Padding(
                padding: const EdgeInsets.all(16),
                child: Row(
                  children: [
                    Container(
                      width: 64,
                      height: 64,
                      decoration: BoxDecoration(
                        color: colorScheme.primaryContainer.withValues(alpha: 0.5),
                        borderRadius: BorderRadius.circular(16),
                      ),
                      clipBehavior: Clip.antiAlias,
                      child: logoUrl != null && logoUrl.isNotEmpty
                          ? AppNetworkImage(
                              url: logoUrl,
                              width: 64,
                              height: 64,
                              fit: BoxFit.cover,
                              borderRadius: BorderRadius.circular(16),
                            )
                          : Icon(Icons.category_outlined, size: 32, color: colorScheme.primary),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            category.name,
                            style: TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: colorScheme.onSurface),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                          Text(
                            subs.length == 1 ? '${subs.length} subcategory' : '${subs.length} subcategories',
                            style: TextStyle(fontSize: 12, color: colorScheme.outline),
                          ),
                        ],
                      ),
                    ),
                    Icon(Icons.chevron_right, color: colorScheme.outline),
                  ],
                ),
              ),
              if (subs.isNotEmpty)
                Container(
                  padding: const EdgeInsets.fromLTRB(16, 12, 16, 12),
                  color: colorScheme.surfaceContainerHighest.withValues(alpha: 0.5),
                  child: Wrap(
                    spacing: 6,
                    runSpacing: 6,
                    children: subs.take(4).map((s) {
                      final subUrl = imageUrl(s.image);
                      return InkWell(
                        onTap: () {},
                        borderRadius: BorderRadius.circular(8),
                        child: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                          decoration: BoxDecoration(
                            color: colorScheme.surface,
                            borderRadius: BorderRadius.circular(8),
                            border: Border.all(color: colorScheme.outlineVariant),
                          ),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              if (subUrl != null && subUrl.isNotEmpty)
                                Padding(
                                  padding: const EdgeInsetsDirectional.only(end: 4),
                                  child: AppNetworkImage(
                                    url: subUrl,
                                    width: 14,
                                    height: 14,
                                    fit: BoxFit.cover,
                                    borderRadius: BorderRadius.circular(4),
                                  ),
                                ),
                              Flexible(
                                child: Text(
                                  s.name,
                                  style: TextStyle(fontSize: 11, fontWeight: FontWeight.w500, color: colorScheme.onSurfaceVariant),
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ),
                            ],
                          ),
                        ),
                      );
                    }).toList(),
                  ),
                ),
            ],
          ),
        ),
      ),
    );
  }
}
