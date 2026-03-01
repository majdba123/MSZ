/// Backend API base URL (production: http://62.84.188.239).
/// Override with --dart-define=API_BASE_URL=... for local or emulator.
const String apiBaseUrl = String.fromEnvironment(
  'API_BASE_URL',
  defaultValue: 'http://62.84.188.239',
);
