@echo off
title Memon Nimko - WhatsApp Service
color 0A

echo ====================================================
echo        MEMON NIMKO - WHATSAPP AUTOMATION SERVICE
echo ====================================================
echo.

cd /d "%~dp0whatsapp-service"

if not exist "node_modules\" (
    echo [INFO] First-time setup detected. Installing dependencies...
    call npm install
    if errorlevel 1 (
        echo [ERROR] npm install failed. Please ensure Node.js is installed.
        pause
        exit /b
    )
)

echo [INFO] Starting WhatsApp Service on Port 3000...
echo [INFO] Opening Browser for QR Code...
start http://localhost:3000

call npm start
pause
