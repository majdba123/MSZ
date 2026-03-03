import 'dart:convert';

import 'package:http/http.dart' as http;

import '../config/api_config.dart';

/// Singleton-style HTTP client. Set [token] once after login; all services
/// that use [ApiClient()] share the same instance and auth header.
class ApiClient {
  factory ApiClient() => _instance;
  ApiClient._();
  static final ApiClient _instance = ApiClient._();

  String? token;
  final String _base = apiBaseUrl;

  Map<String, String> get _headers => {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        if (token != null && token!.isNotEmpty) 'Authorization': 'Bearer $token',
      };

  Map<String, String> get _headersNoBody => {
        'Accept': 'application/json',
        if (token != null && token!.isNotEmpty) 'Authorization': 'Bearer $token',
      };

  Future<http.Response> get(String path) {
    return http.get(Uri.parse('$_base$path'), headers: _headers);
  }

  Future<http.Response> post(String path, Map<String, dynamic> body) {
    return http.post(
      Uri.parse('$_base$path'),
      headers: _headers,
      body: jsonEncode(body),
    );
  }

  Future<http.Response> patch(String path, [Map<String, dynamic>? body]) {
    return http.patch(
      Uri.parse('$_base$path'),
      headers: _headers,
      body: body != null ? jsonEncode(body) : null,
    );
  }

  Future<http.Response> delete(String path) {
    return http.delete(Uri.parse('$_base$path'), headers: _headersNoBody);
  }

  Future<http.StreamedResponse> postMultipart(
    String path,
    Map<String, String> fields, {
    Map<String, String>? files,
  }) async {
    final req = http.MultipartRequest('POST', Uri.parse('$_base$path'));
    req.headers.addAll(_headersNoBody);
    req.fields.addAll(fields);
    if (files != null) {
      for (final entry in files.entries) {
        req.files.add(await http.MultipartFile.fromPath(entry.key, entry.value));
      }
    }
    return req.send();
  }
}
