import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;

import '../config/api_config.dart';
import '../l10n/app_strings.dart';
import '../models/user_model.dart';
import '../services/auth_service.dart';
import '../utils/json_parsers.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key, required this.authService});

  final AuthService authService;

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _phoneController = TextEditingController();
  final _nationalIdController = TextEditingController();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  final _passwordConfirmController = TextEditingController();

  bool _loading = false;
  String? _error;
  List<Map<String, dynamic>> _cities = [];
  int? _selectedCityId;
  final _latController = TextEditingController(text: '35.0');
  final _lngController = TextEditingController(text: '38.5');

  @override
  void initState() {
    super.initState();
    _loadCities();
  }

  Future<void> _loadCities() async {
    try {
      final res = await http.get(
        Uri.parse('$apiBaseUrl/api/cities'),
        headers: {'Accept': 'application/json'},
      );
      if (res.statusCode == 200) {
        final data = jsonDecode(res.body) as Map<String, dynamic>;
        final list = data['data'] as List<dynamic>? ?? [];
        setState(() {
          _cities = list
              .map((e) { final m = e as Map; return {'id': toInt(m['id']), 'name': toStringVal(m['name'])}; })
              .toList();
        });
      }
    } catch (_) {}
  }

  @override
  void dispose() {
    _nameController.dispose();
    _phoneController.dispose();
    _nationalIdController.dispose();
    _emailController.dispose();
    _passwordController.dispose();
    _passwordConfirmController.dispose();
    _latController.dispose();
    _lngController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    setState(() { _error = null; _loading = true; });
    if (!(_formKey.currentState?.validate() ?? false)) {
      setState(() => _loading = false);
      return;
    }
    if (_selectedCityId == null) {
      setState(() { _error = AppStrings.tr('auth.select_city'); _loading = false; });
      return;
    }
    final lat = double.tryParse(_latController.text) ?? 35.0;
    final lng = double.tryParse(_lngController.text) ?? 38.5;
    final payload = <String, dynamic>{
      'name': _nameController.text.trim(),
      'phone_number': _phoneController.text.trim(),
      'national_id': _nationalIdController.text.trim(),
      'city_id': _selectedCityId!,
      'latitude': lat,
      'longitude': lng,
    };
    final email = _emailController.text.trim();
    if (email.isNotEmpty) payload['email'] = email;
    final password = _passwordController.text;
    if (password.isNotEmpty) {
      payload['password'] = password;
      payload['password_confirmation'] = _passwordConfirmController.text;
    }
    try {
      final result = await widget.authService.register(payload);
      await widget.authService.setToken(result['token'] as String);
      await widget.authService.setUser(UserModel.fromJson(result['user'] as Map<String, dynamic>));
      if (!mounted) return;
      Navigator.of(context).pushReplacementNamed('/home');
    } catch (e) {
      if (!mounted) return;
      setState(() { _error = e.toString().replaceFirst('Exception: ', ''); _loading = false; });
    }
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    return Scaffold(
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(24),
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 420),
              child: Form(
                key: _formKey,
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    Text(
                      AppStrings.tr('auth.create_account_title'),
                      style: Theme.of(context).textTheme.headlineMedium?.copyWith(fontWeight: FontWeight.bold),
                      textAlign: TextAlign.center,
                    ),
                    const SizedBox(height: 24),
                    if (_error != null) ...[
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(color: colorScheme.errorContainer, borderRadius: BorderRadius.circular(8)),
                        child: Text(_error!, style: TextStyle(color: colorScheme.onErrorContainer)),
                      ),
                      const SizedBox(height: 16),
                    ],
                    TextFormField(
                      controller: _nameController,
                      decoration: InputDecoration(labelText: AppStrings.tr('auth.full_name')),
                      textInputAction: TextInputAction.next,
                      validator: (v) => (v == null || v.trim().isEmpty) ? '${AppStrings.tr('auth.full_name')} required' : null,
                    ),
                    const SizedBox(height: 12),
                    TextFormField(
                      controller: _phoneController,
                      decoration: InputDecoration(labelText: AppStrings.tr('auth.phone_number'), hintText: '09XXXXXXXX'),
                      keyboardType: TextInputType.phone,
                      textInputAction: TextInputAction.next,
                      validator: (v) => (v == null || v.trim().isEmpty) ? '${AppStrings.tr('auth.phone_number')} required' : null,
                    ),
                    const SizedBox(height: 12),
                    TextFormField(
                      controller: _nationalIdController,
                      decoration: InputDecoration(labelText: AppStrings.tr('auth.national_id')),
                      textInputAction: TextInputAction.next,
                      validator: (v) => (v == null || v.trim().isEmpty) ? '${AppStrings.tr('auth.national_id')} required' : null,
                    ),
                    const SizedBox(height: 12),
                    DropdownButtonFormField<int>(
                      initialValue: _selectedCityId,
                      decoration: InputDecoration(labelText: AppStrings.tr('auth.city')),
                      hint: Text(AppStrings.tr('auth.select_city')),
                      items: _cities.map((c) => DropdownMenuItem<int>(value: c['id'] as int, child: Text((c['name'] ?? '').toString()))).toList(),
                      onChanged: (v) => setState(() => _selectedCityId = v),
                      validator: (v) => v == null ? AppStrings.tr('auth.select_city') : null,
                    ),
                    const SizedBox(height: 12),
                    TextFormField(
                      controller: _emailController,
                      decoration: InputDecoration(labelText: '${AppStrings.tr('auth.email')} (${AppStrings.tr('common.cancel')})'),
                      keyboardType: TextInputType.emailAddress,
                      textInputAction: TextInputAction.next,
                    ),
                    const SizedBox(height: 12),
                    TextFormField(
                      controller: _passwordController,
                      decoration: InputDecoration(labelText: AppStrings.tr('auth.password')),
                      obscureText: true,
                      textInputAction: TextInputAction.next,
                    ),
                    const SizedBox(height: 12),
                    TextFormField(
                      controller: _passwordConfirmController,
                      decoration: InputDecoration(labelText: AppStrings.tr('auth.confirm_password')),
                      obscureText: true,
                      textInputAction: TextInputAction.done,
                      onFieldSubmitted: (_) => _submit(),
                    ),
                    const SizedBox(height: 24),
                    FilledButton(
                      onPressed: _loading ? null : _submit,
                      style: FilledButton.styleFrom(padding: const EdgeInsets.symmetric(vertical: 14)),
                      child: _loading
                          ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(strokeWidth: 2))
                          : Text(AppStrings.tr('auth.register')),
                    ),
                    const SizedBox(height: 16),
                    TextButton(
                      onPressed: () => Navigator.of(context).pushReplacementNamed('/login'),
                      child: Text('${AppStrings.tr('auth.has_account')} ${AppStrings.tr('auth.sign_in')}'),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
