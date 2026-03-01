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

  static double? _toDouble(dynamic v) {
    if (v == null) return null;
    if (v is num) return v.toDouble();
    if (v is String) return double.tryParse(v);
    return null;
  }

  static int _toInt(dynamic v, int fallback) {
    if (v == null) return fallback;
    if (v is int) return v;
    if (v is num) return v.toInt();
    if (v is String) return int.tryParse(v) ?? fallback;
    return fallback;
  }

  static int? _toIntNull(dynamic v) {
    if (v == null) return null;
    if (v is int) return v;
    if (v is num) return v.toInt();
    if (v is String) return int.tryParse(v);
    return null;
  }

  static String _str(dynamic v, [String fallback = '']) {
    if (v == null) return fallback;
    if (v is String) return v;
    return v.toString();
  }

  static Map<String, dynamic>? _map(dynamic v) {
    if (v == null) return null;
    if (v is Map<String, dynamic>) return v;
    if (v is Map) return Map<String, dynamic>.from(v);
    return null;
  }

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: _toInt(json['id'], 0),
      name: _str(json['name']),
      phoneNumber: _str(json['phone_number']),
      nationalId: json['national_id']?.toString(),
      email: json['email']?.toString(),
      avatar: json['avatar']?.toString(),
      avatarUrl: json['avatar_url']?.toString(),
      type: _toInt(json['type'], 0),
      cityId: _toIntNull(json['city_id']),
      city: _map(json['city']),
      latitude: _toDouble(json['latitude']),
      longitude: _toDouble(json['longitude']),
    );
  }
}
