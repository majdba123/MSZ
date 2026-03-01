import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

/// Theme and locale like website: sz_theme (light/dark/system), locale (ar/en).
class AppSettingsService extends ChangeNotifier {
  AppSettingsService() {
    _load();
  }

  static const _keyTheme = 'sz_theme';
  static const _keyLocale = 'sz_locale';

  ThemeMode _themeMode = ThemeMode.system;
  Locale _locale = const Locale('en');

  ThemeMode get themeMode => _themeMode;
  Locale get locale => _locale;
  bool get isAr => _locale.languageCode == 'ar';

  Future<void> _load() async {
    final prefs = await SharedPreferences.getInstance();
    final t = prefs.getString(_keyTheme);
    if (t == 'dark') _themeMode = ThemeMode.dark;
    if (t == 'light') _themeMode = ThemeMode.light;
    if (t == 'system') _themeMode = ThemeMode.system;
    final l = prefs.getString(_keyLocale);
    if (l == 'ar') _locale = const Locale('ar');
    if (l == 'en') _locale = const Locale('en');
    notifyListeners();
  }

  Future<void> setThemeMode(ThemeMode mode) async {
    if (_themeMode == mode) return;
    _themeMode = mode;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_keyTheme, mode == ThemeMode.dark ? 'dark' : mode == ThemeMode.light ? 'light' : 'system');
    notifyListeners();
  }

  Future<void> setLocale(Locale locale) async {
    if (_locale.languageCode == locale.languageCode) return;
    _locale = locale;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_keyLocale, locale.languageCode);
    notifyListeners();
  }
}
