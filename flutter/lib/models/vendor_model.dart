import '../utils/json_parsers.dart';

class VendorModel {
  final int id;
  final String storeName;
  final String? logo;
  final String? description;
  final UserSummary? user;

  const VendorModel({
    required this.id,
    required this.storeName,
    this.logo,
    this.description,
    this.user,
  });

  factory VendorModel.fromJson(Map<String, dynamic> json) {
    final userMap = toMap(json['user']);
    return VendorModel(
      id: toInt(json['id']),
      storeName: toStringVal(json['store_name']),
      logo: toStringOrNull(json['logo']),
      description: toStringOrNull(json['description']),
      user: userMap != null ? UserSummary.fromJson(userMap) : null,
    );
  }
}

class UserSummary {
  final String name;

  UserSummary({required this.name});

  factory UserSummary.fromJson(Map<String, dynamic> json) {
    return UserSummary(name: toStringVal(json['name']));
  }
}
