import 'package:flutter/material.dart';

import '../l10n/app_strings.dart';
import '../models/category_model.dart';
import '../models/product_model.dart';
import '../models/vendor_model.dart';
import '../services/cart_service.dart';
import '../services/client_api_service.dart';
import '../services/favourite_service.dart';
import '../widgets/product_card.dart';
import '../widgets/responsive_product_grid.dart';

class ProductsListScreen extends StatefulWidget {
  const ProductsListScreen({
    super.key,
    this.categoryId,
    this.subcategoryId,
    this.vendorId,
    this.title,
    this.cartService,
    this.favouriteService,
  });

  final int? categoryId;
  final int? subcategoryId;
  final int? vendorId;
  final String? title;
  final CartService? cartService;
  final FavouriteService? favouriteService;

  @override
  State<ProductsListScreen> createState() => _ProductsListScreenState();
}

class _ProductsListScreenState extends State<ProductsListScreen> {
  final ClientApiService _api = ClientApiService();
  List<ProductModel> _products = [];
  List<CategoryModel> _categories = [];
  List<VendorModel> _vendors = [];
  int _page = 1;
  int? _lastPage;
  bool _loading = true;
  String? _error;
  int? _selectedCategoryId;
  int? _selectedSubcategoryId;
  int? _selectedVendorId;
  String? _sort;
  bool? _hasDiscount;

  @override
  void initState() {
    super.initState();
    _selectedCategoryId = widget.categoryId;
    _selectedSubcategoryId = widget.subcategoryId;
    _selectedVendorId = widget.vendorId;
    _loadCategoriesAndVendors();
    _loadProducts();
    widget.favouriteService?.addListener(_refresh);
  }

  @override
  void dispose() {
    widget.favouriteService?.removeListener(_refresh);
    super.dispose();
  }

  void _refresh() {
    if (mounted) setState(() {});
  }

  Future<void> _loadCategoriesAndVendors() async {
    try {
      final results = await Future.wait([_api.getCategories(), _api.getVendors()]);
      if (!mounted) return;
      setState(() {
        _categories = results[0] as List<CategoryModel>;
        _vendors = results[1] as List<VendorModel>;
      });
    } catch (_) {}
  }

  Future<void> _loadProducts({bool reset = false}) async {
    if (reset) _page = 1;
    setState(() {
      _loading = true;
      _error = null;
      if (reset) _products = [];
    });
    try {
      final data = await _api.getProductsPaginated(
        page: _page,
        perPage: 15,
        sort: _sort,
        categoryId: _selectedCategoryId ?? widget.categoryId,
        subcategoryId: _selectedSubcategoryId ?? widget.subcategoryId,
        vendorId: _selectedVendorId ?? widget.vendorId,
        hasDiscount: _hasDiscount,
      );
      if (!mounted) return;
      final list = (data?['data'] as List<dynamic>?)?.map((e) => ProductModel.fromJson(e as Map<String, dynamic>)).toList() ?? [];
      final meta = data?['meta'] as Map<String, dynamic>?;
      final lastPage = meta != null ? _toInt(meta['last_page']) : 1;
      setState(() {
        _products = reset ? list : [..._products, ...list];
        _lastPage = lastPage;
        _loading = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _loading = false;
        _error = e.toString();
      });
    }
  }

  int _toInt(dynamic v) => v is int ? v : int.tryParse(v?.toString() ?? '') ?? 1;
  bool get _hasMore => _lastPage != null && _page < _lastPage!;

  Future<void> _loadMore() async {
    if (_loading || _lastPage == null || _page >= _lastPage!) return;
    setState(() => _page++);
    await _loadProducts();
  }

  void _resetFilters() {
    setState(() {
      _selectedCategoryId = widget.categoryId;
      _selectedSubcategoryId = widget.subcategoryId;
      _selectedVendorId = widget.vendorId;
      _sort = null;
      _hasDiscount = null;
    });
    _loadProducts(reset: true);
  }

  void _addToCart(ProductModel p) {
    widget.cartService?.addItem(productId: p.id, name: p.name, price: p.displayPrice, photo: p.firstPhotoUrl);
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(AppStrings.tr('common.added_to_cart')), duration: const Duration(seconds: 1)),
    );
  }

  void _showFilterSheet(BuildContext context) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (ctx) {
        int? tempCat = _selectedCategoryId;
        int? tempSubcat = _selectedSubcategoryId;
        int? tempVendor = _selectedVendorId;
        String? tempSort = _sort;
        bool? tempDiscount = _hasDiscount;
        return StatefulBuilder(
          builder: (ctx, setSheetState) {
            final allSubcategories = <SubcategoryModel>[];
            for (final c in _categories) {
              allSubcategories.addAll(c.subcategories);
            }

            List<SubcategoryModel> availableSubs;
            if (tempCat != null) {
              final cat = _categories.where((c) => c.id == tempCat).toList();
              availableSubs = cat.isNotEmpty ? cat.first.subcategories : allSubcategories;
            } else {
              availableSubs = allSubcategories;
            }

            return SafeArea(
              child: Padding(
                padding: const EdgeInsets.all(20),
                child: SingleChildScrollView(
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      Center(
                        child: Container(
                          width: 40, height: 4,
                          decoration: BoxDecoration(color: Theme.of(context).colorScheme.outline.withValues(alpha: 0.3), borderRadius: BorderRadius.circular(2)),
                        ),
                      ),
                      const SizedBox(height: 16),
                      Text(AppStrings.tr('products.sort'), style: Theme.of(context).textTheme.titleSmall?.copyWith(fontWeight: FontWeight.w700)),
                      const SizedBox(height: 8),
                      Wrap(
                        spacing: 8,
                        children: [
                          _sortChip(null, AppStrings.tr('products.sort_latest'), tempSort, (v) => setSheetState(() => tempSort = v)),
                          _sortChip('best_selling', AppStrings.tr('products.sort_best_selling'), tempSort, (v) => setSheetState(() => tempSort = v)),
                          _sortChip('most_favorited', AppStrings.tr('products.sort_most_favorited'), tempSort, (v) => setSheetState(() => tempSort = v)),
                          _sortChip('top_rated', AppStrings.tr('products.sort_top_rated'), tempSort, (v) => setSheetState(() => tempSort = v)),
                        ],
                      ),
                      const SizedBox(height: 16),
                      if (_categories.isNotEmpty) ...[
                        Text(AppStrings.tr('products.filter_category'), style: Theme.of(context).textTheme.titleSmall?.copyWith(fontWeight: FontWeight.w700)),
                        const SizedBox(height: 8),
                        DropdownButtonFormField<int>(
                          initialValue: tempCat,
                          decoration: InputDecoration(hintText: AppStrings.tr('nav.categories'), contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10)),
                          isExpanded: true,
                          items: [
                            DropdownMenuItem<int>(value: null, child: Text(AppStrings.tr('common.view_all'))),
                            ..._categories.map((c) => DropdownMenuItem<int>(value: c.id, child: Text(c.name))),
                          ],
                          onChanged: (v) => setSheetState(() => tempCat = v),
                        ),
                        const SizedBox(height: 16),
                      ],
                      if (availableSubs.isNotEmpty) ...[
                        Text(AppStrings.tr('subcategory.title_generic'),
                            style: Theme.of(context).textTheme.titleSmall?.copyWith(fontWeight: FontWeight.w700)),
                        const SizedBox(height: 8),
                        DropdownButtonFormField<int>(
                          initialValue: tempSubcat,
                          decoration: InputDecoration(
                            hintText: AppStrings.tr('subcategory.title_generic'),
                            contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                          ),
                          isExpanded: true,
                          items: [
                            DropdownMenuItem<int>(value: null, child: Text(AppStrings.tr('common.view_all'))),
                            ...availableSubs.map(
                              (s) => DropdownMenuItem<int>(
                                value: s.id,
                                child: Text(s.name),
                              ),
                            ),
                          ],
                          onChanged: (v) => setSheetState(() => tempSubcat = v),
                        ),
                        const SizedBox(height: 16),
                      ],
                      if (_vendors.isNotEmpty) ...[
                        Text(AppStrings.tr('products.filter_vendor'), style: Theme.of(context).textTheme.titleSmall?.copyWith(fontWeight: FontWeight.w700)),
                        const SizedBox(height: 8),
                        DropdownButtonFormField<int>(
                          initialValue: tempVendor,
                          decoration: InputDecoration(hintText: AppStrings.tr('nav.stores'), contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10)),
                          isExpanded: true,
                          items: [
                            DropdownMenuItem<int>(value: null, child: Text(AppStrings.tr('common.view_all'))),
                            ..._vendors.map((v) => DropdownMenuItem<int>(value: v.id, child: Text(v.storeName))),
                          ],
                          onChanged: (v) => setSheetState(() => tempVendor = v),
                        ),
                      ],
                      const SizedBox(height: 16),
                      Text(AppStrings.tr('products.filter_discount'),
                          style: Theme.of(context).textTheme.titleSmall?.copyWith(fontWeight: FontWeight.w700)),
                      const SizedBox(height: 8),
                      Wrap(
                        spacing: 8,
                        children: [
                          _discountChip(
                            null,
                            AppStrings.tr('products.filter_discount_all'),
                            tempDiscount,
                            (v) => setSheetState(() => tempDiscount = v),
                          ),
                          _discountChip(
                            true,
                            AppStrings.tr('products.filter_discount_yes'),
                            tempDiscount,
                            (v) => setSheetState(() => tempDiscount = v),
                          ),
                          _discountChip(
                            false,
                            AppStrings.tr('products.filter_discount_no'),
                            tempDiscount,
                            (v) => setSheetState(() => tempDiscount = v),
                          ),
                        ],
                      ),
                      const SizedBox(height: 20),
                      Row(
                        children: [
                          Expanded(child: OutlinedButton(onPressed: () { Navigator.pop(ctx); _resetFilters(); }, child: Text(AppStrings.tr('common.cancel')))),
                          const SizedBox(width: 12),
                          Expanded(
                            child: FilledButton(
                              onPressed: () {
                                Navigator.pop(ctx);
                                setState(() {
                                  _selectedCategoryId = tempCat;
                                  _selectedSubcategoryId = tempSubcat;
                                  _selectedVendorId = tempVendor;
                                  _sort = tempSort;
                                  _hasDiscount = tempDiscount;
                                });
                                _loadProducts(reset: true);
                              },
                              child: Text(AppStrings.tr('common.save')),
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            );
          },
        );
      },
    );
  }

  Widget _sortChip(String? value, String label, String? current, ValueChanged<String?> onTap) {
    final selected = current == value;
    final colorScheme = Theme.of(context).colorScheme;
    return ChoiceChip(label: Text(label), selected: selected, onSelected: (_) => onTap(value),
      selectedColor: colorScheme.primary.withValues(alpha: 0.15));
  }

  Widget _discountChip(bool? value, String label, bool? current, ValueChanged<bool?> onTap) {
    final selected = current == value;
    final colorScheme = Theme.of(context).colorScheme;
    return ChoiceChip(
      label: Text(label),
      selected: selected,
      onSelected: (_) => onTap(value),
      selectedColor: colorScheme.primary.withValues(alpha: 0.15),
    );
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final title = widget.title ?? AppStrings.tr('products.title_generic');
    final hasActiveFilter = _selectedCategoryId != null ||
        _selectedSubcategoryId != null ||
        _selectedVendorId != null ||
        _sort != null ||
        _hasDiscount != null;

    return Scaffold(
      appBar: AppBar(
        title: Text(title),
        actions: [
          if (hasActiveFilter)
            IconButton(icon: const Icon(Icons.filter_list_off), tooltip: AppStrings.tr('common.cancel'), onPressed: _resetFilters),
          IconButton(icon: const Icon(Icons.tune), onPressed: () => _showFilterSheet(context)),
        ],
      ),
      body: _error != null && _products.isEmpty
          ? Center(
              child: Padding(
                padding: const EdgeInsets.all(24),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Icon(Icons.wifi_off_rounded, size: 48, color: colorScheme.outline),
                    const SizedBox(height: 12),
                    Text(AppStrings.tr('products.error_generic'), style: TextStyle(color: colorScheme.error), textAlign: TextAlign.center),
                    const SizedBox(height: 12),
                    FilledButton(onPressed: () => _loadProducts(reset: true), child: Text(AppStrings.tr('common.retry'))),
                  ],
                ),
              ),
            )
          : _products.isEmpty && !_loading
              ? Center(
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(Icons.search_off_rounded, size: 48, color: colorScheme.outline),
                      const SizedBox(height: 12),
                      Text(AppStrings.tr('products.empty_list'), style: TextStyle(color: colorScheme.outline)),
                    ],
                  ),
                )
              : RefreshIndicator(
                  onRefresh: () => _loadProducts(reset: true),
                  child: GridView.builder(
                    padding: const EdgeInsets.all(16),
                    gridDelegate: responsiveProductGrid(context),
                    itemCount: _products.length + (_hasMore && _products.isNotEmpty ? 1 : 0),
                    itemBuilder: (context, i) {
                      if (i >= _products.length) {
                        WidgetsBinding.instance.addPostFrameCallback((_) => _loadMore());
                        return const Center(child: Padding(padding: EdgeInsets.all(16), child: CircularProgressIndicator()));
                      }
                      final product = _products[i];
                      return ProductCard(
                        key: ValueKey('pl_${product.id}'),
                        product: product,
                        isFavourite: widget.favouriteService?.isFavourite(product.id) ?? false,
                        onToggleFavourite: widget.favouriteService != null ? () => widget.favouriteService!.toggle(product.id) : null,
                        onTap: () => Navigator.of(context).pushNamed('/product/${product.id}'),
                        onAddToCart: () => _addToCart(product),
                      );
                    },
                  ),
                ),
    );
  }
}
