# Flutter app (MSZ)

This folder holds the Flutter project with **Login** and **Register** screens that use the Laravel backend API.

## Run without adding Flutter to PATH

From this folder:

```powershell
cd C:\Users\impos\Desktop\Projects\MSZ\flutter
.\run-flutter.ps1 pub get
.\run-flutter.ps1 run -d chrome
```

Or for Windows desktop:

```powershell
.\run-flutter.ps1 run -d windows
```

## Backend must be running

1. Start Laravel: `php artisan serve` (and `npm run dev` for the Blade site).
2. The Flutter app calls **http://localhost:8000** by default (see `lib/config/api_config.dart`).
   - For **Android emulator** use `http://10.0.2.2:8000` (run with `--dart-define=API_BASE_URL=http://10.0.2.2:8000` or change the default in `api_config.dart`).

## CORS (Flutter web only)

When running in Chrome, the Laravel API must allow the Flutter origin. In your Laravel `.env` add or append:

```env
CORS_ALLOWED_ORIGINS=http://localhost:8000,http://127.0.0.1:PORT
```

Replace `PORT` with the port Flutter web shows in the browser (e.g. `http://127.0.0.1:52345`). Or run Flutter on a fixed port:

```powershell
.\run-flutter.ps1 run -d chrome --web-port=3000
```

Then set `CORS_ALLOWED_ORIGINS=...,http://localhost:3000` in Laravel `.env`.

## Login / Register (Flutter)

- **Login:** phone number + password → `POST /api/auth/login`
- **Register:** name, phone, national ID, city, lat/lng, optional email/password → `POST /api/auth/register`  
  Cities are loaded from `GET /api/cities`.

After login or register, the app stores the token and user and navigates to the home screen. Sign out clears storage and returns to login.
