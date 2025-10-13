# 🔍 ANÁLISIS PROFUNDO: Problema Permisos Cocina

## 📋 Resumen del Problema

**Usuario reporta:**
- Editó usuario desde `admin_centro`
- Desmarcó checkbox "Cocina"
- Usuario cerró sesión e inició nuevamente
- **PROBLEMA**: Sigue apareciendo "Cocina" en navbar

---

## 🎯 Posibles Causas Identificadas

### 1. ⚠️ Permisos por defecto en BD (CAUSA MÁS PROBABLE)

**Archivo:** `migrations/agregar_permisos_usuarios.sql` línea 17-18

```sql
UPDATE usuarios_admin 
SET permisos = '{"pedidos":true,"mesas":true,"cocina":true, ...}'
WHERE rol = 'usuario' AND permisos IS NULL;
```

**Problema:** Esta migración estableció `cocina:true` por defecto para TODOS los usuarios con rol 'usuario'.

**Impacto:** 
- Si el usuario afectado ya existía antes de la migración
- O si se creó después con permisos por defecto
- Tendrá `cocina:true` hardcodeado en la BD

**Verificación:**
```sql
SELECT usuario, permisos FROM usuarios_admin WHERE rol = 'usuario';
```

**Solución:**
```sql
UPDATE usuarios_admin
SET permisos = '{"pedidos":true,"mesas":true,"cocina":false,"mi_carta":true,"categorias":false,"productos":false}'
WHERE usuario = 'usuario_centro' AND rol = 'usuario';
```

---

### 2. 🔄 Sesión PHP no destruida completamente

**Archivo:** `application/controllers/Login.php` método `salir()`

```php
public function salir() {
    $this->session->sess_destroy();
    redirect('login');
}
```

**Problema:** 
- `sess_destroy()` debería destruir la sesión
- Pero el navegador puede cachear la sesión anterior
- O tener cookies residuales

**Verificación:**
1. Cerrar sesión normalmente → Problema persiste
2. Cerrar navegador completamente → Problema persiste
3. Abrir navegador en modo incógnito → ¿Problema persiste?

**Solución:**
```php
public function salir() {
    // Destruir todas las variables de sesión
    $_SESSION = array();
    
    // Destruir la cookie de sesión
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destruir sesión
    $this->session->sess_destroy();
    redirect('login');
}
```

---

### 3. 📝 Formulario no envía correctamente los datos

**Archivo:** `application/views/admin/usuarios.php` línea ~710-720

**Problema potencial:**
- Checkbox "Cocina" desmarcado NO se envía en POST
- Solo checkboxes marcados envían `name="permisos[cocina]"`
- Checkboxes desmarcados simplemente no existen en POST

**Verificación en Usuarios.php línea 186:**
```php
'cocina' => isset($permisos_array['cocina']),
```

✅ **ESTO ESTÁ CORRECTO:**
- Si checkbox marcado → `isset()` = `true`
- Si checkbox desmarcado → `isset()` = `false`

**Prueba de depuración:**
Agregar en `Usuarios.php` línea 180:
```php
// DEBUG: Ver qué se está enviando
error_log("PERMISOS POST: " . print_r($permisos_array, true));
error_log("PERMISOS JSON: " . $permisos_json);
```

---

### 4. 🌐 JavaScript fallback hardcodeado

**Archivo:** `application/views/admin/usuarios.php` línea 822

```javascript
let permisos = {
    pedidos: true, 
    mesas: true, 
    cocina: true,    // ⚠️ Valor por defecto
    mi_carta: true, 
    categorias: false, 
    productos: false
};
```

**Problema:**
- Este objeto se usa como FALLBACK si el JSON no parsea
- Si hay error en `JSON.parse()`, usa estos valores

**Verificación:**
```javascript
if (usuario.permisos) {
    try {
        permisos = JSON.parse(usuario.permisos);
        console.log('Permisos parseados:', permisos); // ✅ Agregar esto
    } catch (e) {
        console.error('Error parseando permisos:', e);
        console.log('Usando permisos por defecto'); // ✅ Agregar esto
    }
}
```

**Solución:**
Cambiar línea 822:
```javascript
let permisos = {
    pedidos: false, 
    mesas: false, 
    cocina: false,    // ✅ Todo en false por defecto
    mi_carta: false, 
    categorias: false, 
    productos: false
};
```

---

### 5. 🗄️ Caché de Base de Datos

**Problema:**
- PostgreSQL puede tener caché de queries
- CodeIgniter tiene caché de resultados

**Verificación:**
Ejecutar directamente en PostgreSQL:
```sql
\c fudo_db
SELECT usuario, permisos FROM usuarios_admin WHERE usuario = 'usuario_centro';
```

---

## 🛠️ Plan de Acción Paso a Paso

### Paso 1: Verificar Estado Actual en BD

```sql
-- Conectar a PostgreSQL
psql -U postgres -d fudo_db

-- Ver permisos actuales
SELECT usuario, permisos FROM usuarios_admin WHERE rol = 'usuario';
```

**Resultado esperado:**
```json
{
  "pedidos": true,
  "mesas": true,
  "cocina": false,    // ⚠️ Debe ser FALSE
  "mi_carta": true,
  "categorias": false,
  "productos": false
}
```

---

### Paso 2: Corregir BD si es necesario

Si `cocina: true`, ejecutar:

```sql
UPDATE usuarios_admin
SET permisos = '{"pedidos":true,"mesas":true,"cocina":false,"mi_carta":true,"categorias":false,"productos":false}'
WHERE usuario = 'usuario_centro';
```

---

### Paso 3: Limpiar Sesión y Cookies

```powershell
# En navegador:
1. F12 (Abrir DevTools)
2. Application → Storage → Clear site data
3. Cerrar navegador COMPLETAMENTE
4. Abrir en modo incógnito
```

---

### Paso 4: Prueba Completa

1. ✅ Abrir navegador en modo incógnito
2. ✅ Ir a `http://localhost/fudo`
3. ✅ Login con `usuario_centro`
4. ✅ Verificar navbar → **NO debe aparecer "🔥 Cocina"**
5. ✅ Intentar acceder directamente a `http://localhost/fudo/cocina`
6. ✅ Debe mostrar error 403 o redirigir

---

### Paso 5: Debugging Avanzado

Si el problema persiste, agregar logs:

**En Login.php línea 25:**
```php
$permisos = null;
if($u->rol == 'usuario' && !empty($u->permisos)) {
    $permisos = json_decode($u->permisos, true);
    error_log("LOGIN - Usuario: {$u->usuario}, Permisos: " . print_r($permisos, true));
}
```

**En pedidos.php línea 218:**
```php
<?php 
error_log("NAVBAR - Rol: $rol, Permisos: " . print_r($permisos, true));
?>
```

**Ver logs:**
```powershell
# En XAMPP
Get-Content C:\xampp\apache\logs\error.log -Tail 50 -Wait
```

---

## 🔍 Herramientas de Diagnóstico Creadas

### 1. Script Web de Diagnóstico
**Archivo:** `diagnostico_permisos.php`

```powershell
# Configurar
# Editar línea 11: $password = "tu_password_postgres";

# Acceder
http://localhost/fudo/diagnostico_permisos.php
```

**Funcionalidad:**
- ✅ Verifica estructura de tabla
- ✅ Lista todos los usuarios con rol 'usuario'
- ✅ Analiza JSON de permisos
- ✅ Simula carga en sesión PHP
- ✅ Genera SQL de corrección

---

### 2. Script SQL de Corrección
**Archivo:** `migrations/corregir_permiso_cocina.sql`

```sql
-- Ver estado actual
SELECT usuario, permisos FROM usuarios_admin WHERE rol = 'usuario';

-- Corregir usuario específico
UPDATE usuarios_admin
SET permisos = '{"pedidos":true,"mesas":true,"cocina":false,"mi_carta":true,"categorias":false,"productos":false}'
WHERE usuario = 'usuario_centro';
```

---

## 📊 Matriz de Diagnóstico

| Síntoma | Causa Probable | Solución |
|---------|----------------|----------|
| Cocina aparece después de logout/login | Permisos en BD tienen `cocina:true` | SQL UPDATE |
| Checkbox aparece marcado al reabrir formulario | BD no se actualizó correctamente | Revisar logs PHP |
| Checkbox aparece desmarcado pero sigue visible | Sesión no se destruyó | Limpiar cookies + modo incógnito |
| Error 403 al acceder directamente a /cocina | ✅ Backend funciona | Problema solo en frontend |
| Cocina visible incluso en modo incógnito | BD definitivamente tiene `cocina:true` | SQL UPDATE obligatorio |

---

## 🎯 Causa Más Probable

**90% de probabilidad:**

El campo `permisos` en la BD **SÍ tiene** `cocina:true`, porque:

1. ✅ La migración inicial lo estableció así por defecto
2. ✅ Al editar desde admin, si hubo algún error, no se guardó
3. ✅ La sesión carga permisos desde BD al login
4. ✅ Si BD dice `cocina:true`, la sesión tendrá `cocina:true`

**Solución definitiva:**

```sql
-- Ejecutar AHORA en PostgreSQL:
UPDATE usuarios_admin
SET permisos = '{"pedidos":true,"mesas":true,"cocina":false,"mi_carta":true,"categorias":false,"productos":false}'
WHERE usuario = 'usuario_centro' AND rol = 'usuario';

-- Verificar:
SELECT usuario, permisos FROM usuarios_admin WHERE usuario = 'usuario_centro';
```

Después:
1. Cerrar navegador completamente
2. Abrir en modo incógnito
3. Login con usuario_centro
4. ✅ **Cocina NO debe aparecer**

---

## 📝 Checklist de Verificación

- [ ] Ejecutar `diagnostico_permisos.php` y revisar resultados
- [ ] Verificar BD directamente con psql
- [ ] Confirmar valor de `permisos` en BD
- [ ] Si `cocina:true` → Ejecutar SQL UPDATE
- [ ] Limpiar cookies del navegador
- [ ] Probar en modo incógnito
- [ ] Verificar que navbar NO muestre "Cocina"
- [ ] Intentar acceso directo a `/cocina` → Debe bloquear
- [ ] Confirmar en logs PHP que permisos se cargan correctamente

---

## 💡 Mejoras Futuras

1. **Agregar logs en tiempo real:**
   - Panel admin muestre permisos actuales del usuario
   - Botón "Ver permisos en BD" en tabla usuarios

2. **Validación visual:**
   - Al guardar, mostrar JSON resultante en modal
   - Confirmar antes de guardar

3. **Destrucción robusta de sesión:**
   - Implementar token de sesión única
   - Invalidar sesión al cambiar permisos

4. **Debugging en desarrollo:**
   - Panel "Debug" que muestre `$_SESSION` completa
   - Botón "Recalcular permisos" sin logout

---

## 🚨 Acciones Inmediatas

**EJECUTAR AHORA:**

```sql
-- 1. Verificar
SELECT usuario, permisos FROM usuarios_admin WHERE rol = 'usuario';

-- 2. Corregir (si cocina=true)
UPDATE usuarios_admin
SET permisos = '{"pedidos":true,"mesas":true,"cocina":false,"mi_carta":true,"categorias":false,"productos":false}'
WHERE usuario = 'usuario_centro';

-- 3. Confirmar
SELECT usuario, permisos FROM usuarios_admin WHERE usuario = 'usuario_centro';
```

**LUEGO:**
- Cerrar navegador
- Abrir en incógnito
- Login con usuario_centro
- ✅ Verificar que NO aparece "Cocina"

---

**Fecha:** 13 de octubre de 2025  
**Estado:** Pendiente de verificación en BD
