class UserModel {
  final int id;
  final String name;
  final String phoneNumber;
  final String? nationalId;
  final String? email;
  final String? avatar;
  final String? avatarUrl;
  final int type;
  final int? cityId;
  final Map<String, dynamic>? city;
  final double? latitude;
  final double? longitude;

  const UserModel({
    required this.id,
    required this.name,
    required this.phoneNumber,
    this.nationalId,
    this.email,
    this.avatar,
    this.avatarUrl,
    required this.type,
    this.cityId,
    this.city,
    this.latitude,
    this.longitude,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] as int,
      name: json['name'] as String,
      phoneNumber: json['phone_number'] as String,
      nationalId: json['national_id'] as String?,
      email: json['email'] as String?,
      avatar: json['avatar'] as String?,
      avatarUrl: json['avatar_url'] as String?,
      type: json['type'] as int,
      cityId: json['city_id'] as int?,
      city: json['city'] as Map<String, dynamic>?,
      latitude: (json['latitude'] as num?)?.toDouble(),
      longitude: (json['longitude'] as num?)?.toDouble(),
    );
  }
}
