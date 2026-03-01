# Run Flutter commands without adding Flutter to PATH.
# Usage: .\run-flutter.ps1 run -d chrome   or   .\run-flutter.ps1 pub get
$flutterBin = "C:\Users\impos\Downloads\flutter_windows_3.41.2-stable\flutter\bin\flutter.bat"
if (-not (Test-Path $flutterBin)) {
    Write-Error "Flutter not found at: $flutterBin"
    exit 1
}
& $flutterBin @args
