# 🚀 Migración: Sistema Multi-Sucursal con Roles

## 📋 Descripción

Este script actualiza tu base de datos existente de FUDO para agregar soporte de múltiples sucursales y roles de usuario (Super Admin y Admin Sucursal).

---

## ⚠️ IMPORTANTE: Backup Primero

**Antes de ejecutar la migración, haz backup de tu base de datos:**

```powershell
# Crear backup
pg_dump -U postgres -d fudo -F c -b -v -f "backup_fudo_$(Get-Date -Format 'yyyyMMdd_HHmmss').backup"
```

---

## 🔧 Opción 1: Ejecutar con PowerShell (Recomendado)

### Paso 1: Abrir PowerShell como Administrador

1. Presiona `Win + X`
2. Selecciona "Windows PowerShell (Admin)" o "Terminal (Admin)"

### Paso 2: Navegar a la carpeta del proyecto

```powershell
cd C:\xampp\htdocs\fudo
```

### Paso 3: Permitir ejecución de scripts (si es necesario)

```powershell
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
```

### Paso 4: Ejecutar el script

```powershell
.\ejecutar_migracion.ps1
```

### Paso 5: Ingresar contraseña

El script te pedirá la contraseña de PostgreSQL (usuario `postgres`).

---

## 🔧 Opción 2: Ejecutar Manualmente con psql

### Desde PowerShell o CMD:

```powershell
cd C:\xampp\htdocs\fudo
psql -U postgres -d fudo -f migration_multisucursal.sql
```

Luego ingresa la contraseña cuando te la pida.

---

## 📊 Lo que hace la migración

### 1. **Crea tabla `sucursales`**
   - 3 sucursales de ejemplo: Centro, Plaza Norte, Mall Sur

### 2. **Actualiza tabla `usuarios_admin`**
   - Agrega campos: `email`, `rol`, `id_sucursal`, `fecha_creacion`
   - Actualiza usuario `admin` existente como Super Admin
   - Crea 3 usuarios Admin Sucursal de ejemplo

### 3. **Actualiza tablas existentes**
   - Agrega campo `id_sucursal` a: `categorias`, `productos`, `mesas`, `pedidos`
   - Asigna datos existentes a "Sucursal Centro" por defecto
   - Crea datos de ejemplo para otras sucursales

### 4. **Crea índices**
   - Optimiza consultas por sucursal

---

## 👥 Credenciales de Prueba

### Super Admin (acceso total):
- **Usuario:** `admin`
- **Contraseña:** `admin123`
- **Permisos:** Gestiona todas las sucursales, crea usuarios, crea sucursales

### Admin Sucursal Centro:
- **Usuario:** `admin_centro`
- **Contraseña:** `centro123`
- **Permisos:** Solo Sucursal Centro

### Admin Sucursal Plaza Norte:
- **Usuario:** `admin_norte`
- **Contraseña:** `norte123`
- **Permisos:** Solo Sucursal Plaza Norte

### Admin Sucursal Mall Sur:
- **Usuario:** `admin_sur`
- **Contraseña:** `sur123`
- **Permisos:** Solo Sucursal Mall Sur

---

## ✅ Verificar que funcionó

### 1. Login
Ve a: http://localhost/fudo/index.php/login

### 2. Prueba con Super Admin
- Usuario: `admin`
- Contraseña: `admin123`
- Deberías poder acceder al panel

### 3. Verifica la base de datos
```sql
-- Ver sucursales
SELECT * FROM sucursales;

-- Ver usuarios con sus roles
SELECT id, usuario, nombre_completo, rol, id_sucursal 
FROM usuarios_admin;

-- Ver categorías por sucursal
SELECT c.id_categoria, c.nombre, s.nombre as sucursal
FROM categorias c
JOIN sucursales s ON c.id_sucursal = s.id_sucursal
ORDER BY s.id_sucursal, c.nombre;
```

---

## 🔄 Si algo sale mal

### Restaurar desde backup:
```powershell
pg_restore -U postgres -d fudo -c backup_fudo_YYYYMMDD_HHMMSS.backup
```

### O resetear completamente:
```powershell
# Eliminar base de datos
psql -U postgres -c "DROP DATABASE IF EXISTS fudo;"

# Crear de nuevo con el database.sql original
psql -U postgres -f database.sql
```

---

## 📝 Próximos Pasos

Después de ejecutar la migración, el backend está 100% listo. Faltan las vistas frontend:

1. ✅ **Backend completado** - Modelos y controladores listos
2. ⏳ **Vistas pendientes:**
   - `admin/usuarios.php` - CRUD de usuarios
   - `admin/sucursales.php` - CRUD de sucursales
   - Actualizar `admin/categorias.php` con filtro de sucursal
   - Actualizar `admin/productos.php` con filtro de sucursal
   - Actualizar menú de navegación

---

## 📞 Soporte

Si tienes problemas:

1. Verifica que PostgreSQL esté corriendo
2. Verifica las credenciales de conexión
3. Revisa los logs de PostgreSQL
4. Verifica que la base de datos `fudo` exista

---

## 📄 Archivos Importantes

- `migration_multisucursal.sql` - Script SQL de migración
- `ejecutar_migracion.ps1` - Script PowerShell automatizado
- `SISTEMA_MULTISUCURSAL.md` - Documentación completa del sistema
- `database.sql` - Schema completo (para instalaciones nuevas)

---

**Fecha:** 8 de octubre de 2025  
**Versión:** 1.0.0  
**Sistema:** FUDO - Multi-Sucursal
