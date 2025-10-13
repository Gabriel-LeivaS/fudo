@echo off
echo ========================================
echo   MONITOR DE DEBUG_LOG.TXT
echo ========================================
echo.
echo Archivo: C:\xampp\htdocs\fudo\debug_log.txt
echo Presiona Ctrl+C para detener
echo.
echo ========================================
echo.

powershell -Command "Get-Content C:\xampp\htdocs\fudo\debug_log.txt -Tail 0 -Wait"
