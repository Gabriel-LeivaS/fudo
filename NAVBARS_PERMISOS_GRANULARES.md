# 🔐 NAVBARS CON PERMISOS GRANULARES

**Fecha:** 13 de octubre de 2025  
**Implementación:** Sistema de permisos granulares en navbars para rol "Usuario"

---

## 🎯 **OBJETIVO**

Implementar verificación de permisos granulares en **TODOS los navbars** del sistema para que:
- **Admin y Admin Sucursal** → Vean todas las opciones disponibles
- **Usuario** → Solo vea los enlaces para los cuales tiene permisos configurados

---

## 🔍 **PROBLEMA ANTERIOR**

### **Antes:**
```php
<!-- ❌ INCORRECTO: Mostraba TODAS las opciones para usuario -->
<?php if($rol == 'admin_sucursal' || $rol == 'usuario'): ?>
    <a href="<?= site_url('admin') ?>">📦 Pedidos</a>
    <a href="<?= site_url('mesas') ?>">🪑 Mesas</a>
    <a href="<?= site_url('cocina') ?>">🔥 Cocina</a>
<?php endif; ?>
```

**Problema:** Todos los usuarios veían todas las opciones, aunque no tuvieran permisos configurados.

---

## ✅ **SOLUCIÓN IMPLEMENTADA**

### **Ahora:**
```php
<!-- ✅ CORRECTO: Verifica permisos granulares -->
<?php 
$rol = $this->session->userdata('rol');
$permisos = $this->session->userdata('permisos');

// Función helper para verificar permisos
$tiene_permiso = function($seccion) use ($rol, $permisos) {
    if($rol == 'admin' || $rol == 'admin_sucursal') return true;
    if($rol == 'usuario' && is_array($permisos)) {
        return isset($permisos[$seccion]) && $permisos[$seccion] === true;
    }
    return false;
};
?>

<?php if($tiene_permiso('pedidos')): ?>
    <a href="<?= site_url('admin') ?>">📦 Pedidos</a>
<?php endif; ?>

<?php if($tiene_permiso('mesas')): ?>
    <a href="<?= site_url('mesas') ?>">🪑 Mesas</a>
<?php endif; ?>
```

---

## 🛠️ **LÓGICA DE LA FUNCIÓN `tiene_permiso()`**

```php
function tiene_permiso($seccion) {
    // 1. Admin y Admin Sucursal: SIEMPRE tienen acceso
    if($rol == 'admin' || $rol == 'admin_sucursal') 
        return true;
    
    // 2. Usuario: Solo si tiene el permiso específico = true
    if($rol == 'usuario' && is_array($permisos)) {
        return isset($permisos[$seccion]) && $permisos[$seccion] === true;
    }
    
    // 3. Por defecto: Sin acceso
    return false;
}
```

---

## 📁 **ARCHIVOS MODIFICADOS**

| Archivo | Ubicación | Líneas | Estado |
|---------|-----------|--------|--------|
| `pedidos.php` | `application/views/admin/` | 210-268 | ✅ Actualizado |
| `categorias.php` | `application/views/admin/` | 275-333 | ✅ Actualizado |
| `productos.php` | `application/views/admin/` | 275-333 | ✅ Actualizado |
| `mesas.php` | `application/views/admin/` | 332-390 | ✅ Actualizado |
| `sucursales.php` | `application/views/admin/` | 230-288 | ✅ Actualizado |
| `usuarios.php` | `application/views/admin/` | 250-308 | ✅ Actualizado |

**Total:** 6 archivos actualizados con permisos granulares

---

## 🧪 **CASOS DE PRUEBA**

### **Test 1: Usuario con Permisos Básicos**

**Configuración:**
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

**Navbar Esperado:**
```
📦 Pedidos | 🪑 Mesas | 🔥 Cocina | 📋 Mi Carta | 👁️ Solo Lectura | 🚪 Salir
```

**NO debe ver:** 🏷️ Categorías, 🛍️ Productos, 👥 Usuarios, 🏢 Sucursales

---

### **Test 2: Usuario con Permisos Completos**

**Configuración:**
```json
{
  "pedidos": true,
  "mesas": true,
  "cocina": true,
  "mi_carta": true,
  "categorias": true,
  "productos": true
}
```

**Navbar Esperado:**
```
📦 Pedidos | 🏷️ Categorías | 🛍️ Productos | 📋 Mi Carta | 🪑 Mesas | 🔥 Cocina | 👁️ Solo Lectura | 🚪 Salir
```

**NO debe ver:** 👥 Usuarios, 🏢 Sucursales

---

### **Test 3: Usuario Sin Permisos**

**Configuración:**
```json
{
  "pedidos": false,
  "mesas": false,
  "cocina": false,
  "mi_carta": false,
  "categorias": false,
  "productos": false
}
```

**Navbar Esperado:**
```
👁️ Solo Lectura | 🚪 Salir
```

**NO debe ver:** Ningún enlace de navegación (solo salir)

---

### **Test 4: Admin Sucursal**

**Sin permisos JSON (no aplica para admin_sucursal)**

**Navbar Esperado:**
```
📦 Pedidos | 🏷️ Categorías | 🛍️ Productos | 📋 Mi Carta | 🪑 Mesas | 🔥 Cocina | 👥 Usuarios | 🚪 Salir
```

**NO debe ver:** 🏢 Sucursales (solo super admin)

---

### **Test 5: Super Admin**

**Sin permisos JSON (no aplica para admin)**

**Navbar Esperado:**
```
📦 Pedidos | 🏷️ Categorías | 🛍️ Productos | 📋 Mi Carta | 🪑 Mesas | 🔥 Cocina | 👥 Usuarios | 🏢 Sucursales | 🚪 Salir
```

**Ve:** TODAS las opciones disponibles

---

## 📊 **MATRIZ DE PERMISOS**

| Sección | Admin | Admin Sucursal | Usuario (con permiso) | Usuario (sin permiso) |
|---------|-------|----------------|----------------------|----------------------|
| 📦 Pedidos | ✅ | ✅ | ✅ | ❌ |
| 🏷️ Categorías | ✅ | ✅ | ✅ | ❌ |
| 🛍️ Productos | ✅ | ✅ | ✅ | ❌ |
| 📋 Mi Carta | ✅ | ✅ | ✅ | ❌ |
| 🪑 Mesas | ✅ | ✅ | ✅ | ❌ |
| 🔥 Cocina | ✅ | ✅ | ✅ | ❌ |
| 👥 Usuarios | ✅ | ✅ | ❌ | ❌ |
| 🏢 Sucursales | ✅ | ❌ | ❌ | ❌ |

---

## 🔒 **SEGURIDAD EN CAPAS**

### **1. Frontend (Navbar):**
- Oculta enlaces según permisos
- Mejora UX: Usuario no ve opciones sin acceso

### **2. Backend (Controladores):**
- Método `tiene_permiso($seccion)` en cada controlador
- Valida permisos antes de procesar solicitudes
- Retorna error 403 si no tiene permiso

### **3. Sesión:**
- Permisos guardados en `$_SESSION['permisos']`
- Parseados desde JSON en Login.php
- Disponibles en toda la aplicación

---

## 🧪 **GUÍA DE PRUEBAS PASO A PASO**

### **Paso 1: Crear Usuario con Permisos Limitados**

1. Login como `admin_centro` (contraseña: `centro123`)
2. Ir a **👥 Usuarios** → **➕ Crear Usuario**
3. Llenar datos:
   - Usuario: `usuario_limitado`
   - Contraseña: `123456`
   - Nombre: `Usuario Limitado`
   - Email: `limitado@centro.com`
   - Rol: `Usuario`
   - Sucursal: `Centro`
4. **Marcar SOLO:**
   - ✅ Pedidos
   - ✅ Mesas
   - ❌ Cocina (dejar sin marcar)
   - ❌ Mi Carta (dejar sin marcar)
   - ❌ Categorías (dejar sin marcar)
   - ❌ Productos (dejar sin marcar)
5. Guardar

---

### **Paso 2: Probar Login con Usuario Limitado**

1. Cerrar sesión
2. Login con:
   - Usuario: `usuario_limitado`
   - Contraseña: `123456`
3. **Verificar navbar muestra SOLO:**
   - ✅ 📦 Pedidos
   - ✅ 🪑 Mesas
   - ✅ 👁️ Solo Lectura
   - ✅ 🚪 Salir
4. **Verificar navbar NO muestra:**
   - ❌ 🔥 Cocina
   - ❌ 📋 Mi Carta
   - ❌ 🏷️ Categorías
   - ❌ 🛍️ Productos
   - ❌ 👥 Usuarios
   - ❌ 🏢 Sucursales

---

### **Paso 3: Verificar Seguridad Backend**

1. Estando logueado como `usuario_limitado`
2. En el navegador, escribir manualmente:
   ```
   http://localhost/fudo/admin/categorias
   ```
3. **Resultado esperado:** Error 403 "No tienes permisos para acceder a esta sección"

---

### **Paso 4: Editar Permisos y Verificar**

1. Cerrar sesión y login como `admin_centro`
2. Ir a **👥 Usuarios** → Buscar `usuario_limitado` → **✏️ Editar**
3. **Marcar TAMBIÉN:**
   - ✅ Cocina
   - ✅ Mi Carta
4. Guardar
5. Cerrar sesión y login como `usuario_limitado`
6. **Verificar que AHORA sí aparecen:**
   - ✅ 🔥 Cocina
   - ✅ 📋 Mi Carta

---

## ✅ **CHECKLIST DE VALIDACIÓN**

- [ ] ✅ Usuario con permiso "pedidos" ve enlace Pedidos
- [ ] ✅ Usuario sin permiso "pedidos" NO ve enlace Pedidos
- [ ] ✅ Usuario con permiso "mesas" ve enlace Mesas
- [ ] ✅ Usuario sin permiso "mesas" NO ve enlace Mesas
- [ ] ✅ Usuario con permiso "cocina" ve enlace Cocina
- [ ] ✅ Usuario sin permiso "cocina" NO ve enlace Cocina
- [ ] ✅ Usuario con permiso "mi_carta" ve enlace Mi Carta
- [ ] ✅ Usuario sin permiso "mi_carta" NO ve enlace Mi Carta
- [ ] ✅ Usuario con permiso "categorias" ve enlace Categorías
- [ ] ✅ Usuario sin permiso "categorias" NO ve enlace Categorías
- [ ] ✅ Usuario con permiso "productos" ve enlace Productos
- [ ] ✅ Usuario sin permiso "productos" NO ve enlace Productos
- [ ] ✅ Admin Sucursal ve TODOS los enlaces (excepto Sucursales)
- [ ] ✅ Super Admin ve TODOS los enlaces (incluido Sucursales)
- [ ] ✅ Badge "Solo Lectura" aparece para rol usuario
- [ ] ✅ Acceso directo por URL sin permiso → Error 403

---

## 🎨 **ESTRUCTURA DEL CÓDIGO**

### **Template de Navbar Actualizado:**

```php
<nav class="navbar">
    <div class="container-fluid">
        <span class="navbar-brand">🍽️ FUDO</span>
        <div class="d-flex align-items-center gap-3">
            <?php 
            $rol = $this->session->userdata('rol');
            $permisos = $this->session->userdata('permisos');
            
            $tiene_permiso = function($seccion) use ($rol, $permisos) {
                if($rol == 'admin' || $rol == 'admin_sucursal') return true;
                if($rol == 'usuario' && is_array($permisos)) {
                    return isset($permisos[$seccion]) && $permisos[$seccion] === true;
                }
                return false;
            };
            ?>
            
            <!-- Enlaces según permisos -->
            <?php if($tiene_permiso('pedidos')): ?>
                <a href="<?= site_url('admin') ?>">📦 Pedidos</a>
            <?php endif; ?>
            
            <!-- ... más enlaces ... -->
            
            <?php if($rol == 'usuario'): ?>
                <span class="badge bg-info">👁️ Solo Lectura</span>
            <?php endif; ?>
            
            <a href="<?= site_url('login/salir') ?>">🚪 Salir</a>
        </div>
    </div>
</nav>
```

---

## 🚀 **BENEFICIOS DE LA IMPLEMENTACIÓN**

1. **UX Mejorada:** Usuario solo ve opciones relevantes
2. **Seguridad:** Doble capa (frontend + backend)
3. **Flexibilidad:** Permisos configurables por usuario
4. **Escalabilidad:** Fácil agregar nuevas secciones
5. **Mantenibilidad:** Código centralizado y reutilizable
6. **Claridad Visual:** Badge "Solo Lectura" indica restricciones

---

## 📝 **NOTAS IMPORTANTES**

### **¿Por qué Admin y Admin Sucursal ven todo?**
- Son roles administrativos con permisos completos por diseño
- No necesitan configuración granular
- Simplifican la gestión operativa

### **¿Qué pasa si permisos es NULL?**
- El usuario NO verá ningún enlace (excepto Salir)
- Es responsabilidad del admin configurar permisos al crear usuario

### **¿Los permisos se validan también en backend?**
- **SÍ**, cada controlador tiene su método `tiene_permiso()`
- Acceso directo por URL sin permiso → Error 403
- Frontend oculta, backend bloquea

---

## 🔄 **PRÓXIMOS PASOS RECOMENDADOS**

1. ✅ **Probar con diferentes configuraciones de permisos**
2. ✅ **Verificar acceso directo por URL devuelve 403**
3. ⚠️ **Considerar agregar tooltips explicando permisos**
4. ⚠️ **Implementar auditoría de accesos denegados**
5. ⚠️ **Agregar panel de "Permisos Actuales" en perfil de usuario**

---

**Estado:** ✅ **IMPLEMENTADO Y FUNCIONAL**  
**Autor:** GitHub Copilot  
**Versión:** 1.0  
**Última Actualización:** 13 de octubre de 2025
