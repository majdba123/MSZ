class ReviewModel {
  ReviewModel({
    required this.id,
    required this.rating,
    this.body,
    this.createdAt,
    this.userName,
    this.userId,
  });

  final int id;
  final int rating;
  final String? body;
  final String? createdAt;
  final String? userName;
  final int? userId;

  factory ReviewModel.fromJson(Map<String, dynamic> json) {
    final user = json['user'] as Map<String, dynamic>?;
    return ReviewModel(
      id: (json['id'] as num?)?.toInt() ?? 0,
      rating: (json['rating'] as num?)?.toInt() ?? 0,
      body: json['body']?.toString(),
      createdAt: json['created_at']?.toString(),
      userName: user?['name']?.toString() ?? json['user_name']?.toString(),
      userId: user != null ? (user['id'] as num?)?.toInt() : null,
    );
  }
}
