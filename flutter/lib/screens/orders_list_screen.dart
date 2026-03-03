import 'package:flutter/material.dart';

import '../l10n/app_strings.dart';
import '../models/order_model.dart';
import '../services/order_service.dart';

class OrdersListScreen extends StatefulWidget {
  const OrdersListScreen({super.key});

  @override
  State<OrdersListScreen> createState() => _OrdersListScreenState();
}

class _OrdersListScreenState extends State<OrdersListScreen> {
  final OrderService _api = OrderService();
  List<OrderModel> _orders = [];
  bool _loading = true;
  String? _statusFilter;
  int _page = 1;
  int _lastPage = 1;
  bool _loadingMore = false;

  static const _statuses = ['pending', 'confirmed', 'completed', 'cancelled'];

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _page = 1;
    });
    try {
      final result = await _api.getOrders(page: 1, status: _statusFilter);
      if (!mounted) return;
      setState(() {
        _orders = result.orders;
        _lastPage = result.lastPage;
        _loading = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() => _loading = false);
    }
  }

  Future<void> _loadMore() async {
    if (_loadingMore || _page >= _lastPage) return;
    setState(() => _loadingMore = true);
    try {
      final result = await _api.getOrders(page: _page + 1, status: _statusFilter);
      if (!mounted) return;
      setState(() {
        _page++;
        _orders.addAll(result.orders);
        _lastPage = result.lastPage;
        _loadingMore = false;
      });
    } catch (_) {
      if (mounted) setState(() => _loadingMore = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return Scaffold(
      appBar: AppBar(title: Text(AppStrings.tr('orders.title'))),
      body: Column(
        children: [
          _buildFilters(colorScheme),
          Expanded(
            child: _loading
                ? const Center(child: CircularProgressIndicator())
                : _orders.isEmpty
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
                          child: ListView.separated(
                            padding: const EdgeInsets.all(16),
                            itemCount: _orders.length + (_loadingMore ? 1 : 0),
                            separatorBuilder: (_, __) => const SizedBox(height: 8),
                            itemBuilder: (context, i) {
                              if (i == _orders.length) {
                                return const Center(child: Padding(padding: EdgeInsets.all(16), child: CircularProgressIndicator()));
                              }
                              return _OrderTile(order: _orders[i], colorScheme: colorScheme);
                            },
                          ),
                        ),
                      ),
          ),
        ],
      ),
    );
  }

  Widget _buildFilters(ColorScheme colorScheme) {
    return SizedBox(
      height: 48,
      child: ListView(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
        children: [
          _FilterChip(
            label: AppStrings.tr('orders.filter_all'),
            selected: _statusFilter == null,
            colorScheme: colorScheme,
            onTap: () {
              _statusFilter = null;
              _load();
            },
          ),
          ..._statuses.map(
            (s) => Padding(
              padding: const EdgeInsetsDirectional.only(start: 8),
              child: _FilterChip(
                label: AppStrings.tr('orders.status_$s'),
                selected: _statusFilter == s,
                colorScheme: colorScheme,
                onTap: () {
                  _statusFilter = s;
                  _load();
                },
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEmpty(ColorScheme colorScheme) {
    return Center(
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(Icons.receipt_long_outlined, size: 64, color: colorScheme.outline.withValues(alpha: 0.3)),
          const SizedBox(height: 16),
          Text(
            AppStrings.tr('orders.empty'),
            style: TextStyle(fontSize: 16, fontWeight: FontWeight.w600, color: colorScheme.onSurface),
          ),
        ],
      ),
    );
  }
}

class _OrderTile extends StatelessWidget {
  const _OrderTile({required this.order, required this.colorScheme});

  final OrderModel order;
  final ColorScheme colorScheme;

  @override
  Widget build(BuildContext context) {
    return Card(
      child: InkWell(
        onTap: () => Navigator.of(context).pushNamed('/order/${order.id}'),
        borderRadius: BorderRadius.circular(16),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    '#${order.orderNumber ?? order.id}',
                    style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700, color: colorScheme.onSurface),
                  ),
                  _StatusBadge(status: order.status ?? 'pending', colorScheme: colorScheme),
                ],
              ),
              const SizedBox(height: 8),
              if (order.vendorName != null)
                Row(
                  children: [
                    Icon(Icons.storefront_outlined, size: 14, color: colorScheme.outline),
                    const SizedBox(width: 4),
                    Text(order.vendorName!, style: TextStyle(fontSize: 13, color: colorScheme.outline)),
                  ],
                ),
              if (order.createdAt != null)
                Padding(
                  padding: const EdgeInsets.only(top: 4),
                  child: Row(
                    children: [
                      Icon(Icons.access_time, size: 14, color: colorScheme.outline),
                      const SizedBox(width: 4),
                      Text(order.createdAt!, style: TextStyle(fontSize: 12, color: colorScheme.outline)),
                    ],
                  ),
                ),
              const SizedBox(height: 8),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    '${order.items.length} ${AppStrings.tr('orders.items')}',
                    style: TextStyle(fontSize: 13, color: colorScheme.outline),
                  ),
                  Text(
                    '${(order.total ?? 0).toStringAsFixed(0)} SYP',
                    style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800, color: colorScheme.primary),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _StatusBadge extends StatelessWidget {
  const _StatusBadge({required this.status, required this.colorScheme});

  final String status;
  final ColorScheme colorScheme;

  @override
  Widget build(BuildContext context) {
    Color bg;
    Color fg;
    switch (status) {
      case 'completed':
        bg = const Color(0xFF059669).withValues(alpha: 0.1);
        fg = const Color(0xFF059669);
      case 'cancelled':
        bg = colorScheme.error.withValues(alpha: 0.1);
        fg = colorScheme.error;
      case 'confirmed':
        bg = const Color(0xFF2563EB).withValues(alpha: 0.1);
        fg = const Color(0xFF2563EB);
      default:
        bg = const Color(0xFFF97316).withValues(alpha: 0.1);
        fg = const Color(0xFFF97316);
    }
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(999)),
      child: Text(
        AppStrings.tr('orders.status_$status'),
        style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: fg),
      ),
    );
  }
}

class _FilterChip extends StatelessWidget {
  const _FilterChip({required this.label, required this.selected, required this.colorScheme, required this.onTap});

  final String label;
  final bool selected;
  final ColorScheme colorScheme;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(999),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
        decoration: BoxDecoration(
          color: selected ? colorScheme.primary : colorScheme.surfaceContainerHighest.withValues(alpha: 0.5),
          borderRadius: BorderRadius.circular(999),
        ),
        child: Text(
          label,
          style: TextStyle(
            fontSize: 12,
            fontWeight: FontWeight.w600,
            color: selected ? colorScheme.onPrimary : colorScheme.onSurface,
          ),
        ),
      ),
    );
  }
}
