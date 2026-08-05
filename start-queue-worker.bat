@echo off
echo Starting Typography Studio Queue Worker...
echo This window must stay open while generating videos.
echo Press Ctrl+C to stop.
echo.
cd /d C:\xampp\htdocs\typographic
php artisan queue:work --sleep=3 --tries=2 --timeout=300
pause
