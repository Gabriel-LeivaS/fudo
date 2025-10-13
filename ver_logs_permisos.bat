@echo off
echo ========================================
echo   MONITOR DE LOGS - EDICION USUARIOS
echo ========================================
echo.
echo Monitoreando: C:\xampp\apache\logs\error.log
echo Presiona Ctrl+C para detener
echo.
echo ========================================
echo.

powershell -Command "Get-Content C:\xampp\apache\logs\error.log -Tail 0 -Wait | Select-String -Pattern 'EDITAR USUARIO|DEBUG|permisos'"
