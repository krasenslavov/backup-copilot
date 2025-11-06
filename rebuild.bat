@echo off
echo ========================================
echo Block Editor Gallery Slider - Asset Rebuild
echo ========================================
echo.

REM Check if sass is available
where sass >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: sass command not found. Please install Dart Sass:
    echo   npm install -g sass
    echo   or download from: https://sass-lang.com/install
    pause
    exit /b 1
)

REM Check if terser is available
where terser >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: terser command not found. Please install terser:
    echo   npm install -g terser
    pause
    exit /b 1
)

echo [1/9] Compiling begs-admin.scss...
call sass assets/dev/scss/begs-admin.scss assets/dist/css/begs-admin.min.css --style=compressed
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to compile begs-admin.scss
    pause
    exit /b 1
)
echo SUCCESS: begs-admin.min.css created

echo.
echo [2/9] Compiling begs-frontend.scss...
call sass assets/dev/scss/begs-frontend.scss assets/dist/css/begs-frontend.min.css --style=compressed
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to compile begs-frontend.scss
    pause
    exit /b 1
)
echo SUCCESS: begs-frontend.min.css created

echo.
echo [3/9] Compiling begs-block-editor.scss...
call sass assets/dev/scss/begs-block-editor.scss assets/dist/css/begs-block-editor.min.css --style=compressed
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to compile begs-block-editor.scss
    pause
    exit /b 1
)
echo SUCCESS: begs-block-editor.min.css created

echo.
echo [4/9] Compiling begs-classic-editor.scss...
call sass assets/dev/scss/begs-classic-editor.scss assets/dist/css/begs-classic-editor.min.css --style=compressed
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to compile begs-classic-editor.scss
    pause
    exit /b 1
)
echo SUCCESS: begs-classic-editor.min.css created

echo.
echo [5/9] Compiling begs-onboarding-notice.scss...
call sass assets/dev/scss/begs-onboarding-notice.scss assets/dist/css/begs-onboarding-notice.min.css --style=compressed
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to compile begs-onboarding-notice.scss
    pause
    exit /b 1
)
echo SUCCESS: begs-onboarding-notice.min.css created

echo.
echo [6/9] Minifying begs-admin.js...
call terser assets/dev/scripts/begs-admin.js --compress --mangle --output assets/dist/js/begs-admin.min.js
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to minify begs-admin.js
    pause
    exit /b 1
)
echo SUCCESS: begs-admin.min.js created

echo.
echo [7/9] Minifying begs-frontend.js...
call terser assets/dev/scripts/begs-frontend.js --compress --mangle --output assets/dist/js/begs-frontend.min.js
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to minify begs-frontend.js
    pause
    exit /b 1
)
echo SUCCESS: begs-frontend.min.js created

echo.
echo [8/9] Minifying begs-block-editor.js...
call terser assets/dev/scripts/begs-block-editor.js --compress --mangle --output assets/dist/js/begs-block-editor.min.js
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to minify begs-block-editor.js
    pause
    exit /b 1
)
echo SUCCESS: begs-block-editor.min.js created

echo.
echo [9/9] Minifying begs-onboarding-notice.js...
call terser assets/dev/scripts/begs-onboarding-notice.js --compress --mangle --output assets/dist/js/begs-onboarding-notice.min.js
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to minify begs-onboarding-notice.js
    pause
    exit /b 1
)
echo SUCCESS: begs-onboarding-notice.min.js created

echo.
echo ========================================
echo Build completed successfully!
echo ========================================
echo.
echo CSS files rebuilt:
echo   - begs-admin.min.css (Admin)
echo   - begs-frontend.min.css (Front-end)
echo   - begs-block-editor.min.css (Block Editor)
echo   - begs-classic-editor.min.css (TinyMCE)
echo   - begs-onboarding-notice.min.css (Onboarding)
echo.
echo JS files rebuilt:
echo   - begs-admin.min.js (Admin)
echo   - begs-frontend.min.js (Front-end)
echo   - begs-block-editor.min.js (Block Editor)
echo   - begs-onboarding-notice.min.js (Onboarding)
echo.
echo All assets have been minified and are ready for production.
echo.
pause
