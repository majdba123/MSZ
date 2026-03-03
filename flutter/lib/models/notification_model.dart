class NotificationModel {
  NotificationModel({
    required this.id,
    required this.body,
    this.senderName,
    this.sentAt,
    this.readAt,
    this.actionType,
    this.actionId,
  });

  final String id;
  final String body;
  final String? senderName;
  final String? sentAt;
  final String? readAt;
  final String? actionType;
  final int? actionId;

  bool get isRead => readAt != null;

  factory NotificationModel.fromJson(Map<String, dynamic> json) => NotificationModel(
        id: json['id']?.toString() ?? '',
        body: json['body']?.toString() ?? '',
        senderName: json['sender_name']?.toString(),
        sentAt: json['sent_at']?.toString(),
        readAt: json['read_at']?.toString(),
        actionType: json['action_type']?.toString(),
        actionId: json['action_id'] is num ? (json['action_id'] as num).toInt() : null,
      );
}
