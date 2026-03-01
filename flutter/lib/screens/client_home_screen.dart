import 'package:flutter/material.dart';

import '../l10n/app_strings.dart';
import '../models/category_model.dart';
import '../models/product_model.dart';
import '../models/vendor_model.dart';
import '../services/app_settings_service.dart';
import '../services/auth_service.dart';
import '../services/client_api_service.dart';
import '../widgets/category_card.dart';
import '../widgets/product_card.dart';
import '../widgets/section_header.dart';
import '../widgets/vendor_card.dart';

/// Client home matching Blade: Hero, Categories, Subcategories, Promo, Vendors, Products, Best Selling, Most Favorited, Trust, Contact.
class ClientHomeScreen extends StatefulWidget {
  const ClientHomeScreen({super.key, required this.authService, required this.appSettings});

  final AuthService authService;
  final AppSettingsService appSettings;

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

  @override
  void initState() {
    super.initState();
    _loadAll();
  }

  Future<void> _loadAll() async {
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final cats = await _api.getCategories();
      final allSubs = <SubcategoryModel>[];
      for (final c in cats) {
        for (final s in c.subcategories) {
          allSubs.add(SubcategoryModel(
            id: s.id,
            name: s.name,
            image: s.image,
            categoryName: c.name,
          ));
        }
      }
      final vendors = await _api.getVendors();
      final products = await _api.getProducts(perPage: 5);
      final best = await _api.getProducts(perPage: 5, sort: 'best_selling');
      final favorited = await _api.getProducts(perPage: 5, sort: 'most_favorited');
      if (!mounted) return;
      setState(() {
        _categories = cats;
        _subcategories = allSubs;
        _vendors = vendors;
        _products = products;
        _bestSelling = best;
        _mostFavorited = favorited;
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
              widget.appSettings.setThemeMode(
                isDark ? ThemeMode.light : ThemeMode.dark,
              );
            },
            tooltip: AppStrings.tr('admin.toggle_theme'),
          ),
          PopupMenuButton<String>(
            icon: const Icon(Icons.language),
            tooltip: AppStrings.tr('lang.choose_language'),
            onSelected: (code) {
              widget.appSettings.setLocale(Locale(code));
            },
            itemBuilder: (context) => [
              PopupMenuItem(value: 'en', child: Text(AppStrings.tr('lang.english'))),
              PopupMenuItem(value: 'ar', child: Text(AppStrings.tr('lang.arabic'))),
            ],
          ),
          IconButton(icon: const Icon(Icons.shopping_cart_outlined), onPressed: () {}),
          IconButton(
            icon: const Icon(Icons.logout),
            onPressed: () async {
              await widget.authService.logout();
              if (mounted) Navigator.of(context).pushReplacementNamed('/login');
            },
            tooltip: AppStrings.tr('nav.sign_out'),
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
                    if (_error != null) Padding(padding: const EdgeInsets.all(16), child: Text(_error!, style: const TextStyle(color: Colors.red))),
                    _buildSectionCategories(),
                    _buildSectionSubcategories(),
                    _buildPromoBanner(),
                    _buildSectionVendors(),
                    _buildSectionProducts('New Arrivals', 'Latest Products', 'Freshly added to our marketplace', _products),
                    _buildSectionProducts('Bestsellers', 'Best Selling', 'Most purchased on our marketplace', _bestSelling),
                    _buildSectionProducts('Most Favorited', 'Most Favorited', 'Top picks from our customers', _mostFavorited),
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
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 32),
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFF111827), Color(0xFF1F2937), Color(0xFFEA580C)],
        ),
      ),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.1),
              borderRadius: BorderRadius.circular(999),
              border: Border.all(color: const Color(0xFFF97316).withOpacity(0.3)),
            ),
            child: const Text('Free shipping on orders over 50,000 SYP', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: Color(0xFFFDBA74))),
          ),
          const SizedBox(height: 20),
          Text(
            AppStrings.tr('home.hero_title'),
            textAlign: TextAlign.center,
            style: const TextStyle(fontSize: 32, fontWeight: FontWeight.w800, color: Colors.white, height: 1.2),
          ),
          const SizedBox(height: 12),
          const Text(
            'Thousands of products from verified vendors. Experience premium quality, competitive prices, and fast delivery — all in one place.',
            textAlign: TextAlign.center,
            style: TextStyle(fontSize: 14, color: Color(0xFF9CA3AF)),
          ),
          const SizedBox(height: 24),
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              FilledButton.icon(
                onPressed: () {},
                icon: const Icon(Icons.shopping_bag_outlined, size: 18),
                label: const Text('Start Shopping'),
                style: FilledButton.styleFrom(backgroundColor: const Color(0xFFF97316), foregroundColor: Colors.white),
              ),
              const SizedBox(width: 12),
              OutlinedButton.icon(
                onPressed: () {},
                icon: const Icon(Icons.grid_view_rounded, size: 18),
                label: const Text('Browse Categories'),
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
          const SectionHeader(
            badge: 'Shop by Category',
            title: 'Browse Our Collections',
            subtitle: "Find what you're looking for across our curated categories",
          ),
          if (_loading && _categories.isEmpty)
            const Center(child: Padding(padding: EdgeInsets.all(24), child: CircularProgressIndicator()))
          else if (_categories.isEmpty)
            const Center(child: Text('No categories yet.', style: TextStyle(color: Color(0xFF9CA3AF))))
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
              itemBuilder: (context, i) => CategoryCard(category: _categories[i], onTap: () {}),
            ),
        ],
      ),
    );
  }

  Widget _buildSectionSubcategories() {
    if (_subcategories.isEmpty) return const SizedBox.shrink();
    return Container(
      color: const Color(0xFFF9FAFB),
      padding: const EdgeInsets.symmetric(vertical: 32, horizontal: 16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const SectionHeader(
            badge: 'Browse',
            title: 'Popular Subcategories',
            subtitle: 'Explore specific product types',
          ),
          SizedBox(
            height: 100,
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              itemCount: _subcategories.length,
              itemBuilder: (context, i) {
                final s = _subcategories[i];
                return Padding(
                  padding: const EdgeInsets.only(right: 12),
                  child: Card(
                    child: InkWell(
                      onTap: () {},
                      borderRadius: BorderRadius.circular(16),
                      child: Padding(
                        padding: const EdgeInsets.all(12),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Container(
                              width: 48,
                              height: 48,
                              decoration: BoxDecoration(color: const Color(0xFFF3F4F6), borderRadius: BorderRadius.circular(12)),
                              child: const Icon(Icons.label_outline, color: Color(0xFF9CA3AF)),
                            ),
                            const SizedBox(width: 12),
                            Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(s.name, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w700)),
                                if (s.categoryName != null) Text(s.categoryName!, style: const TextStyle(fontSize: 10, color: Color(0xFF9CA3AF))),
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
          gradient: const LinearGradient(
            colors: [Color(0xFFEA580C), Color(0xFFF97316), Color(0xFFEAB308)],
          ),
          borderRadius: BorderRadius.circular(24),
          boxShadow: [BoxShadow(color: const Color(0xFFF97316).withOpacity(0.3), blurRadius: 20, offset: const Offset(0, 8))],
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
                  const Text('LIMITED OFFER', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700, letterSpacing: 1.5, color: Colors.white70)),
                  const SizedBox(height: 4),
                  const Text('New arrivals every day!', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: Colors.white)),
                  const Text('Explore fresh products from our top vendors, updated daily.', style: TextStyle(fontSize: 12, color: Colors.white70)),
                ],
              ),
            ),
            TextButton(
              onPressed: () {},
              child: const Text('Shop Now →', style: TextStyle(fontWeight: FontWeight.w700, color: Colors.white)),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSectionVendors() {
    return Container(
      color: const Color(0xFFF9FAFB),
      padding: const EdgeInsets.symmetric(vertical: 32, horizontal: 16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const SectionHeader(
            badge: 'Stores',
            title: 'Featured Stores',
            subtitle: 'Trusted vendors with quality products',
          ),
          if (_vendors.isEmpty)
            const Center(child: Padding(padding: EdgeInsets.all(24), child: Text('No stores available yet.', style: TextStyle(color: Color(0xFF6B7280)))))
          else
            SizedBox(
              height: 220,
              child: ListView.builder(
                scrollDirection: Axis.horizontal,
                itemCount: _vendors.length,
                itemBuilder: (context, i) => Padding(
                  padding: const EdgeInsets.only(right: 12),
                  child: VendorCard(vendor: _vendors[i], onTap: () {}),
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
            action: TextButton(onPressed: () {}, child: const Text('View All')),
          ),
          if (list.isEmpty)
            const Center(child: Padding(padding: EdgeInsets.all(24), child: Text('No products yet.', style: TextStyle(color: Color(0xFF9CA3AF)))))
          else
            GridView.builder(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 2,
                childAspectRatio: 0.72,
                crossAxisSpacing: 12,
                mainAxisSpacing: 12,
              ),
              itemCount: list.length,
              itemBuilder: (context, i) => ProductCard(
                product: list[i],
                onTap: () {},
                onAddToCart: () {},
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildTrustBadges() {
    final items = <Map<String, dynamic>>[
      {'icon': Icons.local_shipping_outlined, 'title': 'Fast Delivery', 'subtitle': 'Quick & reliable shipping', 'color': const Color(0xFFF97316)},
      {'icon': Icons.verified_user_outlined, 'title': 'Secure Shopping', 'subtitle': '100% safe & encrypted', 'color': const Color(0xFF059669)},
      {'icon': Icons.replay, 'title': 'Easy Returns', 'subtitle': 'Hassle-free return policy', 'color': const Color(0xFF2563EB)},
      {'icon': Icons.support_agent, 'title': '24/7 Support', 'subtitle': 'Always here to help', 'color': const Color(0xFF9333EA)},
    ];
    return Container(
      color: const Color(0xFFF9FAFB),
      padding: const EdgeInsets.symmetric(vertical: 32, horizontal: 16),
      child: Column(
        children: [
          GridView.count(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            crossAxisCount: 2,
            mainAxisSpacing: 12,
            crossAxisSpacing: 12,
            childAspectRatio: 1.1,
            children: items.map((e) {
              final color = e['color'] as Color;
              return Card(
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Container(
                        width: 48,
                        height: 48,
                        decoration: BoxDecoration(color: color.withOpacity(0.15), borderRadius: BorderRadius.circular(16)),
                        child: Icon(e['icon'] as IconData, color: color, size: 24),
                      ),
                      const SizedBox(height: 12),
                      Text(e['title'] as String, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w700)),
                      const SizedBox(height: 4),
                      Text(e['subtitle'] as String, style: const TextStyle(fontSize: 12, color: Color(0xFF6B7280)), textAlign: TextAlign.center),
                    ],
                  ),
                ),
              );
            }).toList(),
          ),
        ],
      ),
    );
  }

  Widget _buildContact() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 32),
      child: Column(
        children: [
          Text(AppStrings.tr('home.contact'), style: TextStyle(fontSize: 22, fontWeight: FontWeight.w800, color: Theme.of(context).colorScheme.onSurface)),
          const SizedBox(height: 8),
          Text(AppStrings.tr('home.contact_subtitle'), style: TextStyle(fontSize: 14, color: Theme.of(context).colorScheme.onSurface.withOpacity(0.7)), textAlign: TextAlign.center),
          const SizedBox(height: 24),
          Card(
            child: Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                children: [
                  TextField(decoration: const InputDecoration(labelText: 'Your name (optional)', hintText: 'Your name')),
                  const SizedBox(height: 16),
                  TextField(decoration: const InputDecoration(labelText: 'Email *', hintText: 'you@example.com'), keyboardType: TextInputType.emailAddress),
                  const SizedBox(height: 16),
                  TextField(decoration: const InputDecoration(labelText: 'Message *', hintText: 'Your message...'), maxLines: 4),
                  const SizedBox(height: 20),
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(onPressed: () {}, child: Text(AppStrings.tr('home.send_message'))),
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
