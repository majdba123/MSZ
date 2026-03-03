import 'package:flutter/material.dart';

import '../l10n/app_strings.dart';
import '../models/notification_model.dart';
import '../services/notification_service.dart';

class NotificationsScreen extends StatefulWidget {
  const NotificationsScreen({super.key, required this.notificationService});

  final NotificationService notificationService;

  @override
  State<NotificationsScreen> createState() => _NotificationsScreenState();
}

class _NotificationsScreenState extends State<NotificationsScreen> {
  List<NotificationModel> _items = [];
  bool _loading = true;
  String? _error;
  int _page = 1;
  int _lastPage = 1;
  bool _loadingMore = false;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _error = null;
      _page = 1;
    });
    try {
      final result = await widget.notificationService.getPage(page: 1);
      if (!mounted) return;
      setState(() {
        _items = result.items;
        _lastPage = result.lastPage;
        _loading = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _loading = false;
        _error = e.toString();
      });
    }
  }

  Future<void> _loadMore() async {
    if (_loadingMore || _page >= _lastPage) return;
    setState(() => _loadingMore = true);
    try {
      final result = await widget.notificationService.getPage(page: _page + 1);
      if (!mounted) return;
      setState(() {
        _page++;
        _items.addAll(result.items);
        _lastPage = result.lastPage;
        _loadingMore = false;
      });
    } catch (_) {
      if (mounted) setState(() => _loadingMore = false);
    }
  }

  Future<void> _markAllRead() async {
    await widget.notificationService.markAllRead();
    _load();
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return Scaffold(
      appBar: AppBar(
        title: Text(AppStrings.tr('notifications.title')),
        actions: [
          if (_items.any((n) => !n.isRead))
            TextButton.icon(
              onPressed: _markAllRead,
              icon: Icon(Icons.done_all_rounded, size: 18, color: colorScheme.primary),
              label: Text(AppStrings.tr('notifications.mark_all_read')),
            ),
        ],
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : _error != null
              ? _buildError(colorScheme)
              : _items.isEmpty
                  ? _buildEmpty(colorScheme)
                  : RefreshIndicator(
                      onRefresh: _load,
                      child: NotificationListener<ScrollNotification>(
                        onNotification: (n) {
                          if (n is ScrollEndNotification && n.metrics.extentAfter < 200) {
                            _loadMore();
                          }
                          return false;
                        },
                        child: ListView.builder(
                          padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 12),
                          itemCount: _items.length + (_loadingMore ? 1 : 0),
                          itemBuilder: (context, i) {
                            if (i == _items.length) {
                              return const Center(
                                child: Padding(padding: EdgeInsets.all(16), child: CircularProgressIndicator()),
                              );
                            }
                            return Padding(
                              padding: const EdgeInsets.only(bottom: 8),
                              child: _NotificationTile(
                                notification: _items[i],
                                colorScheme: colorScheme,
                                onTap: () => _onTap(_items[i]),
                              ),
                            );
                          },
                        ),
                      ),
                    ),
    );
  }

  void _onTap(NotificationModel n) {
    if (!n.isRead) {
      widget.notificationService.markRead(n.id);
      setState(() {});
    }
    if (n.actionType == 'product' && n.actionId != null) {
      Navigator.of(context).pushNamed('/product/${n.actionId}');
    } else if (n.actionType == 'order' && n.actionId != null) {
      Navigator.of(context).pushNamed('/order/${n.actionId}');
    }
  }

  Widget _buildEmpty(ColorScheme colorScheme) {
    return Center(
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: 80,
            height: 80,
            decoration: BoxDecoration(
              color: colorScheme.surfaceContainerHighest.withValues(alpha: 0.5),
              shape: BoxShape.circle,
            ),
            child: Icon(Icons.notifications_off_outlined, size: 40, color: colorScheme.outline.withValues(alpha: 0.4)),
          ),
          const SizedBox(height: 20),
          Text(
            AppStrings.tr('notifications.empty'),
            style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: colorScheme.onSurface),
          ),
        ],
      ),
    );
  }

  Widget _buildError(ColorScheme colorScheme) {
    return Center(
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(Icons.error_outline_rounded, size: 48, color: colorScheme.error),
          const SizedBox(height: 12),
          Text(_error!, style: TextStyle(color: colorScheme.error), textAlign: TextAlign.center),
          const SizedBox(height: 16),
          FilledButton(onPressed: _load, child: Text(AppStrings.tr('common.retry'))),
        ],
      ),
    );
  }
}

class _NotificationTile extends StatelessWidget {
  const _NotificationTile({
    required this.notification,
    required this.colorScheme,
    required this.onTap,
  });

  final NotificationModel notification;
  final ColorScheme colorScheme;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: notification.isRead ? colorScheme.surface : colorScheme.primary.withValues(alpha: 0.04),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(
          color: notification.isRead
              ? colorScheme.outlineVariant.withValues(alpha: 0.5)
              : colorScheme.primary.withValues(alpha: 0.15),
        ),
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(14),
          child: Padding(
            padding: const EdgeInsets.all(14),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  width: 42,
                  height: 42,
                  decoration: BoxDecoration(
                    color: notification.isRead
                        ? colorScheme.surfaceContainerHighest.withValues(alpha: 0.5)
                        : colorScheme.primary.withValues(alpha: 0.1),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  alignment: Alignment.center,
                  child: Icon(
                    _icon,
                    size: 20,
                    color: notification.isRead ? colorScheme.outline : colorScheme.primary,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      if (notification.senderName != null)
                        Text(
                          notification.senderName!,
                          style: TextStyle(
                            fontSize: 13,
                            fontWeight: FontWeight.w700,
                            color: colorScheme.onSurface,
                          ),
                        ),
                      Text(
                        notification.body,
                        style: TextStyle(
                          fontSize: 13,
                          color: notification.isRead ? colorScheme.outline : colorScheme.onSurface,
                          height: 1.4,
                        ),
                        maxLines: 3,
                        overflow: TextOverflow.ellipsis,
                      ),
                      if (notification.sentAt != null)
                        Padding(
                          padding: const EdgeInsets.only(top: 6),
                          child: Text(
                            notification.sentAt!,
                            style: TextStyle(fontSize: 11, color: colorScheme.outline),
                          ),
                        ),
                    ],
                  ),
                ),
                if (!notification.isRead)
                  Padding(
                    padding: const EdgeInsets.only(top: 4),
                    child: Container(
                      width: 8,
                      height: 8,
                      decoration: BoxDecoration(
                        color: colorScheme.primary,
                        shape: BoxShape.circle,
                      ),
                    ),
                  ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  IconData get _icon {
    switch (notification.actionType) {
      case 'product':
        return Icons.inventory_2_outlined;
      case 'order':
        return Icons.receipt_long_outlined;
      default:
        return Icons.notifications_outlined;
    }
  }
}
