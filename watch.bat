@echo off
echo ========================================
echo Block Editor Gallery Slider - Watch Mode
echo ========================================
echo.
echo Starting file watchers...
echo This will open separate windows for each watcher.
echo Close those windows to stop watching.
echo.

REM Check if sass is available
where sass >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: sass command not found. Please install Dart Sass:
    echo   npm install -g sass
    pause
    exit /b 1
)

REM Check if nodemon is available
where nodemon >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo WARNING: nodemon not found. Installing for JS watching...
    echo   npm install -g nodemon
    call npm install -g nodemon
    if %ERRORLEVEL% NEQ 0 (
        echo ERROR: Failed to install nodemon
        pause
        exit /b 1
    )
)

REM Check if terser is available
where terser >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: terser command not found. Please install terser:
    echo   npm install -g terser
    pause
    exit /b 1
)

echo.
echo [1/5] Starting SASS watcher for begs-admin.scss...
start "SASS: Admin" cmd /k "sass --watch assets/dev/scss/begs-admin.scss:assets/dist/css/begs-admin.min.css --style=compressed"

timeout /t 1 /nobreak >nul

echo [2/5] Starting SASS watcher for begs-frontend.scss...
start "SASS: Front-end" cmd /k "sass --watch assets/dev/scss/begs-frontend.scss:assets/dist/css/begs-frontend.min.css --style=compressed"

timeout /t 1 /nobreak >nul

echo [3/5] Starting SASS watcher for begs-block-editor.scss...
start "SASS: Block Editor" cmd /k "sass --watch assets/dev/scss/begs-block-editor.scss:assets/dist/css/begs-block-editor.min.css --style=compressed"

timeout /t 1 /nobreak >nul

echo [4/5] Starting SASS watcher for begs-classic-editor.scss...
start "SASS: Classic Editor" cmd /k "sass --watch assets/dev/scss/begs-classic-editor.scss:assets/dist/css/begs-classic-editor.min.css --style=compressed"

timeout /t 1 /nobreak >nul

echo [5/5] Starting JS watcher for all scripts...
start "Terser: JS Watch" cmd /k nodemon --watch assets/dev/scripts --ext js --exec "terser assets/dev/scripts/begs-admin.js --compress --mangle --output assets/dist/js/begs-admin.min.js && terser assets/dev/scripts/begs-frontend.js --compress --mangle --output assets/dist/js/begs-frontend.min.js && terser assets/dev/scripts/begs-block-editor.js --compress --mangle --output assets/dist/js/begs-block-editor.min.js && echo [%time%] JS files rebuilt!"

echo.
echo ========================================
echo Watch Mode Active!
echo ========================================
echo.
echo Five watch windows have been opened:
echo   1. SASS: Admin
echo   2. SASS: Front-end
echo   3. SASS: Block Editor
echo   4. SASS: Classic Editor
echo   5. Terser: JS Watch
echo.
echo Your files will automatically rebuild when changed.
echo Close the watch windows to stop watching.
echo.
pause
