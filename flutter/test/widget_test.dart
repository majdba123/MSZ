import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';

import 'package:msz_app/main.dart';
import 'package:msz_app/services/app_settings_service.dart';
import 'package:msz_app/services/auth_service.dart';
import 'package:msz_app/services/cart_service.dart';
import 'package:msz_app/services/favourite_service.dart';
import 'package:msz_app/services/notification_service.dart';

void main() {
  testWidgets('App loads', (WidgetTester tester) async {
    final authService = AuthService();
    final appSettings = AppSettingsService();
    final cartService = CartService();
    final favouriteService = FavouriteService();
    final notificationService = NotificationService();
    await tester.pumpWidget(MszApp(
      authService: authService,
      appSettings: appSettings,
      cartService: cartService,
      favouriteService: favouriteService,
      notificationService: notificationService,
    ));
    await tester.pumpAndSettle();
    expect(find.byType(MaterialApp), findsOneWidget);
  });
}
