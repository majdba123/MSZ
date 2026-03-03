import 'dart:convert';

import 'package:flutter/material.dart';

import '../l10n/app_strings.dart';
import '../models/product_model.dart';
import '../models/review_model.dart';
import '../services/api_client.dart';
import '../services/auth_service.dart';
import '../services/cart_service.dart';
import '../services/client_api_service.dart';
import '../services/favourite_service.dart';
import '../theme/app_theme.dart';
import '../utils/app_urls.dart';

class ProductDetailScreen extends StatefulWidget {
  const ProductDetailScreen({
    super.key,
    required this.productId,
    this.cartService,
    this.favouriteService,
    this.authService,
  });

  final int productId;
  final CartService? cartService;
  final FavouriteService? favouriteService;
  final AuthService? authService;

  @override
  State<ProductDetailScreen> createState() => _ProductDetailScreenState();
}

class _ProductDetailScreenState extends State<ProductDetailScreen> {
  ProductDetailModel? _product;
  bool _loading = true;
  String? _error;
  int _currentPhoto = 0;
  final _pageCtrl = PageController();
  final _thumbScrollCtrl = ScrollController();

  List<ReviewModel> _reviews = [];
  bool _loadingReviews = false;
  int _reviewPage = 1;
  int _reviewLastPage = 1;

  final _reviewCtrl = TextEditingController();
  int _reviewRating = 5;
  bool _submittingReview = false;

  @override
  void initState() {
    super.initState();
    _load();
    widget.favouriteService?.addListener(_onFavChanged);
  }

  @override
  void dispose() {
    widget.favouriteService?.removeListener(_onFavChanged);
    _reviewCtrl.dispose();
    _pageCtrl.dispose();
    _thumbScrollCtrl.dispose();
    super.dispose();
  }

  void _onFavChanged() {
    if (mounted) setState(() {});
  }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final api = ClientApiService();
      final product = await api.getProduct(widget.productId);
      if (!mounted) return;
      setState(() {
        _product = product;
        _loading = false;
        if (product == null) _error = 'Product not found';
      });
      if (product != null) _loadReviews();
    } catch (e) {
      if (!mounted) return;
      setState(() { _loading = false; _error = e.toString(); });
    }
  }

  Future<void> _loadReviews({bool reset = true}) async {
    if (reset) _reviewPage = 1;
    setState(() => _loadingReviews = true);
    try {
      final res = await ApiClient().get('/api/products/${widget.productId}/reviews?page=$_reviewPage&per_page=10');
      if (res.statusCode == 200 && mounted) {
        final data = jsonDecode(res.body) as Map<String, dynamic>;
        final list = (data['data'] as List<dynamic>? ?? []).map((e) => ReviewModel.fromJson(e as Map<String, dynamic>)).toList();
        final meta = data['meta'] as Map<String, dynamic>? ?? {};
        setState(() {
          _reviews = reset ? list : [..._reviews, ...list];
          _reviewLastPage = (meta['last_page'] as num?)?.toInt() ?? 1;
          _loadingReviews = false;
        });
      }
    } catch (_) {
      if (mounted) setState(() => _loadingReviews = false);
    }
  }

  Future<void> _submitReview() async {
    if (_reviewCtrl.text.trim().isEmpty && _reviewRating < 1) return;
    setState(() => _submittingReview = true);
    try {
      final body = <String, dynamic>{'rating': _reviewRating};
      if (_reviewCtrl.text.trim().isNotEmpty) body['body'] = _reviewCtrl.text.trim();
      await ApiClient().post('/api/products/${widget.productId}/reviews', body);
      if (!mounted) return;
      _reviewCtrl.clear();
      _reviewRating = 5;
      setState(() => _submittingReview = false);
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(AppStrings.tr('product.review_submitted'))));
      _loadReviews();
      _load();
    } catch (e) {
      if (mounted) {
        setState(() => _submittingReview = false);
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.toString())));
      }
    }
  }

  Future<void> _deleteReview(int reviewId) async {
    try {
      await ApiClient().delete('/api/products/${widget.productId}/reviews/$reviewId');
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(AppStrings.tr('product.review_deleted'))));
      _loadReviews();
    } catch (_) {}
  }

  void _addToCart() {
    if (_product == null) return;
    final p = _product!;
    widget.cartService?.addItem(productId: p.id, name: p.name, price: p.displayPrice, photo: p.firstPhotoUrl);
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(AppStrings.tr('common.added_to_cart')), duration: const Duration(seconds: 1)),
    );
  }

  void _toggleFavourite() async {
    if (widget.favouriteService == null) return;
    final wasFav = widget.favouriteService!.isFavourite(widget.productId);
    await widget.favouriteService!.toggle(widget.productId);
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(wasFav ? AppStrings.tr('product.removed_from_favourites') : AppStrings.tr('product.added_to_favourites')),
        duration: const Duration(seconds: 1),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    if (_loading && _product == null) {
      return Scaffold(
        appBar: AppBar(),
        body: const Center(child: CircularProgressIndicator()),
      );
    }
    if (_error != null && _product == null) {
      return Scaffold(
        appBar: AppBar(),
        body: Center(
          child: Padding(
            padding: const EdgeInsets.all(32),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(
                  width: 72,
                  height: 72,
                  decoration: BoxDecoration(
                    color: colorScheme.errorContainer.withValues(alpha: 0.3),
                    shape: BoxShape.circle,
                  ),
                  child: Icon(Icons.error_outline_rounded, size: 36, color: colorScheme.error),
                ),
                const SizedBox(height: 16),
                Text(_error!, style: TextStyle(color: colorScheme.error, fontSize: 14), textAlign: TextAlign.center),
                const SizedBox(height: 20),
                FilledButton.icon(
                  onPressed: _load,
                  icon: const Icon(Icons.refresh_rounded, size: 18),
                  label: Text(AppStrings.tr('common.retry')),
                ),
              ],
            ),
          ),
        ),
      );
    }

    final p = _product!;
    final allPhotos = _buildPhotoUrls(p);
    final isFav = widget.favouriteService?.isFavourite(p.id) ?? false;

    return Scaffold(
      backgroundColor: colorScheme.surface,
      appBar: AppBar(
        title: Text(p.name, overflow: TextOverflow.ellipsis),
        actions: [
          if (widget.favouriteService != null)
            IconButton(
              icon: Icon(
                isFav ? Icons.favorite_rounded : Icons.favorite_border_rounded,
                color: isFav ? const Color(0xFFEF4444) : null,
              ),
              onPressed: _toggleFavourite,
            ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: _load,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              _buildGallery(context, p, allPhotos),
              _buildProductHeader(context, p),
              _buildPriceSection(context, p),
              _buildDetailsSection(context, p),
              if (p.description != null && p.description!.trim().isNotEmpty)
                _buildDescriptionSection(context, p),
              _buildReviewsSection(context),
              const SizedBox(height: 100),
            ],
          ),
        ),
      ),
      bottomNavigationBar: _buildBottomBar(context, p),
    );
  }

  List<String?> _buildPhotoUrls(ProductDetailModel p) {
    final urls = <String?>[];
    for (final photo in p.photos) {
      if (photo.url.isNotEmpty) urls.add(photo.url);
    }
    if (urls.isEmpty && p.firstPhotoUrl != null) urls.add(p.firstPhotoUrl);
    if (urls.isEmpty) urls.add(null);
    return urls;
  }

  void _goToPhoto(int index) {
    if (index == _currentPhoto) return;
    _pageCtrl.animateToPage(index, duration: const Duration(milliseconds: 300), curve: Curves.easeInOut);
  }

  void _scrollThumbIntoView(int index, int total) {
    if (!_thumbScrollCtrl.hasClients || total <= 1) return;
    const thumbW = 64.0 + 8.0;
    final target = (index * thumbW) - (_thumbScrollCtrl.position.viewportDimension / 2) + (thumbW / 2);
    _thumbScrollCtrl.animateTo(
      target.clamp(0.0, _thumbScrollCtrl.position.maxScrollExtent),
      duration: const Duration(milliseconds: 250),
      curve: Curves.easeOut,
    );
  }

  void _openFullscreenGallery(List<String?> photos, int initial) {
    Navigator.of(context).push(
      PageRouteBuilder(
        opaque: false,
        barrierColor: Colors.black,
        pageBuilder: (context, anim, secondAnim) => _FullscreenGallery(
          photos: photos,
          initialIndex: initial,
        ),
        transitionsBuilder: (context, anim, secondAnim, child) {
          return FadeTransition(opacity: anim, child: child);
        },
      ),
    );
  }

  Widget _buildGallery(BuildContext context, ProductDetailModel p, List<String?> photos) {
    final colorScheme = Theme.of(context).colorScheme;
    final screenWidth = MediaQuery.of(context).size.width;
    final mainHeight = screenWidth * 0.8;

    return Column(
      children: [
        GestureDetector(
          onTap: () => _openFullscreenGallery(photos, _currentPhoto),
          child: SizedBox(
            height: mainHeight,
            child: Stack(
              children: [
                Container(
                  color: colorScheme.surfaceContainerHighest.withValues(alpha: 0.12),
                  child: photos.length > 1
                      ? PageView.builder(
                          controller: _pageCtrl,
                          itemCount: photos.length,
                          onPageChanged: (i) {
                            setState(() => _currentPhoto = i);
                            _scrollThumbIntoView(i, photos.length);
                          },
                          itemBuilder: (_, i) => AppNetworkImage(url: photos[i], fit: BoxFit.contain),
                        )
                      : AppNetworkImage(url: photos.first, fit: BoxFit.contain),
                ),
                if (photos.length > 1)
                  Positioned(
                    top: 12,
                    right: 12,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                      decoration: BoxDecoration(
                        color: Colors.black.withValues(alpha: 0.55),
                        borderRadius: BorderRadius.circular(20),
                      ),
                      child: Text(
                        '${_currentPhoto + 1} / ${photos.length}',
                        style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: Colors.white),
                      ),
                    ),
                  ),
                if (p.hasActiveDiscount && (p.discountPercentage?.toInt() ?? 0) > 0)
                  Positioned(
                    top: 12,
                    left: 12,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(colors: [Color(0xFFDC2626), Color(0xFFEF4444)]),
                        borderRadius: BorderRadius.circular(10),
                        boxShadow: [
                          BoxShadow(color: const Color(0xFFDC2626).withValues(alpha: 0.35), blurRadius: 8, offset: const Offset(0, 3)),
                        ],
                      ),
                      child: Text(
                        '-${p.discountPercentage!.toInt()}%',
                        style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w800, color: Colors.white),
                      ),
                    ),
                  ),
                Positioned(
                  bottom: 12,
                  right: 12,
                  child: Container(
                    width: 36,
                    height: 36,
                    decoration: BoxDecoration(
                      color: colorScheme.surface.withValues(alpha: 0.85),
                      shape: BoxShape.circle,
                      boxShadow: [
                        BoxShadow(color: Colors.black.withValues(alpha: 0.1), blurRadius: 6),
                      ],
                    ),
                    child: Icon(Icons.fullscreen_rounded, size: 20, color: colorScheme.onSurface),
                  ),
                ),
              ],
            ),
          ),
        ),
        if (photos.length > 1)
          Container(
            height: 80,
            padding: const EdgeInsets.symmetric(vertical: 8),
            child: ListView.builder(
              controller: _thumbScrollCtrl,
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 16),
              itemCount: photos.length,
              itemBuilder: (context, i) {
                final isSelected = i == _currentPhoto;
                return GestureDetector(
                  onTap: () => _goToPhoto(i),
                  child: AnimatedContainer(
                    duration: const Duration(milliseconds: 200),
                    width: 64,
                    height: 64,
                    margin: const EdgeInsetsDirectional.only(end: 8),
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(
                        color: isSelected ? AppTheme.brandPrimary : colorScheme.outlineVariant.withValues(alpha: 0.4),
                        width: isSelected ? 2.5 : 1,
                      ),
                      boxShadow: isSelected
                          ? [BoxShadow(color: AppTheme.brandPrimary.withValues(alpha: 0.25), blurRadius: 8)]
                          : null,
                    ),
                    clipBehavior: Clip.antiAlias,
                    child: AnimatedOpacity(
                      duration: const Duration(milliseconds: 200),
                      opacity: isSelected ? 1.0 : 0.6,
                      child: AppNetworkImage(url: photos[i], fit: BoxFit.cover),
                    ),
                  ),
                );
              },
            ),
          ),
      ],
    );
  }

  Widget _buildProductHeader(BuildContext context, ProductDetailModel p) {
    final colorScheme = Theme.of(context).colorScheme;

    return Padding(
      padding: const EdgeInsets.fromLTRB(20, 20, 20, 0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (p.vendor != null)
            GestureDetector(
              onTap: () => Navigator.of(context).pushNamed('/vendor/${p.vendor!.id}'),
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                decoration: BoxDecoration(
                  color: AppTheme.brandPrimary.withValues(alpha: 0.08),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Icon(Icons.storefront_rounded, size: 14, color: AppTheme.brandPrimary),
                    const SizedBox(width: 6),
                    Flexible(
                      child: Text(
                        p.vendor!.storeName,
                        style: const TextStyle(fontSize: 13, color: AppTheme.brandPrimary, fontWeight: FontWeight.w700),
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                    const SizedBox(width: 4),
                    Icon(Icons.chevron_right_rounded, size: 16, color: AppTheme.brandPrimary.withValues(alpha: 0.6)),
                  ],
                ),
              ),
            ),
          const SizedBox(height: 12),
          Text(
            p.name,
            style: TextStyle(
              fontSize: 24,
              fontWeight: FontWeight.w900,
              color: colorScheme.onSurface,
              letterSpacing: -0.5,
              height: 1.2,
            ),
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              _buildStars(p.averageRating),
              const SizedBox(width: 8),
              Text(
                p.averageRating.toStringAsFixed(1),
                style: TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: colorScheme.onSurface),
              ),
              if (p.reviewCount > 0) ...[
                const SizedBox(width: 6),
                Text(
                  '(${p.reviewCount} ${AppStrings.tr('common.reviews')})',
                  style: TextStyle(fontSize: 13, color: colorScheme.outline),
                ),
              ],
            ],
          ),
          if (p.category != null || p.subcategory != null) ...[
            const SizedBox(height: 12),
            Wrap(
              spacing: 8,
              runSpacing: 6,
              children: [
                if (p.category != null)
                  _buildCategoryChip(
                    colorScheme,
                    p.category!.name,
                    Icons.category_rounded,
                    () => Navigator.of(context).pushNamed('/category/${p.category!.id}'),
                  ),
                if (p.subcategory != null)
                  _buildCategoryChip(
                    colorScheme,
                    p.subcategory!.name,
                    Icons.label_outline_rounded,
                    () => Navigator.of(context).pushNamed('/subcategory/${p.subcategory!.id}'),
                  ),
              ],
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildPriceSection(BuildContext context, ProductDetailModel p) {
    final colorScheme = Theme.of(context).colorScheme;

    return Container(
      margin: const EdgeInsets.fromLTRB(20, 16, 20, 0),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: colorScheme.surfaceContainerHighest.withValues(alpha: 0.2),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: colorScheme.outlineVariant.withValues(alpha: 0.3)),
      ),
      child: Column(
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Text(
                p.displayPrice.toStringAsFixed(0),
                style: TextStyle(
                  fontSize: 32,
                  fontWeight: FontWeight.w900,
                  color: p.hasActiveDiscount ? const Color(0xFFDC2626) : colorScheme.onSurface,
                  letterSpacing: -1,
                ),
              ),
              const SizedBox(width: 6),
              Padding(
                padding: const EdgeInsets.only(bottom: 4),
                child: Text('SYP', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w600, color: colorScheme.outline)),
              ),
              if (p.hasActiveDiscount && p.discountedPrice != null) ...[
                const SizedBox(width: 12),
                Padding(
                  padding: const EdgeInsets.only(bottom: 4),
                  child: Text(
                    '${p.price.toStringAsFixed(0)} SYP',
                    style: TextStyle(
                      fontSize: 15,
                      color: colorScheme.outline,
                      decoration: TextDecoration.lineThrough,
                      decorationColor: colorScheme.outline,
                    ),
                  ),
                ),
              ],
            ],
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                decoration: BoxDecoration(
                  color: p.inStock
                      ? const Color(0xFF059669).withValues(alpha: 0.1)
                      : colorScheme.errorContainer.withValues(alpha: 0.3),
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(
                    color: p.inStock
                        ? const Color(0xFF059669).withValues(alpha: 0.2)
                        : colorScheme.error.withValues(alpha: 0.2),
                  ),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Icon(
                      p.inStock ? Icons.check_circle_rounded : Icons.cancel_rounded,
                      size: 14,
                      color: p.inStock ? const Color(0xFF059669) : colorScheme.error,
                    ),
                    const SizedBox(width: 6),
                    Text(
                      p.inStock ? '${AppStrings.tr('product.in_stock')} (${p.quantity})' : AppStrings.tr('common.sold_out'),
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w700,
                        color: p.inStock ? const Color(0xFF059669) : colorScheme.error,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildDetailsSection(BuildContext context, ProductDetailModel p) {
    return const SizedBox.shrink();
  }

  Widget _buildDescriptionSection(BuildContext context, ProductDetailModel p) {
    final colorScheme = Theme.of(context).colorScheme;

    return Container(
      margin: const EdgeInsets.fromLTRB(20, 16, 20, 0),
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: colorScheme.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: colorScheme.outlineVariant.withValues(alpha: 0.3)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 32,
                height: 32,
                decoration: BoxDecoration(
                  color: AppTheme.brandPrimary.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: const Icon(Icons.description_outlined, size: 16, color: AppTheme.brandPrimary),
              ),
              const SizedBox(width: 10),
              Text(
                AppStrings.tr('product.description'),
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: colorScheme.onSurface),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Text(
            p.description!,
            style: TextStyle(fontSize: 14, color: colorScheme.onSurfaceVariant, height: 1.7),
          ),
        ],
      ),
    );
  }

  Widget _buildReviewsSection(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final isLoggedIn = widget.authService?.isLoggedIn ?? false;
    final currentUserId = widget.authService?.user?.id;

    return Container(
      margin: const EdgeInsets.fromLTRB(20, 16, 20, 0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 32,
                height: 32,
                decoration: BoxDecoration(
                  color: const Color(0xFFF59E0B).withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: const Icon(Icons.star_rounded, size: 16, color: Color(0xFFF59E0B)),
              ),
              const SizedBox(width: 10),
              Text(
                '${AppStrings.tr('product.reviews')} (${_product?.reviewCount ?? 0})',
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: colorScheme.onSurface),
              ),
            ],
          ),
          const SizedBox(height: 16),
          if (isLoggedIn) ...[
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: colorScheme.surface,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: colorScheme.outlineVariant.withValues(alpha: 0.3)),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    AppStrings.tr('product.write_review'),
                    style: TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: colorScheme.onSurface),
                  ),
                  const SizedBox(height: 10),
                  Row(
                    children: List.generate(5, (i) {
                      return GestureDetector(
                        onTap: () => setState(() => _reviewRating = i + 1),
                        child: Padding(
                          padding: const EdgeInsets.only(right: 4),
                          child: Icon(
                            i < _reviewRating ? Icons.star_rounded : Icons.star_outline_rounded,
                            size: 32,
                            color: const Color(0xFFF59E0B),
                          ),
                        ),
                      );
                    }),
                  ),
                  const SizedBox(height: 10),
                  TextField(
                    controller: _reviewCtrl,
                    decoration: InputDecoration(hintText: AppStrings.tr('product.review_placeholder'), isDense: true),
                    maxLines: 3,
                  ),
                  const SizedBox(height: 10),
                  Align(
                    alignment: AlignmentDirectional.centerEnd,
                    child: FilledButton.icon(
                      onPressed: _submittingReview ? null : _submitReview,
                      icon: _submittingReview
                          ? const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                          : const Icon(Icons.send_rounded, size: 16),
                      label: Text(AppStrings.tr('product.submit_review')),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),
          ],
          if (_loadingReviews && _reviews.isEmpty)
            const Center(child: Padding(padding: EdgeInsets.all(16), child: CircularProgressIndicator()))
          else if (_reviews.isEmpty)
            Center(
              child: Padding(
                padding: const EdgeInsets.all(24),
                child: Column(
                  children: [
                    Icon(Icons.rate_review_outlined, size: 40, color: colorScheme.outline.withValues(alpha: 0.3)),
                    const SizedBox(height: 8),
                    Text(AppStrings.tr('notifications.empty'), style: TextStyle(color: colorScheme.outline)),
                  ],
                ),
              ),
            )
          else
            ...List.generate(_reviews.length, (i) {
              final r = _reviews[i];
              return _ReviewTile(
                review: r,
                colorScheme: colorScheme,
                isOwn: currentUserId != null && r.userId == currentUserId,
                onDelete: () => _deleteReview(r.id),
              );
            }),
          if (_reviewPage < _reviewLastPage)
            Center(
              child: Padding(
                padding: const EdgeInsets.only(top: 8),
                child: OutlinedButton.icon(
                  onPressed: () {
                    _reviewPage++;
                    _loadReviews(reset: false);
                  },
                  icon: const Icon(Icons.expand_more_rounded, size: 18),
                  label: Text(AppStrings.tr('common.view_all')),
                ),
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildBottomBar(BuildContext context, ProductDetailModel p) {
    final colorScheme = Theme.of(context).colorScheme;

    return SafeArea(
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
        decoration: BoxDecoration(
          color: colorScheme.surface,
          border: Border(top: BorderSide(color: colorScheme.outlineVariant.withValues(alpha: 0.3))),
          boxShadow: [
            BoxShadow(color: Colors.black.withValues(alpha: 0.04), blurRadius: 10, offset: const Offset(0, -4)),
          ],
        ),
        child: Row(
          children: [
            Expanded(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    '${p.displayPrice.toStringAsFixed(0)} SYP',
                    style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: colorScheme.onSurface, letterSpacing: -0.5),
                  ),
                  if (p.hasActiveDiscount && p.discountedPrice != null)
                    Text(
                      '${p.price.toStringAsFixed(0)} SYP',
                      style: TextStyle(fontSize: 12, color: colorScheme.outline, decoration: TextDecoration.lineThrough),
                    ),
                ],
              ),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: SizedBox(
                height: 50,
                child: FilledButton.icon(
                  onPressed: p.inStock ? _addToCart : null,
                  icon: Icon(p.inStock ? Icons.shopping_cart_rounded : Icons.block_rounded, size: 20),
                  label: Text(
                    p.inStock ? AppStrings.tr('common.add_to_cart') : AppStrings.tr('common.sold_out'),
                    style: const TextStyle(fontSize: 15),
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStars(double rating) {
    final r = rating.clamp(0.0, 5.0);
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: List.generate(5, (i) {
        if (i < r.floor()) {
          return const Icon(Icons.star_rounded, size: 20, color: Color(0xFFF59E0B));
        } else if (i < r.ceil() && r % 1 > 0) {
          return const Icon(Icons.star_half_rounded, size: 20, color: Color(0xFFF59E0B));
        }
        return const Icon(Icons.star_outline_rounded, size: 20, color: Color(0xFFF59E0B));
      }),
    );
  }

  Widget _buildCategoryChip(ColorScheme colorScheme, String label, IconData icon, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
        decoration: BoxDecoration(
          color: colorScheme.surfaceContainerHighest.withValues(alpha: 0.4),
          borderRadius: BorderRadius.circular(8),
          border: Border.all(color: colorScheme.outlineVariant.withValues(alpha: 0.3)),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, size: 13, color: colorScheme.outline),
            const SizedBox(width: 5),
            Text(label, style: TextStyle(fontSize: 12, fontWeight: FontWeight.w500, color: colorScheme.onSurfaceVariant)),
          ],
        ),
      ),
    );
  }
}

class _ReviewTile extends StatelessWidget {
  const _ReviewTile({required this.review, required this.colorScheme, this.isOwn = false, this.onDelete});

  final ReviewModel review;
  final ColorScheme colorScheme;
  final bool isOwn;
  final VoidCallback? onDelete;

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: colorScheme.surface,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: colorScheme.outlineVariant.withValues(alpha: 0.3)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              CircleAvatar(
                radius: 18,
                backgroundColor: AppTheme.brandPrimary.withValues(alpha: 0.1),
                child: Text(
                  (review.userName ?? '?').substring(0, 1).toUpperCase(),
                  style: const TextStyle(fontWeight: FontWeight.w700, color: AppTheme.brandPrimary),
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      review.userName ?? '?',
                      style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: colorScheme.onSurface),
                    ),
                    if (review.createdAt != null)
                      Text(review.createdAt!, style: TextStyle(fontSize: 11, color: colorScheme.outline)),
                  ],
                ),
              ),
              Row(
                mainAxisSize: MainAxisSize.min,
                children: List.generate(5, (i) => Icon(
                  i < review.rating ? Icons.star_rounded : Icons.star_outline_rounded,
                  size: 14,
                  color: const Color(0xFFF59E0B),
                )),
              ),
              if (isOwn && onDelete != null) ...[
                const SizedBox(width: 8),
                GestureDetector(
                  onTap: onDelete,
                  child: Container(
                    width: 28,
                    height: 28,
                    decoration: BoxDecoration(
                      color: colorScheme.errorContainer.withValues(alpha: 0.3),
                      borderRadius: BorderRadius.circular(6),
                    ),
                    child: Icon(Icons.delete_outline_rounded, size: 14, color: colorScheme.error),
                  ),
                ),
              ],
            ],
          ),
          if (review.body != null && review.body!.isNotEmpty)
            Padding(
              padding: const EdgeInsets.only(top: 10),
              child: Text(review.body!, style: TextStyle(fontSize: 13, color: colorScheme.onSurfaceVariant, height: 1.5)),
            ),
        ],
      ),
    );
  }
}

class _FullscreenGallery extends StatefulWidget {
  const _FullscreenGallery({required this.photos, this.initialIndex = 0});

  final List<String?> photos;
  final int initialIndex;

  @override
  State<_FullscreenGallery> createState() => _FullscreenGalleryState();
}

class _FullscreenGalleryState extends State<_FullscreenGallery> {
  late final PageController _ctrl;
  late int _current;
  final _transformCtrl = TransformationController();
  bool _zoomed = false;

  @override
  void initState() {
    super.initState();
    _current = widget.initialIndex;
    _ctrl = PageController(initialPage: widget.initialIndex);
    _transformCtrl.addListener(_onTransformChange);
  }

  @override
  void dispose() {
    _ctrl.dispose();
    _transformCtrl.removeListener(_onTransformChange);
    _transformCtrl.dispose();
    super.dispose();
  }

  void _onTransformChange() {
    final scale = _transformCtrl.value.getMaxScaleOnAxis();
    final wasZoomed = _zoomed;
    _zoomed = scale > 1.05;
    if (wasZoomed != _zoomed) setState(() {});
  }

  void _resetZoom() {
    _transformCtrl.value = Matrix4.identity();
  }

  @override
  Widget build(BuildContext context) {
    final total = widget.photos.length;

    return Scaffold(
      backgroundColor: Colors.black,
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.white,
        elevation: 0,
        leading: IconButton(
          icon: Container(
            width: 36,
            height: 36,
            decoration: BoxDecoration(
              color: Colors.black.withValues(alpha: 0.4),
              shape: BoxShape.circle,
            ),
            child: const Icon(Icons.close_rounded, size: 20, color: Colors.white),
          ),
          onPressed: () => Navigator.of(context).pop(),
        ),
        actions: [
          if (total > 1)
            Container(
              margin: const EdgeInsets.only(right: 12),
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              decoration: BoxDecoration(
                color: Colors.black.withValues(alpha: 0.4),
                borderRadius: BorderRadius.circular(20),
              ),
              child: Text(
                '${_current + 1} / $total',
                style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: Colors.white),
              ),
            ),
        ],
      ),
      body: Stack(
        fit: StackFit.expand,
        children: [
          PageView.builder(
            controller: _ctrl,
            physics: _zoomed ? const NeverScrollableScrollPhysics() : const BouncingScrollPhysics(),
            itemCount: total,
            onPageChanged: (i) {
              _resetZoom();
              setState(() => _current = i);
            },
            itemBuilder: (context, i) {
              return GestureDetector(
                onDoubleTap: () {
                  if (_zoomed) {
                    _resetZoom();
                  } else {
                    _transformCtrl.value = Matrix4.diagonal3Values(2.5, 2.5, 1.0);
                  }
                },
                child: InteractiveViewer(
                  transformationController: _transformCtrl,
                  minScale: 1.0,
                  maxScale: 5.0,
                  child: Center(
                    child: AppNetworkImage(url: widget.photos[i], fit: BoxFit.contain),
                  ),
                ),
              );
            },
          ),
          if (total > 1)
            Positioned(
              bottom: MediaQuery.of(context).padding.bottom + 24,
              left: 0,
              right: 0,
              child: SizedBox(
                height: 56,
                child: ListView.builder(
                  scrollDirection: Axis.horizontal,
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  itemCount: total,
                  itemBuilder: (context, i) {
                    final isSelected = i == _current;
                    return GestureDetector(
                      onTap: () {
                        _resetZoom();
                        _ctrl.animateToPage(i, duration: const Duration(milliseconds: 300), curve: Curves.easeInOut);
                      },
                      child: AnimatedContainer(
                        duration: const Duration(milliseconds: 200),
                        width: 52,
                        height: 52,
                        margin: const EdgeInsetsDirectional.only(end: 8),
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(
                            color: isSelected ? Colors.white : Colors.white.withValues(alpha: 0.25),
                            width: isSelected ? 2.5 : 1,
                          ),
                        ),
                        clipBehavior: Clip.antiAlias,
                        child: AnimatedOpacity(
                          duration: const Duration(milliseconds: 200),
                          opacity: isSelected ? 1.0 : 0.45,
                          child: AppNetworkImage(url: widget.photos[i], fit: BoxFit.cover),
                        ),
                      ),
                    );
                  },
                ),
              ),
            ),
        ],
      ),
    );
  }
}
