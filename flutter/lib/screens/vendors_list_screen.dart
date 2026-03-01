import 'package:flutter/material.dart';

import '../models/vendor_model.dart';
import '../services/client_api_service.dart';
import '../widgets/vendor_card.dart';

class VendorsListScreen extends StatefulWidget {
  const VendorsListScreen({super.key});

  @override
  State<VendorsListScreen> createState() => _VendorsListScreenState();
}

class _VendorsListScreenState extends State<VendorsListScreen> {
  final ClientApiService _api = ClientApiService();
  List<VendorModel> _vendors = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    final list = await _api.getVendors();
    if (!mounted) return;
    setState(() {
      _vendors = list;
      _loading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Stores')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _load,
              child: _vendors.isEmpty
                  ? const Center(child: Text('No stores yet.'))
                  : ListView.builder(
                      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                      itemCount: _vendors.length,
                      itemBuilder: (context, i) {
                        final vendor = _vendors[i];
                        return Padding(
                          key: ValueKey('vl_${vendor.id}'),
                          padding: const EdgeInsets.only(bottom: 12),
                          child: VendorCard(
                            vendor: vendor,
                            onTap: () => Navigator.of(context).pushNamed('/vendor/${vendor.id}'),
                          ),
                        );
                      },
                    ),
            ),
    );
  }
}
