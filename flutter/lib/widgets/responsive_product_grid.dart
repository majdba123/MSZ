import 'package:flutter/material.dart';

/// Computes a [SliverGridDelegate] that adapts column count and aspect ratio
/// based on available width. Cards get ~170-200px wide and tall enough to
/// hold the image + compact info without overflow.
SliverGridDelegate responsiveProductGrid(BuildContext context) {
  final width = MediaQuery.of(context).size.width;
  final columns = width < 360 ? 2 : (width < 600 ? 2 : (width < 900 ? 3 : 4));
  const spacing = 10.0;
  final itemWidth = (width - 32 - (columns - 1) * spacing) / columns;
  final itemHeight = itemWidth * 1.55;

  return SliverGridDelegateWithFixedCrossAxisCount(
    crossAxisCount: columns,
    childAspectRatio: itemWidth / itemHeight,
    crossAxisSpacing: spacing,
    mainAxisSpacing: spacing,
  );
}
