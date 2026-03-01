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
    return VendorModel(
      id: json['id'] as int,
      storeName: json['store_name'] as String? ?? '',
      logo: json['logo'] as String?,
      description: json['description'] as String?,
      user: json['user'] != null ? UserSummary.fromJson(json['user'] as Map<String, dynamic>) : null,
    );
  }
}

class UserSummary {
  final String name;

  UserSummary({required this.name});

  factory UserSummary.fromJson(Map<String, dynamic> json) {
    return UserSummary(name: json['name'] as String? ?? '');
  }
}
