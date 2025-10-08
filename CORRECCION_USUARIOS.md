# 🔧 Corrección: Gestión de Usuarios

**Fecha:** 8 de octubre de 2025  
**Problema:** El super admin no podía editar los datos de los admin sucursal

---

## 🐛 Problema Identificado

El sistema tenía un **desajuste de nombres de campos** entre la base de datos, el modelo y la vista:

- **Base de datos:** La tabla `usuarios_admin` tiene `id` como PRIMARY KEY
- **Código original:** Algunos lugares usaban `id_usuario` que no existía
- **Resultado:** Las operaciones CRUD fallaban silenciosamente

---

## ✅ Correcciones Aplicadas

### 1. **Usuario_model.php** - Mapeo de Campos

Se agregó `u.id as id_usuario` en todos los SELECT para crear un alias consistente:

```php
// ANTES (❌ Error)
public function obtener_todos() {
    return $this->db->select('u.*, s.nombre as nombre_sucursal')
                    ->from('usuarios_admin u')
                    // ...
}

// DESPUÉS (✅ Correcto)
public function obtener_todos() {
    return $this->db->select('u.id as id_usuario, u.*, s.nombre as nombre_sucursal')
                    ->from('usuarios_admin u')
                    // ...
}
```

**Métodos actualizados:**
- ✅ `obtener_todos()` → Agrega alias `u.id as id_usuario`
- ✅ `obtener_por_rol($rol)` → Agrega alias `u.id as id_usuario`
- ✅ `obtener_por_sucursal($id_sucursal)` → Agrega alias `u.id as id_usuario`
- ✅ `obtener_por_id($id)` → Agrega alias `u.id as id_usuario`
- ✅ `actualizar($id, $datos)` → Usa `id` correctamente con WHERE
- ✅ `eliminar($id)` → Usa `id` correctamente con WHERE
- ✅ `cambiar_estado($id, $estado)` → Usa `id` correctamente con WHERE

---

### 2. **Usuarios.php** (Controlador) - Parámetros URL

Se actualizaron los métodos para aceptar parámetros tanto por URL como por POST:

```php
// ANTES (❌ Solo POST)
public function editar() {
    $id = $this->input->post('id');
    // ...
}

// DESPUÉS (✅ URL + POST)
public function editar($id = null) {
    // Obtener ID de parámetro URL o POST
    if(empty($id)) {
        $id = $this->input->post('id_usuario');
    }
    // ...
}
```

**Métodos actualizados:**

#### `editar($id = null)`
- Acepta `$id` por URL: `/usuarios/editar/5`
- También acepta por POST: `id_usuario`
- Validación completa de datos
- Prevención de duplicados (usuario/email)

#### `eliminar($id = null)`
- Acepta `$id` por URL: `/usuarios/eliminar/5`
- También acepta por POST: `id`
- Previene auto-eliminación del usuario actual

#### `cambiar_estado($id = null, $estado = null)`
- Acepta `$id` y `$estado` por URL: `/usuarios/cambiar_estado/5/1`
- También acepta por POST: `id`, `estado`
- Conversión robusta de estado a booleano
- Previene auto-desactivación

---

### 3. **Login.php** - Consistencia

El controlador Login ya usaba correctamente `$u->id`:

```php
public function acceder() {
    $u = $this->Usuario_model->verificar_usuario($usuario, $contrasena);
    if($u) {
        $session_data = [
            'id_usuario' => $u->id,  // ✅ Correcto desde el inicio
            // ...
        ];
    }
}
```

---

## 🎯 Flujo Completo de Operaciones

### **Crear Usuario**
```
Vista (usuarios.php)
  ↓ POST /usuarios/crear
Controlador (Usuarios::crear)
  ↓ Validaciones + encriptación
Modelo (Usuario_model::crear)
  ↓ INSERT en usuarios_admin
Base de Datos → Retorna id
  ↓ JSON response
Vista → Recarga página
```

### **Editar Usuario**
```
Vista (usuarios.php)
  ↓ Carga datos con usuarios[i].id_usuario
  ↓ POST /usuarios/editar/:id
Controlador (Usuarios::editar($id))
  ↓ Captura $id de URL
  ↓ Validaciones (no duplicar usuario/email)
Modelo (Usuario_model::actualizar)
  ↓ UPDATE usuarios_admin WHERE id = $id
Base de Datos → Actualiza registro
  ↓ JSON response
Vista → Recarga página
```

### **Eliminar Usuario**
```
Vista (usuarios.php)
  ↓ Confirmación JavaScript
  ↓ POST /usuarios/eliminar/:id
Controlador (Usuarios::eliminar($id))
  ↓ Previene auto-eliminación
Modelo (Usuario_model::eliminar)
  ↓ DELETE FROM usuarios_admin WHERE id = $id
Base de Datos → Elimina registro
  ↓ JSON response
Vista → Recarga página
```

### **Cambiar Estado**
```
Vista (usuarios.php)
  ↓ POST /usuarios/cambiar_estado/:id/:estado
Controlador (Usuarios::cambiar_estado($id, $estado))
  ↓ Previene auto-desactivación
  ↓ Convierte estado a boolean
Modelo (Usuario_model::cambiar_estado)
  ↓ UPDATE usuarios_admin SET activo = $estado WHERE id = $id
Base de Datos → Actualiza estado
  ↓ JSON response
Vista → Recarga página
```

---

## 🧪 Cómo Probar

### 1. **Acceder como Super Admin**
```
URL: http://localhost/fudo/index.php/login
Usuario: admin
Contraseña: admin123
```

### 2. **Ir a Gestión de Usuarios**
```
URL: http://localhost/fudo/index.php/usuarios
```

### 3. **Probar Crear Usuario**
- Click en "➕ Crear Usuario"
- Completar formulario:
  - Usuario: `test_admin`
  - Contraseña: `test123`
  - Nombre: `Usuario de Prueba`
  - Email: `test@fudo.cl`
  - Rol: `Admin Sucursal`
  - Sucursal: `Sucursal Centro`
- Click en "💾 Guardar Usuario"
- **Esperado:** Mensaje "✅ Usuario creado exitosamente" y recarga

### 4. **Probar Editar Usuario**
- Click en "✏️" del usuario recién creado
- Modificar nombre: `Usuario Modificado`
- Click en "💾 Actualizar Usuario"
- **Esperado:** Mensaje "✅ Usuario actualizado exitosamente"

### 5. **Probar Cambiar Estado**
- Click en "🔒" (desactivar) del usuario
- Confirmar
- **Esperado:** Badge cambia a "❌ Inactivo"
- Click en "🔓" (activar)
- **Esperado:** Badge cambia a "✅ Activo"

### 6. **Probar Eliminar**
- Click en "🗑️" del usuario de prueba
- Confirmar eliminación
- **Esperado:** Usuario desaparece de la tabla

---

## 📊 Validaciones Implementadas

### **En Crear:**
- ✅ Campos requeridos: usuario, contraseña, nombre_completo, rol
- ✅ Si rol = admin_sucursal → id_sucursal obligatorio
- ✅ Usuario único (no duplicados)
- ✅ Email único (no duplicados)
- ✅ Contraseña mínimo 6 caracteres

### **En Editar:**
- ✅ Campos requeridos: usuario, nombre_completo, rol
- ✅ Contraseña opcional (solo si se cambia)
- ✅ Si rol = admin_sucursal → id_sucursal obligatorio
- ✅ Usuario único excepto actual (no duplicados)
- ✅ Email único excepto actual (no duplicados)

### **En Eliminar:**
- ✅ No permite eliminar al usuario actual (auto-eliminación)
- ✅ Confirmación JavaScript antes de eliminar

### **En Cambiar Estado:**
- ✅ No permite desactivar al usuario actual
- ✅ Conversión robusta de estado (string/int/bool)

---

## 🔐 Seguridad

### **Autenticación**
```php
// Constructor verifica sesión
if(!$this->session->userdata('logueado')) {
    redirect('login');
}
```

### **Autorización**
```php
// Solo super admin puede gestionar usuarios
if($this->session->userdata('rol') != 'admin') {
    show_error('No tienes permisos', 403);
}
```

### **Encriptación de Contraseñas**
```php
// Usuario_model::crear()
$hash_result = $this->db->query(
    "SELECT crypt(?, gen_salt('bf')) as hash", 
    array($datos['contrasena'])
)->row();
$datos['contrasena'] = $hash_result->hash;
```

### **Prevención SQL Injection**
```php
// Query Builder con placeholders
$this->db->where('id', $id)
         ->update('usuarios_admin', $datos);
```

---

## 📂 Archivos Modificados

1. **application/controllers/Usuarios.php**
   - Métodos `editar()`, `eliminar()`, `cambiar_estado()` ahora aceptan parámetros URL
   - Validaciones mejoradas

2. **application/models/Usuario_model.php**
   - Todos los SELECT agregan alias `u.id as id_usuario`
   - Consistencia en nombres de campos

3. **application/views/admin/usuarios.php** (ya creada)
   - Vista CRUD completa con JavaScript
   - Filtros dinámicos por rol/sucursal
   - Modales crear/editar con validaciones

4. **application/views/admin/login.php** (mejorada)
   - Diseño moderno con Montserrat
   - Gradientes y animaciones
   - Toggle password

---

## ✨ Resultado Final

El super admin ahora puede:

- ✅ **Ver** todos los usuarios del sistema
- ✅ **Crear** nuevos usuarios (admin o admin_sucursal)
- ✅ **Editar** cualquier usuario (nombre, email, rol, sucursal)
- ✅ **Cambiar contraseñas** de cualquier usuario
- ✅ **Activar/Desactivar** usuarios
- ✅ **Eliminar** usuarios (excepto sí mismo)
- ✅ **Filtrar** por rol y sucursal

Todo con una interfaz moderna, validaciones robustas y mensajes claros de éxito/error.

---

## 🎨 Diseño Consistente

La interfaz mantiene el diseño establecido:
- Fuente **Montserrat** (400, 600, 700, 800)
- Gradientes **135deg** (#b08c6a → #a3c06b)
- Border-radius **14px** en cards
- Box-shadow **0 14px 36px rgba(11,11,11,0.06)**
- Emojis como iconografía
- Badges coloridos para estados
- Animaciones hover en botones

---

**¡Sistema completamente funcional! 🚀**
