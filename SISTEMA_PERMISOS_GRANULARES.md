# 🔐 Sistema de Permisos Granulares - FUDO

## 📋 Resumen de Cambios

Se ha implementado un sistema completo de permisos granulares que permite:

1. **Rol "usuario"** con permisos personalizables
2. **Admin Sucursal** puede crear y gestionar usuarios de su sucursal
3. **Validación de permisos** en backend y frontend

---

## 🗄️ PASO 1: Actualizar Base de Datos

### Ejecutar Migración SQL

```bash
# Opción 1: Desde terminal/CMD
psql -U postgres -d fudo -f "migrations/agregar_permisos_usuarios.sql"

# Opción 2: Desde pgAdmin
# Abre pgAdmin → Herramientas → Query Tool → Pega el contenido del archivo
```

### ¿Qué hace la migración?
- Agrega columna `permisos` (TEXT/JSON) a tabla `usuarios_admin`
- Asigna permisos por defecto a usuarios existentes con rol 'usuario'

---

## 🎯 Estructura de Permisos

### Permisos Disponibles

```json
{
  "pedidos": true,      // 📋 Acceso a vista de pedidos
  "mesas": true,        // 🪑 Acceso a gestión de mesas
  "cocina": true,       // 👨‍🍳 Acceso a panel de cocina
  "mi_carta": true,     // 📖 Acceso a vista de carta
  "categorias": false,  // 🏷️ Acceso a categorías (admin)
  "productos": false    // 🛍️ Acceso a productos (admin)
}
```

### Permisos por Defecto

**Usuario básico (solo lectura operativa):**
```json
{
  "pedidos": true,
  "mesas": true,
  "cocina": true,
  "mi_carta": true,
  "categorias": false,
  "productos": false
}
```

**Usuario con permisos administrativos:**
```json
{
  "pedidos": true,
  "mesas": true,
  "cocina": true,
  "mi_carta": true,
  "categorias": true,   // ✅ Puede ver/gestionar categorías
  "productos": true     // ✅ Puede ver/gestionar productos
}
```

---

## 👥 Roles y Capacidades

### ⭐ Super Admin (`admin`)
- **Acceso:** Todo el sistema
- **Puede crear:** Admin Sucursal, Usuario
- **Puede gestionar:** Todas las sucursales
- **Restricciones:** Ninguna

### 👤 Admin Sucursal (`admin_sucursal`)
- **Acceso:** Su sucursal únicamente
- **Puede crear:** Solo usuarios con rol "Usuario" de su sucursal
- **Puede gestionar:** Usuarios, pedidos, mesas, productos, categorías de su sucursal
- **Restricciones:** No puede crear otros admin_sucursal ni super admin

### 👁️ Usuario (`usuario`)
- **Acceso:** Según permisos asignados
- **Puede ver:** Solo lectura (no puede crear/editar/eliminar)
- **Puede gestionar:** Ninguna acción de escritura
- **Restricciones:** Solo visualización, requiere sucursal asignada

---

## 🔧 Archivos Modificados

### Base de Datos
- ✅ `database.sql` - Estructura actualizada con columna permisos
- ✅ `migrations/agregar_permisos_usuarios.sql` - Migración para BD existentes

### Backend (Controladores)
- ✅ `application/controllers/Login.php` - Guarda permisos en sesión
- ✅ `application/controllers/Usuarios.php` - Gestión de usuarios y permisos
- ✅ `application/controllers/Admin.php` - Validación permisos categorías/productos/mi_carta
- ✅ `application/controllers/Mesas.php` - Validación permisos mesas
- ✅ `application/controllers/Cocina.php` - Validación permisos cocina

### Frontend (Vistas)
- ✅ `application/views/admin/usuarios.php` - Checkboxes de permisos en formularios

---

## 📝 Uso del Sistema

### Crear Usuario con Permisos Personalizados

1. **Como Super Admin o Admin Sucursal:**
   - Ir a: `http://localhost/fudo/usuarios`
   - Clic en "➕ Nuevo Usuario"
   - Seleccionar rol "👁️ Usuario (Solo Lectura)"
   - Seleccionar sucursal
   - **Marcar permisos deseados:**
     - ✅ Pedidos (recomendado)
     - ✅ Mesas (recomendado)
     - ✅ Cocina (recomendado)
     - ✅ Mi Carta (recomendado)
     - ⚠️ Categorías (opcional, acceso administrativo)
     - ⚠️ Productos (opcional, acceso administrativo)
   - Guardar

2. **Validaciones automáticas:**
   - Admin Sucursal solo puede crear usuarios de su sucursal
   - Admin Sucursal solo puede asignar rol "Usuario"
   - Permisos solo aplican a rol "usuario"

### Editar Permisos de Usuario Existente

1. Ir a gestión de usuarios
2. Clic en botón "✏️" del usuario
3. Cambiar checkboxes de permisos
4. Guardar cambios

---

## 🧪 Pruebas

### Usuarios de Ejemplo

| Usuario | Contraseña | Rol | Sucursal | Permisos |
|---------|-----------|-----|----------|----------|
| `admin` | `admin123` | Super Admin | - | Todos |
| `admin_centro` | `centro123` | Admin Sucursal | Centro | Todos (su sucursal) |
| `usuario_centro` | `centro123` | Usuario | Centro | Básicos (pedidos, mesas, cocina, carta) |
| `usuario_norte` | `norte123` | Usuario | Norte | Completos (incluye categorías, productos) |

### Casos de Prueba

**1. Login como `usuario_centro`:**
- ✅ Debe ver: Pedidos, Mesas, Cocina, Mi Carta
- ❌ No debe ver: Categorías, Productos, Usuarios, Sucursales

**2. Login como `usuario_norte`:**
- ✅ Debe ver: Pedidos, Mesas, Cocina, Mi Carta, Categorías, Productos
- ❌ No debe ver: Usuarios, Sucursales
- ⚠️ Solo lectura en todas las secciones

**3. Login como `admin_centro` e intentar crear usuario:**
- ✅ Puede crear usuarios con rol "Usuario"
- ✅ Solo para sucursal "Centro"
- ❌ No puede crear Admin Sucursal ni Super Admin

---

## 🔒 Seguridad

### Validaciones Backend

```php
// Ejemplo: Validar permiso en controlador
private function tiene_permiso($seccion) {
    if($this->rol == 'admin' || $this->rol == 'admin_sucursal') {
        return true;
    }
    if($this->rol == 'usuario' && is_array($this->permisos)) {
        return isset($this->permisos[$seccion]) && $this->permisos[$seccion] === true;
    }
    return false;
}

// Uso en método
public function categorias() {
    if(!$this->tiene_permiso('categorias')) {
        show_error('No tienes permisos', 403);
    }
    // ... resto del código
}
```

### Validaciones Frontend

Las vistas deben verificar permisos antes de mostrar opciones:

```php
<?php if($this->session->userdata('rol') == 'usuario'): ?>
    <?php 
    $permisos = $this->session->userdata('permisos');
    if(isset($permisos['categorias']) && $permisos['categorias']): 
    ?>
        <a href="<?= site_url('admin/categorias') ?>">Categorías</a>
    <?php endif; ?>
<?php endif; ?>
```

---

## 📊 Diagrama de Flujo

```
┌─────────────┐
│   Login     │
└──────┬──────┘
       │
       v
┌──────────────────┐
│ Verificar Rol    │
└────┬────┬────┬───┘
     │    │    │
     v    v    v
   Admin  AS  Usuario
     │    │    │
     │    │    └──> Verificar Permisos JSON
     │    │            │
     │    │            v
     │    │         ┌─────────────────┐
     │    │         │ Mostrar según   │
     │    │         │ permisos        │
     │    │         └─────────────────┘
     │    │
     │    └──> Gestionar su sucursal
     │         Crear usuarios
     │
     └──> Acceso total

```

---

## 🚀 Próximos Pasos Recomendados

1. **Navbar Dinámico:** Actualizar navbars en vistas para mostrar/ocultar opciones según permisos
2. **Auditoría:** Agregar logs de acciones por usuario
3. **Permisos Granulares Adicionales:** 
   - Crear/Editar/Eliminar por separado
   - Exportar reportes
   - Configuración avanzada
4. **UI/UX:** Agregar badges visuales de permisos en tablas de usuarios

---

## 📞 Soporte

Si encuentras errores o necesitas ayuda:
1. Verifica que la migración SQL se ejecutó correctamente
2. Revisa los logs en `application/logs/`
3. Verifica permisos en la sesión: `var_dump($this->session->userdata('permisos'));`

---

**Fecha de implementación:** 13 de octubre de 2025  
**Versión:** 2.0 - Sistema de Permisos Granulares
