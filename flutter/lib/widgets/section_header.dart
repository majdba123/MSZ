import 'package:flutter/material.dart';

/// Matches Blade section headers: badge + title + subtitle.
class SectionHeader extends StatelessWidget {
  const SectionHeader({
    super.key,
    required this.badge,
    required this.title,
    this.subtitle,
    this.action,
  });

  final String badge;
  final String title;
  final String? subtitle;
  final Widget? action;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final colorScheme = theme.colorScheme;
    return Padding(
      padding: const EdgeInsets.only(bottom: 24),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.end,
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                  decoration: BoxDecoration(
                    color: _badgeColor(badge, colorScheme),
                    borderRadius: BorderRadius.circular(999),
                  ),
                  child: Text(
                    badge.toUpperCase(),
                    style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.w700,
                      letterSpacing: 1.2,
                      color: _badgeTextColor(badge, colorScheme),
                    ),
                  ),
                ),
                const SizedBox(height: 10),
                Text(
                  title,
                  style: TextStyle(
                    fontSize: 22,
                    fontWeight: FontWeight.w800,
                    color: colorScheme.onSurface,
                  ),
                ),
                if (subtitle != null) ...[
                  const SizedBox(height: 4),
                  Text(
                    subtitle!,
                    style: TextStyle(fontSize: 14, color: colorScheme.outline),
                  ),
                ],
              ],
            ),
          ),
          if (action != null) action!,
        ],
      ),
    );
  }

  Color _badgeColor(String b, ColorScheme colorScheme) {
    final lower = b.toLowerCase();
    if (lower.contains('category') || lower.contains('shop')) return colorScheme.primaryContainer.withValues(alpha: 0.6);
    if (lower.contains('browse') || lower.contains('sub')) return colorScheme.tertiaryContainer.withValues(alpha: 0.6);
    if (lower.contains('store')) return colorScheme.secondaryContainer.withValues(alpha: 0.6);
    if (lower.contains('new') || lower.contains('bestseller') || lower.contains('favorited')) return colorScheme.primaryContainer.withValues(alpha: 0.5);
    return colorScheme.surfaceContainerHighest;
  }

  Color _badgeTextColor(String b, ColorScheme colorScheme) {
    final lower = b.toLowerCase();
    if (lower.contains('category') || lower.contains('shop')) return colorScheme.onPrimaryContainer;
    if (lower.contains('browse') || lower.contains('sub')) return colorScheme.onTertiaryContainer;
    if (lower.contains('store')) return colorScheme.onSecondaryContainer;
    if (lower.contains('new') || lower.contains('bestseller') || lower.contains('favorited')) return colorScheme.onPrimaryContainer;
    return colorScheme.onSurfaceVariant;
  }
}
