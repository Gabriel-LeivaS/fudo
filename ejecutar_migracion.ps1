# ============================================================
# Script PowerShell: Ejecutar Migración Multi-Sucursal
# ============================================================

Write-Host "============================================================" -ForegroundColor Cyan
Write-Host "   MIGRACIÓN: Sistema Multi-Sucursal con Roles - FUDO" -ForegroundColor Cyan
Write-Host "============================================================" -ForegroundColor Cyan
Write-Host ""

# Configuración de PostgreSQL
$PGHOST = "localhost"
$PGPORT = "5432"
$PGUSER = "postgres"
$PGDATABASE = "fudo"

Write-Host "Configuración:" -ForegroundColor Yellow
Write-Host "  Host: $PGHOST" -ForegroundColor White
Write-Host "  Puerto: $PGPORT" -ForegroundColor White
Write-Host "  Usuario: $PGUSER" -ForegroundColor White
Write-Host "  Base de datos: $PGDATABASE" -ForegroundColor White
Write-Host ""

# Solicitar contraseña
$PGPASSWORD = Read-Host "Ingrese la contraseña de PostgreSQL para usuario '$PGUSER'" -AsSecureString
$PGPASSWORD_Plain = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($PGPASSWORD))

Write-Host ""
Write-Host "Ejecutando migración..." -ForegroundColor Yellow
Write-Host ""

# Ejecutar migración
$env:PGPASSWORD = $PGPASSWORD_Plain

try {
    & psql -h $PGHOST -p $PGPORT -U $PGUSER -d $PGDATABASE -f "migration_multisucursal.sql"
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host ""
        Write-Host "============================================================" -ForegroundColor Green
        Write-Host "   ✅ MIGRACIÓN COMPLETADA EXITOSAMENTE" -ForegroundColor Green
        Write-Host "============================================================" -ForegroundColor Green
        Write-Host ""
        Write-Host "CREDENCIALES DE PRUEBA:" -ForegroundColor Cyan
        Write-Host ""
        Write-Host "Super Admin:" -ForegroundColor Yellow
        Write-Host "  Usuario: admin" -ForegroundColor White
        Write-Host "  Contraseña: admin123" -ForegroundColor White
        Write-Host ""
        Write-Host "Admin Sucursal Centro:" -ForegroundColor Yellow
        Write-Host "  Usuario: admin_centro" -ForegroundColor White
        Write-Host "  Contraseña: centro123" -ForegroundColor White
        Write-Host ""
        Write-Host "Admin Sucursal Plaza Norte:" -ForegroundColor Yellow
        Write-Host "  Usuario: admin_norte" -ForegroundColor White
        Write-Host "  Contraseña: norte123" -ForegroundColor White
        Write-Host ""
        Write-Host "Admin Sucursal Mall Sur:" -ForegroundColor Yellow
        Write-Host "  Usuario: admin_sur" -ForegroundColor White
        Write-Host "  Contraseña: sur123" -ForegroundColor White
        Write-Host ""
        Write-Host "============================================================" -ForegroundColor Green
        Write-Host ""
        Write-Host "Próximos pasos:" -ForegroundColor Cyan
        Write-Host "1. Inicia sesión en: http://localhost/fudo/index.php/login" -ForegroundColor White
        Write-Host "2. Prueba con el usuario 'admin' / 'admin123'" -ForegroundColor White
        Write-Host "3. Las vistas frontend están pendientes de crear" -ForegroundColor White
    } else {
        Write-Host ""
        Write-Host "❌ Error al ejecutar la migración" -ForegroundColor Red
        Write-Host "Código de salida: $LASTEXITCODE" -ForegroundColor Red
    }
} catch {
    Write-Host ""
    Write-Host "❌ Error: $_" -ForegroundColor Red
} finally {
    # Limpiar variable de entorno
    Remove-Item Env:\PGPASSWORD -ErrorAction SilentlyContinue
}

Write-Host ""
Write-Host "Presiona cualquier tecla para salir..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
