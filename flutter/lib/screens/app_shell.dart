import 'package:flutter/material.dart';

import '../l10n/app_strings.dart';
import '../services/app_settings_service.dart';
import '../services/auth_service.dart';
import '../services/cart_service.dart';
import '../services/favourite_service.dart';
import '../services/notification_service.dart';
import 'cart_screen.dart';
import 'categories_list_screen.dart';
import 'client_home_screen.dart';
import 'notifications_screen.dart';
import 'profile_screen.dart';

class AppShell extends StatefulWidget {
  const AppShell({
    super.key,
    required this.authService,
    required this.appSettings,
    required this.cartService,
    required this.favouriteService,
    required this.notificationService,
  });

  final AuthService authService;
  final AppSettingsService appSettings;
  final CartService cartService;
  final FavouriteService favouriteService;
  final NotificationService notificationService;

  @override
  State<AppShell> createState() => _AppShellState();
}

class _AppShellState extends State<AppShell> {
  int _currentIndex = 0;
  late final List<Widget> _screens;

  @override
  void initState() {
    super.initState();
    _screens = [
      ClientHomeScreen(
        authService: widget.authService,
        appSettings: widget.appSettings,
        cartService: widget.cartService,
        favouriteService: widget.favouriteService,
      ),
      CategoriesListScreen(),
      CartScreen(
        cartService: widget.cartService,
        authService: widget.authService,
      ),
      NotificationsScreen(notificationService: widget.notificationService),
      ProfileScreen(
        authService: widget.authService,
        favouriteService: widget.favouriteService,
      ),
    ];
    widget.cartService.addListener(_onServicesChanged);
    widget.notificationService.addListener(_onServicesChanged);

    if (widget.authService.isLoggedIn) {
      if (!widget.favouriteService.isLoaded) widget.favouriteService.loadIds();
      widget.notificationService.fetchUnreadCount();
    }
  }

  @override
  void dispose() {
    widget.cartService.removeListener(_onServicesChanged);
    widget.notificationService.removeListener(_onServicesChanged);
    super.dispose();
  }

  void _onServicesChanged() {
    if (mounted) setState(() {});
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: IndexedStack(index: _currentIndex, children: _screens),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _currentIndex,
        onDestinationSelected: (i) => setState(() => _currentIndex = i),
        labelBehavior: NavigationDestinationLabelBehavior.alwaysShow,
        destinations: [
          NavigationDestination(
            icon: const Icon(Icons.home_outlined),
            selectedIcon: const Icon(Icons.home_rounded),
            label: AppStrings.tr('nav.home'),
          ),
          NavigationDestination(
            icon: const Icon(Icons.category_outlined),
            selectedIcon: const Icon(Icons.category_rounded),
            label: AppStrings.tr('nav.categories'),
          ),
          NavigationDestination(
            icon: Badge(
              isLabelVisible: widget.cartService.itemCount > 0,
              label: Text(_badgeText(widget.cartService.itemCount)),
              child: const Icon(Icons.shopping_cart_outlined),
            ),
            selectedIcon: Badge(
              isLabelVisible: widget.cartService.itemCount > 0,
              label: Text(_badgeText(widget.cartService.itemCount)),
              child: const Icon(Icons.shopping_cart_rounded),
            ),
            label: AppStrings.tr('nav.cart'),
          ),
          NavigationDestination(
            icon: Badge(
              isLabelVisible: widget.notificationService.unreadCount > 0,
              label: Text(_badgeText(widget.notificationService.unreadCount)),
              child: const Icon(Icons.notifications_outlined),
            ),
            selectedIcon: Badge(
              isLabelVisible: widget.notificationService.unreadCount > 0,
              label: Text(_badgeText(widget.notificationService.unreadCount)),
              child: const Icon(Icons.notifications_rounded),
            ),
            label: AppStrings.tr('nav.notifications'),
          ),
          NavigationDestination(
            icon: const Icon(Icons.person_outlined),
            selectedIcon: const Icon(Icons.person_rounded),
            label: AppStrings.tr('nav.profile'),
          ),
        ],
      ),
    );
  }

  String _badgeText(int count) => count > 99 ? '99+' : count.toString();
}
