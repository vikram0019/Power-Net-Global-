@echo off
setlocal
set "PROJECT_DIR=%~dp0"
set "PHP_EXE=C:\xampp\php\php.exe"

echo ==============================================
echo   PowerNetGlobal - Starting Dev Environment
echo ==============================================
echo.

echo Checking MySQL...
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I "mysqld.exe" >NUL
if errorlevel 1 (
    echo Starting MySQL...
    start "MySQL" /min "C:\xampp\mysql_start.bat"
    timeout /t 4 /nobreak >NUL
) else (
    echo MySQL is already running.
)

echo.
echo Starting Laravel dev server...
cd /d "%PROJECT_DIR%"
start "PowerNetGlobal Server" "%PHP_EXE%" artisan serve

timeout /t 2 /nobreak >NUL
start http://127.0.0.1:8000/

echo.
echo PowerNetGlobal is running at http://127.0.0.1:8000/
echo A separate "PowerNetGlobal Server" window is now running the app.
echo Close that window (or press Ctrl+C in it) to stop the server.
echo.
pause
