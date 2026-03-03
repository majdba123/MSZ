import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';

import 'l10n/app_strings.dart';
import 'screens/app_shell.dart';
import 'screens/category_detail_screen.dart';
import 'screens/login_screen.dart';
import 'screens/order_detail_screen.dart';
import 'screens/orders_list_screen.dart';
import 'screens/product_detail_screen.dart';
import 'screens/products_list_screen.dart';
import 'screens/register_screen.dart';
import 'screens/subcategory_detail_screen.dart';
import 'screens/vendor_detail_screen.dart';
import 'screens/vendors_list_screen.dart';
import 'services/app_settings_service.dart';
import 'services/auth_service.dart';
import 'services/cart_service.dart';
import 'services/favourite_service.dart';
import 'services/notification_service.dart';
import 'theme/app_theme.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  final authService = AuthService();
  final appSettings = AppSettingsService();
  final cartService = CartService();
  final favouriteService = FavouriteService();
  final notificationService = NotificationService();

  await authService.loadStored();

  if (authService.isLoggedIn) {
    favouriteService.loadIds();
    notificationService.fetchUnreadCount();
  }

  runApp(MszApp(
    authService: authService,
    appSettings: appSettings,
    cartService: cartService,
    favouriteService: favouriteService,
    notificationService: notificationService,
  ));
}

class MszApp extends StatefulWidget {
  const MszApp({
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
  State<MszApp> createState() => _MszAppState();
}

class _MszAppState extends State<MszApp> {
  @override
  void initState() {
    super.initState();
    widget.appSettings.addListener(_onSettingsChanged);
  }

  @override
  void dispose() {
    widget.appSettings.removeListener(_onSettingsChanged);
    super.dispose();
  }

  void _onSettingsChanged() {
    setState(() {
      AppStrings.locale = widget.appSettings.locale;
    });
  }

  @override
  Widget build(BuildContext context) {
    AppStrings.locale = widget.appSettings.locale;
    return MaterialApp(
      title: 'SyriaZone',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.theme,
      darkTheme: AppTheme.darkTheme,
      themeMode: widget.appSettings.themeMode,
      locale: widget.appSettings.locale,
      supportedLocales: const [Locale('en'), Locale('ar')],
      localizationsDelegates: const [
        GlobalMaterialLocalizations.delegate,
        GlobalWidgetsLocalizations.delegate,
        GlobalCupertinoLocalizations.delegate,
      ],
      initialRoute: widget.authService.isLoggedIn ? '/home' : '/login',
      onGenerateRoute: (settings) {
        final path = settings.name ?? '';
        final uri = Uri.parse(path);
        final segments = uri.pathSegments;
        if (segments.length == 2) {
          final id = int.tryParse(segments[1]);
          if (id != null) {
            if (segments[0] == 'product') {
              return MaterialPageRoute<void>(
                settings: settings,
                builder: (_) => ProductDetailScreen(
                  productId: id,
                  cartService: widget.cartService,
                  favouriteService: widget.favouriteService,
                  authService: widget.authService,
                ),
              );
            }
            if (segments[0] == 'category') {
              return MaterialPageRoute<void>(
                settings: settings,
                builder: (_) => CategoryDetailScreen(
                  categoryId: id,
                  cartService: widget.cartService,
                  favouriteService: widget.favouriteService,
                ),
              );
            }
            if (segments[0] == 'subcategory') {
              return MaterialPageRoute<void>(
                settings: settings,
                builder: (_) => SubcategoryDetailScreen(
                  subcategoryId: id,
                  cartService: widget.cartService,
                  favouriteService: widget.favouriteService,
                ),
              );
            }
            if (segments[0] == 'vendor') {
              return MaterialPageRoute<void>(
                settings: settings,
                builder: (_) => VendorDetailScreen(
                  vendorId: id,
                  cartService: widget.cartService,
                  favouriteService: widget.favouriteService,
                ),
              );
            }
            if (segments[0] == 'order') {
              return MaterialPageRoute<void>(
                settings: settings,
                builder: (_) => OrderDetailScreen(orderId: id),
              );
            }
          }
        }
        return null;
      },
      routes: {
        '/login': (context) => LoginScreen(authService: widget.authService),
        '/register': (context) => RegisterScreen(authService: widget.authService),
        '/home': (context) => AppShell(
              authService: widget.authService,
              appSettings: widget.appSettings,
              cartService: widget.cartService,
              favouriteService: widget.favouriteService,
              notificationService: widget.notificationService,
            ),
        '/products': (context) => ProductsListScreen(
              cartService: widget.cartService,
              favouriteService: widget.favouriteService,
            ),
        '/vendors': (context) => VendorsListScreen(),
        '/orders': (context) => const OrdersListScreen(),
      },
    );
  }
}
