class CategoryModel {
  final int id;
  final String name;
  final String? logo;
  final List<SubcategoryModel> subcategories;

  const CategoryModel({
    required this.id,
    required this.name,
    this.logo,
    this.subcategories = const [],
  });

  factory CategoryModel.fromJson(Map<String, dynamic> json) {
    final subs = json['subcategories'] as List<dynamic>?;
    return CategoryModel(
      id: json['id'] as int,
      name: json['name'] as String? ?? '',
      logo: json['logo'] as String?,
      subcategories: subs != null
          ? subs.map((s) => SubcategoryModel.fromJson(s as Map<String, dynamic>)).toList()
          : [],
    );
  }
}

class SubcategoryModel {
  final int id;
  final String name;
  final String? image;
  final String? categoryName;

  const SubcategoryModel({
    required this.id,
    required this.name,
    this.image,
    this.categoryName,
  });

  factory SubcategoryModel.fromJson(Map<String, dynamic> json) {
    return SubcategoryModel(
      id: json['id'] as int,
      name: json['name'] as String? ?? '',
      image: json['image'] as String?,
      categoryName: json['category_name'] as String?,
    );
  }
}
