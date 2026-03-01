import '../utils/json_parsers.dart';

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
    final subs = toList(json['subcategories']);
    return CategoryModel(
      id: toInt(json['id']),
      name: toStringVal(json['name']),
      logo: toStringOrNull(json['logo']),
      subcategories: subs != null
          ? subs.map((s) => toMap(s)).whereType<Map<String, dynamic>>().map(SubcategoryModel.fromJson).toList()
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
      id: toInt(json['id']),
      name: toStringVal(json['name']),
      image: toStringOrNull(json['image']),
      categoryName: toStringOrNull(json['category_name']),
    );
  }
}
