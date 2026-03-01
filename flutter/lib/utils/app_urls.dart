import '../config/api_config.dart';

String? imageUrl(String? path) {
  if (path == null || path.isEmpty) return null;
  if (path.startsWith('http')) return path;
  return '$apiBaseUrl/storage/$path';
}
