import 'package:flutter/material.dart';

import '../l10n/app_strings.dart';
import '../models/order_model.dart';
import '../services/order_service.dart';
import '../utils/app_urls.dart';

class OrderDetailScreen extends StatefulWidget {
  const OrderDetailScreen({super.key, required this.orderId});

  final int orderId;

  @override
  State<OrderDetailScreen> createState() => _OrderDetailScreenState();
}

class _OrderDetailScreenState extends State<OrderDetailScreen> {
  final OrderService _api = OrderService();
  OrderModel? _order;
  bool _loading = true;
  bool _cancelling = false;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final order = await _api.getOrder(widget.orderId);
      if (!mounted) return;
      setState(() {
        _order = order;
        _loading = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() => _loading = false);
    }
  }

  Future<void> _cancel() async {
    if (_order == null) return;
    setState(() => _cancelling = true);
    try {
      final ok = await _api.cancelOrder(_order!.id);
      if (!mounted) return;
      if (ok) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(AppStrings.tr('orders.cancelled'))),
        );
        _load();
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(e.toString())),
        );
      }
    } finally {
      if (mounted) setState(() => _cancelling = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return Scaffold(
      appBar: AppBar(title: Text(AppStrings.tr('orders.detail_title'))),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : _order == null
              ? Center(child: Text(AppStrings.tr('orders.not_found')))
              : RefreshIndicator(
                  onRefresh: _load,
                  child: SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    padding: const EdgeInsets.all(16),
                    child: _buildContent(colorScheme),
                  ),
                ),
    );
  }

  Widget _buildContent(ColorScheme colorScheme) {
    final o = _order!;
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _buildHeader(colorScheme, o),
        const SizedBox(height: 16),
        _buildItems(colorScheme, o),
        const SizedBox(height: 16),
        _buildTotals(colorScheme, o),
        if (o.canCancel) ...[
          const SizedBox(height: 24),
          SizedBox(
            width: double.infinity,
            height: 44,
            child: OutlinedButton(
              onPressed: _cancelling ? null : _cancel,
              style: OutlinedButton.styleFrom(foregroundColor: colorScheme.error, side: BorderSide(color: colorScheme.error)),
              child: _cancelling
                  ? SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: colorScheme.error))
                  : Text(AppStrings.tr('orders.cancel_order')),
            ),
          ),
        ],
        const SizedBox(height: 32),
      ],
    );
  }

  Widget _buildHeader(ColorScheme colorScheme, OrderModel o) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  '${AppStrings.tr('orders.order')} #${o.orderNumber ?? o.id}',
                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: colorScheme.onSurface),
                ),
                _statusBadge(o.status ?? 'pending', colorScheme),
              ],
            ),
            const SizedBox(height: 12),
            _infoRow(Icons.access_time, AppStrings.tr('orders.date'), o.createdAt ?? '-', colorScheme),
            if (o.vendorName != null) _infoRow(Icons.storefront_outlined, AppStrings.tr('orders.vendor'), o.vendorName!, colorScheme),
            _infoRow(Icons.payment, AppStrings.tr('orders.payment'), AppStrings.tr('orders.payment_cash'), colorScheme),
          ],
        ),
      ),
    );
  }

  Widget _infoRow(IconData icon, String label, String value, ColorScheme colorScheme) {
    return Padding(
      padding: const EdgeInsets.only(top: 6),
      child: Row(
        children: [
          Icon(icon, size: 16, color: colorScheme.outline),
          const SizedBox(width: 8),
          Text(label, style: TextStyle(fontSize: 13, color: colorScheme.outline)),
          const SizedBox(width: 8),
          Expanded(
            child: Text(
              value,
              style: TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: colorScheme.onSurface),
              textAlign: TextAlign.end,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildItems(ColorScheme colorScheme, OrderModel o) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              AppStrings.tr('orders.items_title'),
              style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700, color: colorScheme.onSurface),
            ),
            const SizedBox(height: 12),
            ...o.items.map((item) => _OrderItemTile(item: item, colorScheme: colorScheme)),
          ],
        ),
      ),
    );
  }

  Widget _buildTotals(ColorScheme colorScheme, OrderModel o) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            _totalRow(AppStrings.tr('cart.subtotal'), o.subtotal, colorScheme),
            if (o.couponCode != null && o.couponDiscount != null && o.couponDiscount! > 0) ...[
              const SizedBox(height: 4),
              _totalRow('${AppStrings.tr('orders.coupon')} (${o.couponCode})', -o.couponDiscount!, colorScheme),
            ],
            const SizedBox(height: 8),
            Divider(color: colorScheme.outlineVariant.withValues(alpha: 0.3)),
            const SizedBox(height: 8),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  AppStrings.tr('orders.total'),
                  style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700, color: colorScheme.onSurface),
                ),
                Text(
                  '${(o.total ?? 0).toStringAsFixed(0)} SYP',
                  style: TextStyle(fontSize: 17, fontWeight: FontWeight.w900, color: colorScheme.primary),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _totalRow(String label, double? amount, ColorScheme colorScheme) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: TextStyle(fontSize: 13, color: colorScheme.outline)),
        Text(
          '${(amount ?? 0).toStringAsFixed(0)} SYP',
          style: TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: colorScheme.onSurface),
        ),
      ],
    );
  }

  Widget _statusBadge(String status, ColorScheme colorScheme) {
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
      child: Text(AppStrings.tr('orders.status_$status'), style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: fg)),
    );
  }
}

class _OrderItemTile extends StatelessWidget {
  const _OrderItemTile({required this.item, required this.colorScheme});

  final OrderItemModel item;
  final ColorScheme colorScheme;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Row(
        children: [
          ClipRRect(
            borderRadius: BorderRadius.circular(8),
            child: SizedBox(
              width: 48,
              height: 48,
              child: AppNetworkImage(url: item.photoUrl, fit: BoxFit.cover),
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  item.productName ?? '#${item.productId}',
                  style: TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: colorScheme.onSurface),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                Text(
                  '${item.quantity} × ${(item.unitPrice ?? 0).toStringAsFixed(0)} SYP',
                  style: TextStyle(fontSize: 12, color: colorScheme.outline),
                ),
              ],
            ),
          ),
          Text(
            '${(item.lineTotal ?? 0).toStringAsFixed(0)} SYP',
            style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: colorScheme.onSurface),
          ),
        ],
      ),
    );
  }
}
