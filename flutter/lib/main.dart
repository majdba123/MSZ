import 'package:flutter/material.dart';

import 'screens/client_home_screen.dart';
import 'screens/login_screen.dart';
import 'screens/register_screen.dart';
import 'services/auth_service.dart';
import 'theme/app_theme.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  final authService = AuthService();
  await authService.loadStored();

  runApp(MszApp(authService: authService));
}

class MszApp extends StatelessWidget {
  const MszApp({super.key, required this.authService});

  final AuthService authService;

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'SyriaZone',
      theme: AppTheme.theme,
      initialRoute: authService.isLoggedIn ? '/home' : '/login',
      routes: {
        '/login': (context) => LoginScreen(authService: authService),
        '/register': (context) => RegisterScreen(authService: authService),
        '/home': (context) => ClientHomeScreen(authService: authService),
      },
    );
  }
}
