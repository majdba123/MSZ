import 'package:flutter/material.dart';

import '../l10n/app_strings.dart';
import '../models/category_model.dart';
import '../models/product_model.dart';
import '../models/vendor_model.dart';
import '../services/app_settings_service.dart';
import '../services/auth_service.dart';
import '../services/cart_service.dart';
import '../services/client_api_service.dart';
import '../services/favourite_service.dart';
import '../widgets/category_card.dart';
import '../widgets/product_card.dart';
import '../widgets/responsive_product_grid.dart';
import '../widgets/section_header.dart';
import '../widgets/vendor_card.dart';

class ClientHomeScreen extends StatefulWidget {
  const ClientHomeScreen({
    super.key,
    required this.authService,
    required this.appSettings,
    required this.cartService,
    required this.favouriteService,
  });

  final AuthService authService;
  final AppSettingsService appSettings;
  final CartService cartService;
  final FavouriteService favouriteService;

  @override
  State<ClientHomeScreen> createState() => _ClientHomeScreenState();
}

class _ClientHomeScreenState extends State<ClientHomeScreen> {
  final ClientApiService _api = ClientApiService();
  List<CategoryModel> _categories = [];
  List<SubcategoryModel> _subcategories = [];
  List<VendorModel> _vendors = [];
  List<ProductModel> _products = [];
  List<ProductModel> _bestSelling = [];
  List<ProductModel> _mostFavorited = [];
  bool _loading = true;
  String? _error;

  final _nameCtrl = TextEditingController();
  final _emailCtrl = TextEditingController();
  final _msgCtrl = TextEditingController();
  bool _sending = false;

  @override
  void initState() {
    super.initState();
    _loadAll();
    widget.favouriteService.addListener(_onFavChanged);

    final user = widget.authService.user;
    if (user != null) {
      _nameCtrl.text = user.name;
      _emailCtrl.text = user.email ?? '';
    }
  }

  @override
  void dispose() {
    widget.favouriteService.removeListener(_onFavChanged);
    _nameCtrl.dispose();
    _emailCtrl.dispose();
    _msgCtrl.dispose();
    super.dispose();
  }

  void _onFavChanged() {
    if (mounted) setState(() {});
  }

  Future<void> _loadAll() async {
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final results = await Future.wait([
        _api.getCategories(),
        _api.getVendors(),
        _api.getProducts(perPage: 5),
        _api.getProducts(perPage: 5, sort: 'best_selling'),
        _api.getProducts(perPage: 5, sort: 'most_favorited'),
      ]);
      if (!mounted) return;
      final cats = results[0] as List<CategoryModel>;
      final allSubs = <SubcategoryModel>[];
      for (final c in cats) {
        for (final s in c.subcategories) {
          allSubs.add(SubcategoryModel(id: s.id, name: s.name, image: s.image, categoryName: c.name));
        }
      }
      setState(() {
        _categories = cats;
        _subcategories = allSubs;
        _vendors = results[1] as List<VendorModel>;
        _products = results[2] as List<ProductModel>;
        _bestSelling = results[3] as List<ProductModel>;
        _mostFavorited = results[4] as List<ProductModel>;
        _loading = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _error = e.toString();
        _loading = false;
      });
    }
  }

  void _addToCart(ProductModel p) {
    widget.cartService.addItem(
      productId: p.id,
      name: p.name,
      price: p.displayPrice,
      photo: p.firstPhotoUrl,
    );
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(AppStrings.tr('common.added_to_cart')), duration: const Duration(seconds: 1)),
    );
  }

  void _toggleFav(int productId) {
    widget.favouriteService.toggle(productId);
  }

  Future<void> _sendContact() async {
    if (_emailCtrl.text.trim().isEmpty || _msgCtrl.text.trim().isEmpty) return;
    setState(() => _sending = true);
    final ok = await _api.sendContact(
      name: _nameCtrl.text.trim(),
      email: _emailCtrl.text.trim(),
      message: _msgCtrl.text.trim(),
    );
    if (!mounted) return;
    setState(() => _sending = false);
    if (ok) {
      _msgCtrl.clear();
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(AppStrings.tr('home.message_sent'))),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return Scaffold(
      appBar: AppBar(
        title: Text(AppStrings.tr('SyriaZone')),
        actions: [
          IconButton(
            icon: Icon(isDark ? Icons.light_mode : Icons.dark_mode),
            onPressed: () {
              widget.appSettings.setThemeMode(isDark ? ThemeMode.light : ThemeMode.dark);
            },
            tooltip: AppStrings.tr('admin.toggle_theme'),
          ),
          PopupMenuButton<String>(
            icon: const Icon(Icons.language),
            tooltip: AppStrings.tr('lang.choose_language'),
            onSelected: (code) => widget.appSettings.setLocale(Locale(code)),
            itemBuilder: (context) => [
              PopupMenuItem(value: 'en', child: Text(AppStrings.tr('lang.english'))),
              PopupMenuItem(value: 'ar', child: Text(AppStrings.tr('lang.arabic'))),
            ],
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: _loadAll,
        child: _loading && _categories.isEmpty
            ? const Center(child: CircularProgressIndicator())
            : SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    _buildHero(),
                    if (_error != null)
                      Padding(
                        padding: const EdgeInsets.all(16),
                        child: Text(_error!, style: const TextStyle(color: Colors.red)),
                      ),
                    _buildSectionCategories(),
                    _buildSectionSubcategories(),
                    _buildPromoBanner(),
                    _buildSectionVendors(),
                    _buildSectionProducts(
                      AppStrings.tr('home.section_new_arrivals_badge'),
                      AppStrings.tr('home.section_new_arrivals_title'),
                      AppStrings.tr('home.section_new_arrivals_subtitle'),
                      _products,
                    ),
                    _buildSectionProducts(
                      AppStrings.tr('home.section_bestsellers_badge'),
                      AppStrings.tr('home.section_bestsellers_title'),
                      AppStrings.tr('home.section_bestsellers_subtitle'),
                      _bestSelling,
                    ),
                    _buildSectionProducts(
                      AppStrings.tr('home.section_most_favorited_badge'),
                      AppStrings.tr('home.section_most_favorited_title'),
                      AppStrings.tr('home.section_most_favorited_subtitle'),
                      _mostFavorited,
                    ),
                    _buildTrustBadges(),
                    _buildContact(),
                    const SizedBox(height: 32),
                  ],
                ),
              ),
      ),
    );
  }

  Widget _buildHero() {
    final theme = Theme.of(context);
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 32),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: AlignmentDirectional.topStart,
          end: AlignmentDirectional.bottomEnd,
          colors: [
            theme.colorScheme.inverseSurface,
            theme.colorScheme.inverseSurface.withValues(alpha: 0.9),
            theme.colorScheme.primary,
          ],
        ),
      ),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            decoration: BoxDecoration(
              color: Colors.white.withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(999),
              border: Border.all(color: const Color(0xFFF97316).withValues(alpha: 0.3)),
            ),
            child: Text(
              AppStrings.tr('home.hero_badge'),
              style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: Color(0xFFFDBA74)),
            ),
          ),
          const SizedBox(height: 20),
          Text(
            AppStrings.tr('home.hero_title'),
            textAlign: TextAlign.center,
            style: const TextStyle(fontSize: 32, fontWeight: FontWeight.w800, color: Colors.white, height: 1.2),
          ),
          const SizedBox(height: 12),
          Text(
            AppStrings.tr('home.hero_description'),
            textAlign: TextAlign.center,
            style: const TextStyle(fontSize: 14, color: Color(0xFF9CA3AF)),
          ),
          const SizedBox(height: 24),
          Wrap(
            alignment: WrapAlignment.center,
            spacing: 12,
            runSpacing: 8,
            children: [
              FilledButton.icon(
                onPressed: () => Navigator.of(context).pushNamed('/products'),
                icon: const Icon(Icons.shopping_bag_outlined, size: 18),
                label: Text(AppStrings.tr('home.hero_cta_primary')),
                style: FilledButton.styleFrom(backgroundColor: const Color(0xFFF97316), foregroundColor: Colors.white),
              ),
              OutlinedButton.icon(
                onPressed: () => Navigator.of(context).pushNamed('/categories'),
                icon: const Icon(Icons.grid_view_rounded, size: 18),
                label: Text(AppStrings.tr('home.hero_cta_secondary')),
                style: OutlinedButton.styleFrom(foregroundColor: Colors.white, side: const BorderSide(color: Colors.white54)),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildSectionCategories() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 32),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SectionHeader(
            badge: AppStrings.tr('home.categories_badge'),
            title: AppStrings.tr('home.categories_title'),
            subtitle: AppStrings.tr('home.categories_subtitle'),
            action: TextButton(
              onPressed: () => Navigator.of(context).pushNamed('/categories'),
              child: Text(AppStrings.tr('common.view_all')),
            ),
          ),
          if (_loading && _categories.isEmpty)
            const Center(child: Padding(padding: EdgeInsets.all(24), child: CircularProgressIndicator()))
          else if (_categories.isEmpty)
            Center(child: Text(AppStrings.tr('home.empty_categories'), style: const TextStyle(color: Color(0xFF9CA3AF))))
          else
            GridView.builder(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 2,
                childAspectRatio: 1.1,
                crossAxisSpacing: 12,
                mainAxisSpacing: 12,
              ),
              itemCount: _categories.length,
              itemBuilder: (context, i) => CategoryCard(
                key: ValueKey('cat_${_categories[i].id}'),
                category: _categories[i],
                onTap: () => Navigator.of(context).pushNamed('/category/${_categories[i].id}'),
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildSectionSubcategories() {
    if (_subcategories.isEmpty) return const SizedBox.shrink();
    final colorScheme = Theme.of(context).colorScheme;
    return Container(
      color: colorScheme.surfaceContainerHighest.withValues(alpha: 0.3),
      padding: const EdgeInsets.symmetric(vertical: 32, horizontal: 16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SectionHeader(
            badge: AppStrings.tr('home.subcategories_badge'),
            title: AppStrings.tr('home.subcategories_title'),
            subtitle: AppStrings.tr('home.subcategories_subtitle'),
          ),
          SizedBox(
            height: 100,
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              cacheExtent: 200,
              itemCount: _subcategories.length,
              itemBuilder: (context, i) {
                final s = _subcategories[i];
                return Padding(
                  key: ValueKey('sub_${s.id}'),
                  padding: const EdgeInsetsDirectional.only(end: 12),
                  child: Card(
                    child: InkWell(
                      onTap: () => Navigator.of(context).pushNamed('/subcategory/${s.id}'),
                      borderRadius: BorderRadius.circular(16),
                      child: Padding(
                        padding: const EdgeInsets.all(12),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Container(
                              width: 48,
                              height: 48,
                              decoration: BoxDecoration(
                                color: const Color(0xFFF3F4F6),
                                borderRadius: BorderRadius.circular(12),
                              ),
                              child: const Icon(Icons.label_outline, color: Color(0xFF9CA3AF)),
                            ),
                            const SizedBox(width: 12),
                            Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(s.name, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w700)),
                                if (s.categoryName != null)
                                  Text(s.categoryName!, style: const TextStyle(fontSize: 10, color: Color(0xFF9CA3AF))),
                              ],
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPromoBanner() {
    return Padding(
      padding: const EdgeInsets.all(16),
      child: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          gradient: const LinearGradient(colors: [Color(0xFFEA580C), Color(0xFFF97316), Color(0xFFEAB308)]),
          borderRadius: BorderRadius.circular(24),
          boxShadow: [
            BoxShadow(color: const Color(0xFFF97316).withValues(alpha: 0.3), blurRadius: 20, offset: const Offset(0, 8)),
          ],
        ),
        child: Row(
          children: [
            Container(
              width: 56,
              height: 56,
              decoration: BoxDecoration(color: Colors.white24, borderRadius: BorderRadius.circular(16)),
              child: const Icon(Icons.auto_awesome, color: Colors.white, size: 28),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(AppStrings.tr('home.promo_badge'),
                      style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w700, letterSpacing: 1.5, color: Colors.white70)),
                  const SizedBox(height: 4),
                  Text(AppStrings.tr('home.promo_title'),
                      style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: Colors.white)),
                  Text(AppStrings.tr('home.promo_subtitle'), style: const TextStyle(fontSize: 12, color: Colors.white70)),
                ],
              ),
            ),
            TextButton(
              onPressed: () => Navigator.of(context).pushNamed('/products'),
              child: Text(AppStrings.tr('home.promo_cta'),
                  style: const TextStyle(fontWeight: FontWeight.w700, color: Colors.white)),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSectionVendors() {
    final colorScheme = Theme.of(context).colorScheme;
    return Container(
      color: colorScheme.surfaceContainerHighest.withValues(alpha: 0.3),
      padding: const EdgeInsets.symmetric(vertical: 32, horizontal: 16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SectionHeader(
            badge: AppStrings.tr('home.vendors_badge'),
            title: AppStrings.tr('home.vendors_title'),
            subtitle: AppStrings.tr('home.vendors_subtitle'),
            action: TextButton(
              onPressed: () => Navigator.of(context).pushNamed('/vendors'),
              child: Text(AppStrings.tr('common.view_all')),
            ),
          ),
          if (_vendors.isEmpty)
            Center(
              child: Padding(
                padding: const EdgeInsets.all(24),
                child: Text(AppStrings.tr('home.empty_stores'), style: const TextStyle(color: Color(0xFF6B7280))),
              ),
            )
          else
            SizedBox(
              height: 220,
              child: ListView.builder(
                scrollDirection: Axis.horizontal,
                cacheExtent: 400,
                itemCount: _vendors.length,
                itemBuilder: (context, i) => Padding(
                  padding: const EdgeInsetsDirectional.only(end: 12),
                  key: ValueKey('vendor_${_vendors[i].id}'),
                  child: VendorCard(
                    vendor: _vendors[i],
                    onTap: () => Navigator.of(context).pushNamed('/vendor/${_vendors[i].id}'),
                  ),
                ),
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildSectionProducts(String badge, String title, String subtitle, List<ProductModel> list) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 32),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SectionHeader(
            badge: badge,
            title: title,
            subtitle: subtitle,
            action: TextButton(
              onPressed: () => Navigator.of(context).pushNamed('/products'),
              child: Text(AppStrings.tr('common.view_all')),
            ),
          ),
          if (list.isEmpty)
            Center(
              child: Padding(
                padding: const EdgeInsets.all(24),
                child: Text(AppStrings.tr('home.empty_products'),
                    style: TextStyle(color: Theme.of(context).colorScheme.outline)),
              ),
            )
          else
            GridView.builder(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              gridDelegate: responsiveProductGrid(context),
              itemCount: list.length,
              itemBuilder: (context, i) {
                final product = list[i];
                return ProductCard(
                  key: ValueKey('product_${product.id}_$badge'),
                  product: product,
                  isFavourite: widget.favouriteService.isFavourite(product.id),
                  onToggleFavourite: () => _toggleFav(product.id),
                  onTap: () => Navigator.of(context).pushNamed('/product/${product.id}'),
                  onAddToCart: () => _addToCart(product),
                );
              },
            ),
        ],
      ),
    );
  }

  Widget _buildTrustBadges() {
    final colorScheme = Theme.of(context).colorScheme;
    final items = <Map<String, dynamic>>[
      {'icon': Icons.local_shipping_outlined, 'title': AppStrings.tr('home.trust_fast_delivery_title'), 'subtitle': AppStrings.tr('home.trust_fast_delivery_subtitle'), 'color': const Color(0xFFF97316)},
      {'icon': Icons.verified_user_outlined, 'title': AppStrings.tr('home.trust_secure_shopping_title'), 'subtitle': AppStrings.tr('home.trust_secure_shopping_subtitle'), 'color': const Color(0xFF059669)},
      {'icon': Icons.replay, 'title': AppStrings.tr('home.trust_easy_returns_title'), 'subtitle': AppStrings.tr('home.trust_easy_returns_subtitle'), 'color': const Color(0xFF2563EB)},
      {'icon': Icons.support_agent, 'title': AppStrings.tr('home.trust_support_title'), 'subtitle': AppStrings.tr('home.trust_support_subtitle'), 'color': const Color(0xFF9333EA)},
    ];
    return Container(
      color: colorScheme.surfaceContainerHighest.withValues(alpha: 0.3),
      padding: const EdgeInsets.symmetric(vertical: 32, horizontal: 16),
      child: GridView.count(
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        crossAxisCount: 2,
        mainAxisSpacing: 12,
        crossAxisSpacing: 12,
        childAspectRatio: 1.1,
        children: items.map((e) {
          final color = e['color'] as Color;
          return RepaintBoundary(
            child: Card(
              color: colorScheme.surface,
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Container(
                      width: 48,
                      height: 48,
                      decoration: BoxDecoration(color: color.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(16)),
                      child: Icon(e['icon'] as IconData, color: color, size: 24),
                    ),
                    const SizedBox(height: 12),
                    Text(e['title'] as String, style: TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: colorScheme.onSurface)),
                    const SizedBox(height: 4),
                    Text(e['subtitle'] as String, style: TextStyle(fontSize: 12, color: colorScheme.outline), textAlign: TextAlign.center),
                  ],
                ),
              ),
            ),
          );
        }).toList(),
      ),
    );
  }

  Widget _buildContact() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 32),
      child: Column(
        children: [
          Text(AppStrings.tr('home.contact'),
              style: TextStyle(fontSize: 22, fontWeight: FontWeight.w800, color: Theme.of(context).colorScheme.onSurface)),
          const SizedBox(height: 8),
          Text(AppStrings.tr('home.contact_subtitle'),
              style: TextStyle(fontSize: 14, color: Theme.of(context).colorScheme.outline), textAlign: TextAlign.center),
          const SizedBox(height: 24),
          Card(
            child: Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                children: [
                  TextField(controller: _nameCtrl, decoration: InputDecoration(labelText: AppStrings.tr('auth.full_name'))),
                  const SizedBox(height: 16),
                  TextField(
                    controller: _emailCtrl,
                    decoration: InputDecoration(labelText: AppStrings.tr('auth.email'), hintText: 'you@example.com'),
                    keyboardType: TextInputType.emailAddress,
                  ),
                  const SizedBox(height: 16),
                  TextField(
                    controller: _msgCtrl,
                    decoration: InputDecoration(labelText: '${AppStrings.tr('home.message')} *', hintText: AppStrings.tr('home.message_hint')),
                    maxLines: 4,
                  ),
                  const SizedBox(height: 20),
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      onPressed: _sending ? null : _sendContact,
                      child: _sending
                          ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2))
                          : Text(AppStrings.tr('home.send_message')),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}
