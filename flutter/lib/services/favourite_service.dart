import 'dart:convert';

import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;

import 'api_client.dart';

/// Manages product favourites via the API. Caches favourite product IDs
/// in memory for instant UI checks.
class FavouriteService extends ChangeNotifier {
  final Set<int> _ids = {};
  bool _loaded = false;

  Set<int> get ids => Set.unmodifiable(_ids);
  bool isFavourite(int productId) => _ids.contains(productId);
  bool get isLoaded => _loaded;

  /// Fetches the list of favourite product IDs from the API.
  Future<void> loadIds() async {
    try {
      final res = await ApiClient().get('/api/favourites/ids');
      if (res.statusCode == 200) {
        final data = jsonDecode(res.body) as Map<String, dynamic>;
        final list = data['data'] as List<dynamic>? ?? [];
        _ids
          ..clear()
          ..addAll(list.map((e) => (e as num).toInt()));
        _loaded = true;
        notifyListeners();
      }
    } catch (e) {
      debugPrint('FavouriteService.loadIds error: $e');
    }
  }

  /// Toggles favourite status. Returns the new favourited state.
  Future<bool> toggle(int productId) async {
    final res = await ApiClient().post('/api/favourites/$productId', {});
    final data = jsonDecode(res.body) as Map<String, dynamic>;
    final favourited = data['favourited'] == true;
    if (favourited) {
      _ids.add(productId);
    } else {
      _ids.remove(productId);
    }
    notifyListeners();
    return favourited;
  }

  /// Fetches full favourite product list.
  Future<List<Map<String, dynamic>>> getAll() async {
    final res = await ApiClient().get('/api/favourites');
    if (res.statusCode != 200) return [];
    final data = jsonDecode(res.body) as Map<String, dynamic>;
    final list = data['data'] as List<dynamic>? ?? [];
    return list.cast<Map<String, dynamic>>();
  }

  void remove(int productId) {
    _ids.remove(productId);
    notifyListeners();
    ApiClient().delete('/api/favourites/$productId').catchError((_) => http.Response('', 500));
  }
}
