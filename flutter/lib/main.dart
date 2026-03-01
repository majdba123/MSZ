import 'package:flutter/material.dart';

import 'l10n/app_strings.dart';
import 'screens/client_home_screen.dart';
import 'screens/login_screen.dart';
import 'screens/register_screen.dart';
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
      routes: {
        '/login': (context) => LoginScreen(authService: widget.authService),
        '/register': (context) => RegisterScreen(authService: widget.authService),
        '/home': (context) => ClientHomeScreen(
              authService: widget.authService,
              appSettings: widget.appSettings,
            ),
      },
    );
  }
}
