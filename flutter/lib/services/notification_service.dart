import 'dart:convert';

import 'package:flutter/foundation.dart';

import '../models/notification_model.dart';
import 'api_client.dart';

/// Manages notifications via the API.
class NotificationService extends ChangeNotifier {
  int _unreadCount = 0;
  int get unreadCount => _unreadCount;

  Future<void> fetchUnreadCount() async {
    try {
      final res = await ApiClient().get('/api/notifications?per_page=1');
      if (res.statusCode == 200) {
        final data = jsonDecode(res.body) as Map<String, dynamic>;
        _unreadCount = (data['unread_count'] as num?)?.toInt() ?? 0;
        notifyListeners();
      }
    } catch (e) {
      debugPrint('NotificationService.fetchUnreadCount error: $e');
    }
  }

  Future<NotificationPage> getPage({int page = 1, int perPage = 15}) async {
    final res = await ApiClient().get('/api/notifications?page=$page&per_page=$perPage');
    if (res.statusCode != 200) {
      return NotificationPage(items: [], unreadCount: _unreadCount, lastPage: 1);
    }
    final data = jsonDecode(res.body) as Map<String, dynamic>;
    _unreadCount = (data['unread_count'] as num?)?.toInt() ?? 0;

    final list = (data['data'] as List<dynamic>? ?? [])
        .map((e) => NotificationModel.fromJson(e as Map<String, dynamic>))
        .toList();

    final meta = data['meta'] as Map<String, dynamic>? ?? {};
    final lastPage = (meta['last_page'] as num?)?.toInt() ?? 1;

    notifyListeners();
    return NotificationPage(items: list, unreadCount: _unreadCount, lastPage: lastPage);
  }

  Future<void> markRead(String id) async {
    await ApiClient().patch('/api/notifications/$id/read');
    if (_unreadCount > 0) _unreadCount--;
    notifyListeners();
  }

  Future<int> markAllRead() async {
    final res = await ApiClient().post('/api/notifications/mark-all-read', {});
    final data = jsonDecode(res.body) as Map<String, dynamic>;
    final marked = (data['data']?['marked'] as num?)?.toInt() ?? 0;
    _unreadCount = 0;
    notifyListeners();
    return marked;
  }
}

class NotificationPage {
  NotificationPage({required this.items, required this.unreadCount, required this.lastPage});

  final List<NotificationModel> items;
  final int unreadCount;
  final int lastPage;
}
