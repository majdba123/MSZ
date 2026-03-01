import 'package:flutter/material.dart';

/// Same keys as website (lang/en.json, lang/ar.json). Use tr(key) with current locale.
class AppStrings {
  AppStrings._();

  static Locale _locale = const Locale('en');
  static set locale(Locale l) => _locale = l;
  static bool get isAr => _locale.languageCode == 'ar';

  static String tr(String key, [String fallback = '']) {
    final map = _locale.languageCode == 'ar' ? _ar : _en;
    return map[key] ?? _en[key] ?? fallback;
  }

  static const Map<String, String> _en = {
    'SyriaZone': 'SyriaZone',
    'nav.categories': 'Categories',
    'nav.products': 'Products',
    'nav.stores': 'Stores',
    'nav.sign_in': 'Sign In',
    'nav.register': 'Register',
    'nav.cart': 'Cart',
    'nav.sign_out': 'Sign Out',
    'footer.all_products': 'All Products',
    'footer.all_stores': 'All Stores',
    'footer.contact_us': 'Contact Us',
    'footer.rights': 'All rights reserved.',
    'auth.sign_in': 'Sign In',
    'auth.register': 'Register',
    'auth.sign_in_title': 'Sign In',
    'auth.create_account_title': 'Create Account',
    'auth.phone_number': 'Phone Number',
    'auth.password': 'Password',
    'auth.confirm_password': 'Confirm Password',
    'auth.email': 'Email Address',
    'auth.full_name': 'Full Name',
    'auth.national_id': 'National ID',
    'auth.no_account': "Don't have an account?",
    'auth.has_account': 'Already have an account?',
    'auth.create_one': 'Create one',
    'auth.sign_in_to_account': 'Sign in to your account',
    'auth.city': 'City',
    'auth.select_city': 'Select your city',
    'common.loading': 'Loading...',
    'common.save': 'Save',
    'common.cancel': 'Cancel',
    'home.welcome': 'Welcome',
    'home.tagline': 'Your trusted online marketplace.',
    'home.hero_title': 'Discover. Shop. Enjoy.',
    'home.contact': 'Contact Us',
    'home.contact_subtitle': "Send us a message and we'll get back to you as soon as we can.",
    'home.send_message': 'Send Message',
    'lang.arabic': 'العربية',
    'lang.english': 'English',
    'lang.choose_language': 'Choose language',
    'admin.toggle_theme': 'Toggle theme',
  };

  static const Map<String, String> _ar = {
    'SyriaZone': 'سوريا زون',
    'nav.categories': 'التصنيفات',
    'nav.products': 'المنتجات',
    'nav.stores': 'المتاجر',
    'nav.sign_in': 'تسجيل الدخول',
    'nav.register': 'إنشاء حساب',
    'nav.cart': 'السلة',
    'nav.sign_out': 'تسجيل الخروج',
    'footer.all_products': 'جميع المنتجات',
    'footer.all_stores': 'جميع المتاجر',
    'footer.contact_us': 'تواصل معنا',
    'footer.rights': 'جميع الحقوق محفوظة.',
    'auth.sign_in': 'تسجيل الدخول',
    'auth.register': 'إنشاء حساب',
    'auth.sign_in_title': 'تسجيل الدخول',
    'auth.create_account_title': 'إنشاء حساب',
    'auth.phone_number': 'رقم الهاتف',
    'auth.password': 'كلمة المرور',
    'auth.confirm_password': 'تأكيد كلمة المرور',
    'auth.email': 'البريد الإلكتروني',
    'auth.full_name': 'الاسم الكامل',
    'auth.national_id': 'الهوية الوطنية',
    'auth.no_account': 'ليس لديك حساب؟',
    'auth.has_account': 'لديك حساب بالفعل؟',
    'auth.create_one': 'أنشئ واحداً',
    'auth.sign_in_to_account': 'سجّل الدخول إلى حسابك',
    'auth.city': 'المدينة',
    'auth.select_city': 'اختر مدينتك',
    'common.loading': 'جاري التحميل...',
    'common.save': 'حفظ',
    'common.cancel': 'إلغاء',
    'home.welcome': 'مرحباً بك',
    'home.tagline': 'منصتك الموثوقة للتسوق الإلكتروني.',
    'home.hero_title': 'اكتشف. تسوق. استمتع.',
    'home.contact': 'تواصل معنا',
    'home.contact_subtitle': 'أرسل لنا رسالة وسنرد عليك في أقرب وقت.',
    'home.send_message': 'إرسال الرسالة',
    'lang.arabic': 'العربية',
    'lang.english': 'English',
    'lang.choose_language': 'اختر اللغة',
    'admin.toggle_theme': 'تبديل المظهر',
  };
}
