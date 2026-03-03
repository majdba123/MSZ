import 'package:flutter/material.dart';

import '../l10n/app_strings.dart';
import '../models/category_model.dart';
import '../services/client_api_service.dart';
import '../widgets/category_card.dart';

class CategoriesListScreen extends StatefulWidget {
  const CategoriesListScreen({super.key});

  @override
  State<CategoriesListScreen> createState() => _CategoriesListScreenState();
}

class _CategoriesListScreenState extends State<CategoriesListScreen> {
  final ClientApiService _api = ClientApiService();
  List<CategoryModel> _categories = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    final list = await _api.getCategories();
    if (!mounted) return;
    setState(() {
      _categories = list;
      _loading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(AppStrings.tr('nav.categories'))),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _load,
              child: _categories.isEmpty
                  ? Center(child: Text(AppStrings.tr('home.empty_categories')))
                  : GridView.builder(
                      padding: const EdgeInsets.all(16),
                      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                        crossAxisCount: 2,
                        childAspectRatio: 1.1,
                        crossAxisSpacing: 12,
                        mainAxisSpacing: 12,
                      ),
                      itemCount: _categories.length,
                      itemBuilder: (context, i) {
                        final cat = _categories[i];
                        return CategoryCard(
                          key: ValueKey('cl_${cat.id}'),
                          category: cat,
                          onTap: () => Navigator.of(context).pushNamed('/category/${cat.id}'),
                        );
                      },
                    ),
            ),
    );
  }
}
