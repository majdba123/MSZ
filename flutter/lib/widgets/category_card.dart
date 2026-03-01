import 'package:flutter/material.dart';

import '../models/category_model.dart';
import '../utils/app_urls.dart';

/// Category card matching Blade: logo/icon, name, subcount, optional subcategory chips.
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
    final logo = category.logo;
    final url = imageUrl(logo);
    final subs = category.subcategories;

    return Card(
      clipBehavior: Clip.antiAlias,
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
                      color: const Color(0xFFFFEDD5),
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: url != null && url.isNotEmpty
                        ? ClipRRect(
                            borderRadius: BorderRadius.circular(16),
                            child: Image.network(url, fit: BoxFit.cover, errorBuilder: (_, __, ___) => const _Icon()),
                          )
                        : const _Icon(),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          category.name,
                          style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: Color(0xFF111827)),
                        ),
                        Text(
                          '${subs.length} subcategor${subs.length == 1 ? 'y' : 'ies'}',
                          style: const TextStyle(fontSize: 12, color: Color(0xFF9CA3AF)),
                        ),
                      ],
                    ),
                  ),
                  const Icon(Icons.chevron_right, color: Color(0xFFD1D5DB)),
                ],
              ),
            ),
            if (subs.isNotEmpty)
              Container(
                padding: const EdgeInsets.fromLTRB(16, 12, 16, 12),
                color: const Color(0xFFF9FAFB),
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
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(color: const Color(0xFFE5E7EB)),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            if (subUrl != null && subUrl.isNotEmpty)
                              Padding(
                                padding: const EdgeInsets.only(right: 4),
                                child: Image.network(subUrl, width: 14, height: 14, fit: BoxFit.cover, errorBuilder: (_, __, ___) => const SizedBox.shrink()),
                              ),
                            Text(s.name, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w500, color: Color(0xFF6B7280))),
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
    );
  }
}

class _Icon extends StatelessWidget {
  const _Icon();

  @override
  Widget build(BuildContext context) {
    return const Icon(Icons.category_outlined, size: 32, color: Color(0xFFF97316));
  }
}
