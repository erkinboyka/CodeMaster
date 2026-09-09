@echo off
title CodeMaster - Laravel Dev Server
color 0A

echo ============================================
echo    CodeMaster - IT Education Platform
echo ============================================
echo.

set "PHP=C:\OSPanel\modules\PHP-8.3\PHP\php.exe"
set "PROJECT=C:\OSPanel\home\Codemaster"

cd /d "%PROJECT%"

echo [1/4] Checking database connection...

"%PHP%" -r "try { new PDO('mysql:host=127.127.126.50;port=3306;dbname=codemaster', 'root', ''); echo '  DB OK'; } catch (PDOException $e) { echo '  DB ERROR: ' . $e->getMessage(); exit(1); }"

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERROR: Cannot connect to MySQL.
    pause
    exit /b 1
)

echo.
echo [2/4] Running database migrations (fresh - all tables dropped)...
"%PHP%" artisan migrate:fresh --force

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERROR: Migration failed.
    pause
    exit /b 1
)

echo.
echo [3/4] Seeding database (all seeders via DatabaseSeeder)...
"%PHP%" artisan db:seed --force

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERROR: Database seeding failed.
    pause
    exit /b 1
)

echo.
echo [4/4] Starting development servers...
echo.

start "CodeMaster Vite" cmd /k "cd /d "%PROJECT%" && npx vite --host"

start "CodeMaster Queue" cmd /k "cd /d "%PROJECT%" && "%PHP%" artisan queue:work --stop-when-empty"

echo ============================================
echo Laravel: http://localhost:8000
echo Vite:    http://localhost:5173
echo ============================================
echo.

"%PHP%" artisan serve --host=0.0.0.0 --port=8000

pause