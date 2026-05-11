@echo off
echo ========================================
echo   Face Verification API - Sistem Absensi
echo ========================================
echo.

cd /d "%~dp0"

:: Cek apakah virtual environment sudah ada
if not exist "venv\Scripts\activate.bat" (
    echo [INFO] Membuat virtual environment Python...
    python -m venv venv
    if errorlevel 1 (
        echo [ERROR] Gagal membuat venv. Pastikan Python sudah terinstall.
        pause
        exit /b 1
    )
)

:: Aktifkan virtual environment
call venv\Scripts\activate.bat

:: Install dependencies jika belum
echo [INFO] Mengecek dan install dependencies...
pip install -r requirements.txt --quiet

echo.
echo [OK] Server dimulai di http://127.0.0.1:5001
echo [OK] Tekan CTRL+C untuk menghentikan server
echo.

python app.py

pause
