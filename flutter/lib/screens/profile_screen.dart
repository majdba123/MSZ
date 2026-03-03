import 'package:flutter/material.dart';

import '../l10n/app_strings.dart';
import '../services/auth_service.dart';
import '../services/favourite_service.dart';
import '../theme/app_theme.dart';
import '../utils/app_urls.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key, required this.authService, this.favouriteService});

  final AuthService authService;
  final FavouriteService? favouriteService;

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final user = widget.authService.user;

    if (user == null) {
      return Scaffold(
        appBar: AppBar(title: Text(AppStrings.tr('profile.title'))),
        body: Center(
          child: Padding(
            padding: const EdgeInsets.all(32),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(
                  width: 80,
                  height: 80,
                  decoration: BoxDecoration(
                    color: colorScheme.surfaceContainerHighest.withValues(alpha: 0.5),
                    shape: BoxShape.circle,
                  ),
                  child: Icon(Icons.person_off_outlined, size: 40, color: colorScheme.outline.withValues(alpha: 0.5)),
                ),
                const SizedBox(height: 20),
                Text(
                  AppStrings.tr('profile.not_logged_in'),
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.w700, color: colorScheme.onSurface),
                ),
                const SizedBox(height: 6),
                Text(
                  AppStrings.tr('profile.sign_in_prompt'),
                  style: TextStyle(fontSize: 14, color: colorScheme.outline),
                ),
                const SizedBox(height: 28),
                SizedBox(
                  height: 48,
                  child: FilledButton.icon(
                    onPressed: () => Navigator.of(context).pushReplacementNamed('/login'),
                    icon: const Icon(Icons.login_rounded, size: 20),
                    label: Text(AppStrings.tr('nav.sign_in')),
                  ),
                ),
              ],
            ),
          ),
        ),
      );
    }

    String accountType;
    IconData accountIcon;
    switch (user.type) {
      case 1:
        accountType = AppStrings.tr('profile.type_admin');
        accountIcon = Icons.admin_panel_settings_outlined;
      case 2:
        accountType = AppStrings.tr('profile.type_vendor');
        accountIcon = Icons.storefront_outlined;
      default:
        accountType = AppStrings.tr('profile.type_customer');
        accountIcon = Icons.person_outline;
    }

    return Scaffold(
      appBar: AppBar(title: Text(AppStrings.tr('profile.title'))),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [
                    AppTheme.brandPrimary.withValues(alpha: 0.08),
                    colorScheme.surfaceContainerHighest.withValues(alpha: 0.3),
                  ],
                ),
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: colorScheme.outlineVariant.withValues(alpha: 0.3)),
              ),
              child: Column(
                children: [
                  _buildAvatar(colorScheme, user.name, user.avatarUrl),
                  const SizedBox(height: 14),
                  Text(
                    user.name,
                    style: TextStyle(fontSize: 22, fontWeight: FontWeight.w800, color: colorScheme.onSurface, letterSpacing: -0.3),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    user.phoneNumber,
                    style: TextStyle(fontSize: 14, color: colorScheme.outline),
                  ),
                  const SizedBox(height: 10),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                    decoration: BoxDecoration(
                      color: AppTheme.brandPrimary.withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(999),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(accountIcon, size: 14, color: AppTheme.brandPrimary),
                        const SizedBox(width: 6),
                        Text(
                          accountType,
                          style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppTheme.brandPrimary),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),

            _buildSection(
              colorScheme,
              AppStrings.tr('profile.section_account'),
              Icons.person_outline_rounded,
              [
                _ProfileRow(icon: Icons.badge_outlined, label: AppStrings.tr('profile.name'), value: user.name, colorScheme: colorScheme),
                _ProfileRow(icon: Icons.phone_outlined, label: AppStrings.tr('profile.phone_number'), value: user.phoneNumber, colorScheme: colorScheme),
                if (user.nationalId != null)
                  _ProfileRow(icon: Icons.credit_card_outlined, label: AppStrings.tr('profile.national_id'), value: user.nationalId!, colorScheme: colorScheme),
                if (user.city != null)
                  _ProfileRow(icon: Icons.location_on_outlined, label: AppStrings.tr('profile.city'), value: user.city!['name']?.toString() ?? '-', colorScheme: colorScheme),
              ],
            ),
            const SizedBox(height: 12),

            _buildSection(
              colorScheme,
              AppStrings.tr('profile.section_contact'),
              Icons.mail_outline_rounded,
              [
                _ProfileRow(icon: Icons.email_outlined, label: AppStrings.tr('profile.email'), value: user.email ?? '-', colorScheme: colorScheme),
              ],
            ),
            const SizedBox(height: 16),

            _buildMenuTile(
              colorScheme,
              Icons.favorite_outline_rounded,
              AppStrings.tr('profile.favourites'),
              const Color(0xFFEF4444),
              () => Navigator.of(context).pushNamed('/products'),
            ),
            const SizedBox(height: 8),
            _buildMenuTile(
              colorScheme,
              Icons.receipt_long_outlined,
              AppStrings.tr('profile.orders'),
              const Color(0xFF3B82F6),
              () => Navigator.of(context).pushNamed('/orders'),
            ),
            const SizedBox(height: 24),

            SizedBox(
              width: double.infinity,
              height: 48,
              child: OutlinedButton.icon(
                onPressed: () async {
                  await widget.authService.logout();
                  if (mounted) Navigator.of(context).pushReplacementNamed('/login');
                },
                icon: Icon(Icons.logout_rounded, color: colorScheme.error, size: 20),
                label: Text(AppStrings.tr('profile.logout'), style: TextStyle(color: colorScheme.error, fontWeight: FontWeight.w600)),
                style: OutlinedButton.styleFrom(
                  side: BorderSide(color: colorScheme.error.withValues(alpha: 0.3)),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                ),
              ),
            ),
            const SizedBox(height: 32),
          ],
        ),
      ),
    );
  }

  Widget _buildAvatar(ColorScheme colorScheme, String name, String? avatarUrl) {
    if (avatarUrl != null && avatarUrl.isNotEmpty) {
      return Container(
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          border: Border.all(color: AppTheme.brandPrimary.withValues(alpha: 0.3), width: 3),
        ),
        child: ClipOval(
          child: SizedBox(
            width: 88,
            height: 88,
            child: AppNetworkImage(url: avatarUrl, fit: BoxFit.cover),
          ),
        ),
      );
    }
    return Container(
      width: 88,
      height: 88,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        gradient: LinearGradient(
          colors: [AppTheme.brandPrimary.withValues(alpha: 0.15), AppTheme.brandPrimary.withValues(alpha: 0.05)],
        ),
        border: Border.all(color: AppTheme.brandPrimary.withValues(alpha: 0.2), width: 3),
      ),
      child: Center(
        child: Text(
          name.isNotEmpty ? name[0].toUpperCase() : '?',
          style: const TextStyle(fontSize: 36, fontWeight: FontWeight.w800, color: AppTheme.brandPrimary),
        ),
      ),
    );
  }

  Widget _buildSection(ColorScheme colorScheme, String title, IconData icon, List<Widget> children) {
    return Container(
      decoration: BoxDecoration(
        color: colorScheme.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: colorScheme.outlineVariant.withValues(alpha: 0.5)),
      ),
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 32,
                height: 32,
                decoration: BoxDecoration(
                  color: AppTheme.brandPrimary.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Icon(icon, size: 16, color: AppTheme.brandPrimary),
              ),
              const SizedBox(width: 10),
              Text(title, style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700, color: colorScheme.onSurface)),
            ],
          ),
          const SizedBox(height: 14),
          ...children,
        ],
      ),
    );
  }

  Widget _buildMenuTile(ColorScheme colorScheme, IconData icon, String label, Color accentColor, VoidCallback onTap) {
    return Container(
      decoration: BoxDecoration(
        color: colorScheme.surface,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: colorScheme.outlineVariant.withValues(alpha: 0.5)),
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(14),
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Row(
              children: [
                Container(
                  width: 40,
                  height: 40,
                  decoration: BoxDecoration(
                    color: accentColor.withValues(alpha: 0.08),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Icon(icon, size: 20, color: accentColor),
                ),
                const SizedBox(width: 14),
                Expanded(child: Text(label, style: TextStyle(fontSize: 15, fontWeight: FontWeight.w600, color: colorScheme.onSurface))),
                Icon(Icons.chevron_right_rounded, size: 22, color: colorScheme.outline),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _ProfileRow extends StatelessWidget {
  const _ProfileRow({required this.icon, required this.label, required this.value, required this.colorScheme});

  final IconData icon;
  final String label;
  final String value;
  final ColorScheme colorScheme;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Row(
        children: [
          Icon(icon, size: 16, color: colorScheme.outline),
          const SizedBox(width: 8),
          Expanded(
            child: Text(label, style: TextStyle(fontSize: 13, color: colorScheme.outline)),
          ),
          Flexible(
            child: Text(
              value,
              style: TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: colorScheme.onSurface),
              textAlign: TextAlign.end,
              overflow: TextOverflow.ellipsis,
            ),
          ),
        ],
      ),
    );
  }
}
