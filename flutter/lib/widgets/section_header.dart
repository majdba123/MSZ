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
                    color: _badgeColor(badge),
                    borderRadius: BorderRadius.circular(999),
                  ),
                  child: Text(
                    badge.toUpperCase(),
                    style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.w700,
                      letterSpacing: 1.2,
                      color: _badgeTextColor(badge),
                    ),
                  ),
                ),
                const SizedBox(height: 10),
                Text(
                  title,
                  style: const TextStyle(
                    fontSize: 22,
                    fontWeight: FontWeight.w800,
                    color: Color(0xFF111827),
                  ),
                ),
                if (subtitle != null) ...[
                  const SizedBox(height: 4),
                  Text(
                    subtitle!,
                    style: const TextStyle(fontSize: 14, color: Color(0xFF6B7280)),
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

  Color _badgeColor(String b) {
    final lower = b.toLowerCase();
    if (lower.contains('category') || lower.contains('shop')) return const Color(0xFFFFEDD5);
    if (lower.contains('browse') || lower.contains('sub')) return const Color(0xFFDBEAFE);
    if (lower.contains('store')) return const Color(0xFFF3E8FF);
    if (lower.contains('new') || lower.contains('bestseller') || lower.contains('favorited')) return const Color(0xFFD1FAE5);
    return const Color(0xFFFFEDD5);
  }

  Color _badgeTextColor(String b) {
    final lower = b.toLowerCase();
    if (lower.contains('category') || lower.contains('shop')) return const Color(0xFFEA580C);
    if (lower.contains('browse') || lower.contains('sub')) return const Color(0xFF2563EB);
    if (lower.contains('store')) return const Color(0xFF9333EA);
    if (lower.contains('new') || lower.contains('bestseller') || lower.contains('favorited')) return const Color(0xFF059669);
    return const Color(0xFFEA580C);
  }
}
