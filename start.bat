@echo off
title CodeMaster - Laravel Dev Server
color 0A

echo ============================================
echo    CodeMaster - IT Education Platform
echo ============================================
echo.

set PHP=C:\OSPanel\modules\PHP-8.3\PHP\php.exe
set PROJECT=C:\OSPanel\home\Codemaster

cd /d %PROJECT%

echo [1/3] Checking database connection...
%PHP% -r "try { new PDO('mysql:host=127.127.126.50;port=3306;dbname=codemaster', 'root', ''); echo '  DB OK\n'; } catch (PDOException $e) { echo '  DB ERROR: ' . $e->getMessage() . '\n'; exit(1); }"
if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERROR: Cannot connect to MySQL. Make sure MySQL-8.2 is running.
    pause
    exit /b 1
)

echo [2/3] Starting services...
echo.

start "CodeMaster Vite" cmd /c "cd /d %PROJECT% && npx vite --host"
start "CodeMaster Queue" cmd /c "cd /d %PROJECT% && %PHP% artisan queue:work --verbose"

echo [3/3] Starting Laravel server on http://localhost:8000
echo.

%PHP% artisan serve --host=0.0.0.0 --port=8000

pause
