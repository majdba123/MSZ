import 'dart:convert';

import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../config/api_config.dart';
import '../models/user_model.dart';
import 'api_client.dart';

class AuthService {
  AuthService() : _client = ApiClient();

  final ApiClient _client;
  static const _keyToken = 'auth_token';
  static const _keyUser = 'auth_user';

  String? _token;
  UserModel? _user;

  String? get token => _token;
  UserModel? get user => _user;
  bool get isLoggedIn => _token != null && _token!.isNotEmpty;

  Future<void> loadStored() async {
    final prefs = await SharedPreferences.getInstance();
    _token = prefs.getString(_keyToken);
    final userJson = prefs.getString(_keyUser);
    if (userJson != null) {
      try {
        _user = UserModel.fromJson(
          jsonDecode(userJson) as Map<String, dynamic>,
        );
      } catch (e, st) {
        debugPrint('AuthService.loadStored parse error: $e');
        debugPrint(st.toString());
      }
    }
    _client.token = _token;
  }

  Future<void> setToken(String t) async {
    _token = t;
    _client.token = t;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_keyToken, t);
  }

  Future<void> setUser(UserModel u) async {
    _user = u;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_keyUser, jsonEncode(_userToJson(u)));
  }

  Map<String, dynamic> _userToJson(UserModel u) => {
        'id': u.id,
        'name': u.name,
        'phone_number': u.phoneNumber,
        'national_id': u.nationalId,
        'email': u.email,
        'avatar': u.avatar,
        'avatar_url': u.avatarUrl,
        'type': u.type,
        'city_id': u.cityId,
        'city': u.city,
        'latitude': u.latitude,
        'longitude': u.longitude,
      };

  Future<void> clear() async {
    _token = null;
    _user = null;
    _client.token = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_keyToken);
    await prefs.remove(_keyUser);
  }

  /// Returns map with 'user' and 'token' on success; throws on error.
  Future<Map<String, dynamic>> login(String phoneNumber, String password) async {
    final res = await _client.post('/api/auth/login', {
      'phone_number': phoneNumber.trim(),
      'password': password,
    });
    return _handleAuthResponse(res);
  }

  /// Returns map with 'user' and 'token' on success; throws on error.
  Future<Map<String, dynamic>> register(Map<String, dynamic> body) async {
    final res = await _client.post('/api/auth/register', body);
    return _handleAuthResponse(res);
  }

  Map<String, dynamic> _handleAuthResponse(dynamic res) {
    final status = res.statusCode;
    final data = jsonDecode(res.body) as Map<String, dynamic>;
    if (status >= 200 && status < 300) {
      final d = data['data'] as Map<String, dynamic>?;
      if (d == null) throw Exception(data['message'] ?? 'Invalid response');
      final token = d['token'] as String?;
      final userJson = d['user'] as Map<String, dynamic>?;
      if (token == null || userJson == null) {
        throw Exception('Missing token or user in response');
      }
      return {'token': token, 'user': userJson};
    }
    final message = data['message'] as String?;
    final errors = data['errors'] as Map<String, dynamic>?;
    if (errors != null && errors.isNotEmpty) {
      final first = errors.values.first;
      final msg = first is List ? first.first.toString() : first.toString();
      throw Exception(msg);
    }
    throw Exception(message ?? 'Request failed');
  }

  Future<void> logout() async {
    if (_token != null) {
      try {
        await _client.post('/api/auth/logout', {});
      } catch (_) {}
    }
    await clear();
  }
}
