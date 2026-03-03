import 'package:flutter/material.dart';

import 'l10n/app_strings.dart';
import 'screens/categories_list_screen.dart';
import 'screens/category_detail_screen.dart';
import 'screens/client_home_screen.dart';
import 'screens/login_screen.dart';
import 'screens/product_detail_screen.dart';
import 'screens/products_list_screen.dart';
import 'screens/profile_screen.dart';
import 'screens/register_screen.dart';
import 'screens/subcategory_detail_screen.dart';
import 'screens/vendor_detail_screen.dart';
import 'screens/vendors_list_screen.dart';
import 'services/app_settings_service.dart';
import 'services/auth_service.dart';
import 'theme/app_theme.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  final authService = AuthService();
  final appSettings = AppSettingsService();
  await authService.loadStored();

  runApp(MszApp(authService: authService, appSettings: appSettings));
}

class MszApp extends StatefulWidget {
  const MszApp({super.key, required this.authService, required this.appSettings});

  final AuthService authService;
  final AppSettingsService appSettings;

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
      title: AppStrings.tr('SyriaZone'),
      theme: AppTheme.theme,
      darkTheme: AppTheme.darkTheme,
      themeMode: widget.appSettings.themeMode,
      locale: widget.appSettings.locale,
      supportedLocales: const [Locale('en'), Locale('ar')],
      builder: (context, child) {
        return Directionality(
          textDirection: widget.appSettings.isAr ? TextDirection.rtl : TextDirection.ltr,
          child: child!,
        );
      },
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
                builder: (_) => ProductDetailScreen(productId: id),
              );
            }
            if (segments[0] == 'category') {
              return MaterialPageRoute<void>(
                settings: settings,
                builder: (_) => CategoryDetailScreen(categoryId: id),
              );
            }
            if (segments[0] == 'subcategory') {
              return MaterialPageRoute<void>(
                settings: settings,
                builder: (_) => SubcategoryDetailScreen(subcategoryId: id),
              );
            }
            if (segments[0] == 'vendor') {
              return MaterialPageRoute<void>(
                settings: settings,
                builder: (_) => VendorDetailScreen(vendorId: id),
              );
            }
          }
        }
        return null;
      },
      routes: {
        '/login': (context) => LoginScreen(authService: widget.authService),
        '/register': (context) => RegisterScreen(authService: widget.authService),
        '/home': (context) => ClientHomeScreen(
              authService: widget.authService,
              appSettings: widget.appSettings,
            ),
        '/products': (context) => ProductsListScreen(),
        '/categories': (context) => CategoriesListScreen(),
        '/vendors': (context) => VendorsListScreen(),
        '/profile': (context) => ProfileScreen(authService: widget.authService),
      },
    );
  }
}
