import 'package:flutter/material.dart';

import '../l10n/app_strings.dart';
import '../services/auth_service.dart';
import '../services/cart_service.dart';
import '../theme/app_theme.dart';
import '../utils/app_urls.dart';

class CartScreen extends StatefulWidget {
  const CartScreen({super.key, required this.cartService, required this.authService});

  final CartService cartService;
  final AuthService authService;

  @override
  State<CartScreen> createState() => _CartScreenState();
}

class _CartScreenState extends State<CartScreen> {
  final _couponController = TextEditingController();
  bool _checkingOut = false;
  bool _orderPlaced = false;

  @override
  void initState() {
    super.initState();
    widget.cartService.addListener(_refresh);
  }

  @override
  void dispose() {
    widget.cartService.removeListener(_refresh);
    _couponController.dispose();
    super.dispose();
  }

  void _refresh() {
    if (mounted) setState(() {});
  }

  Future<void> _checkout() async {
    if (!widget.authService.isLoggedIn) {
      _showSnack(AppStrings.tr('cart.login_required'));
      return;
    }
    setState(() => _checkingOut = true);
    try {
      await widget.cartService.checkout(couponCode: _couponController.text);
      if (!mounted) return;
      setState(() {
        _checkingOut = false;
        _orderPlaced = true;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() => _checkingOut = false);
      _showSnack(e.toString().replaceFirst('Exception: ', ''));
    }
  }

  void _showSnack(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg)));
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final cart = widget.cartService;

    if (_orderPlaced) return _buildOrderPlaced(colorScheme);

    return Scaffold(
      appBar: AppBar(
        title: Text(AppStrings.tr('nav.cart')),
        actions: [
          if (!cart.isEmpty)
            TextButton.icon(
              onPressed: () => cart.clear(),
              icon: Icon(Icons.delete_sweep_outlined, size: 18, color: colorScheme.error),
              label: Text(AppStrings.tr('cart.clear'), style: TextStyle(color: colorScheme.error)),
            ),
        ],
      ),
      body: cart.isEmpty ? _buildEmpty(colorScheme) : _buildCart(colorScheme, cart),
    );
  }

  Widget _buildOrderPlaced(ColorScheme colorScheme) {
    return Scaffold(
      appBar: AppBar(title: Text(AppStrings.tr('nav.cart'))),
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(32),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                width: 88,
                height: 88,
                decoration: BoxDecoration(
                  gradient: const LinearGradient(colors: [Color(0xFF059669), Color(0xFF10B981)]),
                  shape: BoxShape.circle,
                  boxShadow: [
                    BoxShadow(
                      color: const Color(0xFF059669).withValues(alpha: 0.3),
                      blurRadius: 20,
                      offset: const Offset(0, 8),
                    ),
                  ],
                ),
                child: const Icon(Icons.check_rounded, size: 44, color: Colors.white),
              ),
              const SizedBox(height: 24),
              Text(
                AppStrings.tr('cart.order_placed'),
                style: TextStyle(fontSize: 22, fontWeight: FontWeight.w800, color: colorScheme.onSurface, letterSpacing: -0.3),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 8),
              Text(
                AppStrings.tr('cart.order_placed_sub'),
                style: TextStyle(fontSize: 14, color: colorScheme.outline),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 28),
              SizedBox(
                width: 220,
                height: 48,
                child: FilledButton(
                  onPressed: () => setState(() => _orderPlaced = false),
                  child: Text(AppStrings.tr('cart.continue_shopping')),
                ),
              ),
              const SizedBox(height: 10),
              SizedBox(
                width: 220,
                height: 48,
                child: OutlinedButton(
                  onPressed: () => Navigator.of(context).pushNamed('/orders'),
                  child: Text(AppStrings.tr('cart.view_orders')),
                ),
              ),
            ],
          ),
        ),
      ),
    );
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
            child: Icon(Icons.shopping_cart_outlined, size: 40, color: colorScheme.outline.withValues(alpha: 0.4)),
          ),
          const SizedBox(height: 20),
          Text(
            AppStrings.tr('cart.empty'),
            style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: colorScheme.onSurface),
          ),
          const SizedBox(height: 6),
          Text(
            AppStrings.tr('cart.empty_sub'),
            style: TextStyle(fontSize: 14, color: colorScheme.outline),
          ),
        ],
      ),
    );
  }

  Widget _buildCart(ColorScheme colorScheme, CartService cart) {
    return Column(
      children: [
        Expanded(
          child: ListView.separated(
            padding: const EdgeInsets.all(16),
            itemCount: cart.items.length,
            separatorBuilder: (_, __) => const SizedBox(height: 10),
            itemBuilder: (context, i) {
              final item = cart.items[i];
              return _CartItemTile(
                item: item,
                colorScheme: colorScheme,
                onRemove: () => cart.removeItem(item.productId),
                onInc: () => cart.updateQuantity(item.productId, item.quantity + 1),
                onDec: () => cart.updateQuantity(item.productId, item.quantity - 1),
              );
            },
          ),
        ),
        _buildFooter(colorScheme, cart),
      ],
    );
  }

  Widget _buildFooter(ColorScheme colorScheme, CartService cart) {
    return SafeArea(
      top: false,
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: colorScheme.surface,
          border: Border(top: BorderSide(color: colorScheme.outlineVariant.withValues(alpha: 0.3))),
          boxShadow: [
            BoxShadow(color: Colors.black.withValues(alpha: 0.04), blurRadius: 8, offset: const Offset(0, -2)),
          ],
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            TextField(
              controller: _couponController,
              decoration: InputDecoration(
                hintText: AppStrings.tr('cart.coupon_hint'),
                prefixIcon: Icon(Icons.local_offer_outlined, size: 20, color: colorScheme.outline),
                isDense: true,
                contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
              ),
            ),
            const SizedBox(height: 14),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(AppStrings.tr('cart.subtotal'), style: TextStyle(fontSize: 14, color: colorScheme.outline)),
                Text(
                  '${cart.subtotal.toStringAsFixed(0)} SYP',
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: colorScheme.onSurface, letterSpacing: -0.3),
                ),
              ],
            ),
            const SizedBox(height: 6),
            Row(
              children: [
                Icon(Icons.info_outline_rounded, size: 14, color: colorScheme.outline),
                const SizedBox(width: 4),
                Expanded(
                  child: Text(
                    AppStrings.tr('cart.payment_cash'),
                    style: TextStyle(fontSize: 12, color: colorScheme.outline),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 14),
            SizedBox(
              width: double.infinity,
              height: 50,
              child: FilledButton.icon(
                onPressed: _checkingOut ? null : _checkout,
                icon: _checkingOut
                    ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                    : const Icon(Icons.shopping_bag_outlined, size: 20),
                label: Text(AppStrings.tr('cart.checkout'), style: const TextStyle(fontSize: 16)),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _CartItemTile extends StatelessWidget {
  const _CartItemTile({
    required this.item,
    required this.colorScheme,
    required this.onRemove,
    required this.onInc,
    required this.onDec,
  });

  final CartItem item;
  final ColorScheme colorScheme;
  final VoidCallback onRemove;
  final VoidCallback onInc;
  final VoidCallback onDec;

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: colorScheme.surface,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: colorScheme.outlineVariant.withValues(alpha: 0.5)),
      ),
      padding: const EdgeInsets.all(12),
      child: Row(
        children: [
          ClipRRect(
            borderRadius: BorderRadius.circular(10),
            child: SizedBox(
              width: 72,
              height: 72,
              child: AppNetworkImage(url: item.photo, fit: BoxFit.cover),
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  item.name,
                  style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: colorScheme.onSurface),
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 4),
                Text(
                  '${item.price.toStringAsFixed(0)} SYP',
                  style: TextStyle(fontSize: 14, color: AppTheme.brandPrimary, fontWeight: FontWeight.w800),
                ),
                const SizedBox(height: 8),
                Row(
                  children: [
                    _QtyButton(icon: Icons.remove, onTap: onDec, colorScheme: colorScheme),
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 12),
                      child: Text(
                        '${item.quantity}',
                        style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700, color: colorScheme.onSurface),
                      ),
                    ),
                    _QtyButton(icon: Icons.add, onTap: onInc, colorScheme: colorScheme),
                    const Spacer(),
                    Text(
                      '${item.lineTotal.toStringAsFixed(0)} SYP',
                      style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: colorScheme.onSurface),
                    ),
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(width: 4),
          IconButton(
            onPressed: onRemove,
            icon: Icon(Icons.close_rounded, size: 20, color: colorScheme.error),
            style: IconButton.styleFrom(
              backgroundColor: colorScheme.errorContainer.withValues(alpha: 0.3),
              minimumSize: const Size(32, 32),
              padding: EdgeInsets.zero,
            ),
          ),
        ],
      ),
    );
  }
}

class _QtyButton extends StatelessWidget {
  const _QtyButton({required this.icon, required this.onTap, required this.colorScheme});

  final IconData icon;
  final VoidCallback onTap;
  final ColorScheme colorScheme;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: colorScheme.surfaceContainerHighest.withValues(alpha: 0.5),
      borderRadius: BorderRadius.circular(8),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(8),
        child: SizedBox(
          width: 30,
          height: 30,
          child: Icon(icon, size: 16, color: colorScheme.onSurface),
        ),
      ),
    );
  }
}
