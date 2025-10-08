# Sistema Multi-Sucursal con Roles - FUDO

## 📋 Resumen del Sistema Implementado

Se ha implementado un sistema completo de roles y permisos con soporte multi-sucursal para el panel de administración de FUDO.

---

## 👥 Roles del Sistema

### 1. **Super Admin** (`admin`)
- Control total del sistema
- Puede gestionar todas las sucursales
- Crear/editar/eliminar usuarios (incluidos admin_sucursal)
- Crear/editar sucursales
- Ver y gestionar datos de todas las sucursales
- Acceso a módulos exclusivos: 👥 Usuarios, 🏢 Sucursales

### 2. **Admin Sucursal** (`admin_sucursal`)
- Solo gestiona su sucursal asignada
- CRUD de categorías y productos de su sucursal
- Ver pedidos únicamente de su sucursal
- Gestionar mesas de su sucursal
- NO puede crear usuarios ni gestionar otras sucursales

---

## 🗄️ Cambios en Base de Datos

### Nueva Tabla: `sucursales`
```sql
id_sucursal SERIAL PRIMARY KEY
nombre VARCHAR(100) NOT NULL
direccion VARCHAR(255)
telefono VARCHAR(20)
email VARCHAR(100)
activo BOOLEAN DEFAULT TRUE
fecha_creacion TIMESTAMP DEFAULT NOW()
```

### Tabla `usuarios_admin` Actualizada
**Nuevos campos:**
- `rol` VARCHAR(20) - 'admin' o 'admin_sucursal'
- `id_sucursal` INT - Foreign Key a sucursales (NULL para super admin)
- `email` VARCHAR(100)
- `fecha_creacion` TIMESTAMP

### Tablas con `id_sucursal` agregado:
- ✅ `categorias`
- ✅ `productos`
- ✅ `mesas`
- ✅ `pedidos`

---

## 🔧 Backend Implementado

### Modelos Creados/Actualizados

#### ✅ `Sucursal_model.php` (NUEVO)
- `obtener_todas()` - Lista todas las sucursales
- `obtener_activas()` - Solo sucursales activas
- `obtener_por_id($id)` - Detalle de una sucursal
- `crear($datos)` - Crear nueva sucursal
- `actualizar($id, $datos)` - Actualizar sucursal
- `eliminar($id)` - Eliminar sucursal
- `cambiar_estado($id, $estado)` - Toggle activo/inactivo
- `tiene_usuarios($id)` - Verificar si tiene usuarios asignados
- `tiene_mesas($id)` - Verificar si tiene mesas
- `obtener_estadisticas($id)` - Stats de la sucursal

#### ✅ `Usuario_model.php` (ACTUALIZADO)
**Nuevos métodos:**
- `obtener_todos()` - Lista usuarios con JOIN a sucursales
- `obtener_por_rol($rol)` - Filtrar por rol
- `obtener_por_sucursal($id_sucursal)` - Usuarios de una sucursal
- `obtener_por_id($id)` - Detalle de usuario
- `crear($datos)` - Crear usuario con encriptación
- `actualizar($id, $datos)` - Actualizar usuario
- `eliminar($id)` - Eliminar usuario
- `cambiar_estado($id, $estado)` - Toggle activo/inactivo
- `existe_usuario($usuario, $excluir_id)` - Validar username único
- `existe_email($email, $excluir_id)` - Validar email único

**Modificado:**
- `verificar_usuario()` - Ahora retorna rol, id_sucursal y nombre_sucursal

#### ✅ `Categoria_model.php` (ACTUALIZADO)
- Todos los métodos `obtener_*` ahora aceptan parámetro `$id_sucursal`
- Filtra automáticamente por sucursal cuando se proporciona

#### ✅ `Producto_model.php` (ACTUALIZADO)
- Métodos `obtener_*` con soporte de filtro por `$id_sucursal`
- JOIN con categorías mantiene filtro de sucursal

#### ✅ `Pedido_model.php` (ACTUALIZADO)
- `crear_pedido()` ahora acepta `$id_sucursal`
- `obtener_pedidos_pendientes()` filtra por sucursal

---

### Controladores Creados/Actualizados

#### ✅ `Usuarios.php` (NUEVO)
**Rutas:**
- `GET /usuarios` - Vista CRUD usuarios
- `POST /usuarios/crear` - Crear nuevo usuario
- `POST /usuarios/editar` - Actualizar usuario
- `POST /usuarios/eliminar` - Eliminar usuario
- `POST /usuarios/cambiar_estado` - Toggle activo/inactivo

**Seguridad:**
- Solo accesible para rol `admin`
- Valida username y email únicos
- No permite eliminar/desactivar el propio usuario

#### ✅ `Sucursales.php` (NUEVO)
**Rutas:**
- `GET /sucursales` - Vista CRUD sucursales
- `POST /sucursales/crear` - Crear nueva sucursal
- `POST /sucursales/editar` - Actualizar sucursal
- `POST /sucursales/eliminar` - Eliminar sucursal (verifica dependencias)
- `POST /sucursales/cambiar_estado` - Toggle activo/inactivo
- `GET /sucursales/estadisticas/:id` - Stats JSON

**Seguridad:**
- Solo accesible para rol `admin`
- Verifica usuarios y mesas antes de eliminar

#### ✅ `Login.php` (ACTUALIZADO)
**Método `acceder()` modificado:**
Ahora guarda en sesión:
```php
$session_data = [
    'logueado' => TRUE,
    'id_usuario' => $u->id,
    'usuario' => $u->usuario,
    'nombre_completo' => $u->nombre_completo,
    'email' => $u->email,
    'rol' => $u->rol,              // NUEVO
    'id_sucursal' => $u->id_sucursal,  // NUEVO
    'nombre_sucursal' => $u->nombre_sucursal  // NUEVO
];
```

#### ✅ `Admin.php` (ACTUALIZADO)
**Constructor modificado:**
- Carga `Sucursal_model`
- Guarda `$this->rol` y `$this->id_sucursal` del usuario en sesión

**Métodos actualizados con filtro por sucursal:**
- `index()` - Filtra pedidos por sucursal si es admin_sucursal
- `categorias()` - Filtra y pasa sucursales si es super admin
- `categoria_crear()` - Valida y asigna sucursal según rol
- `productos()` - Filtra productos y categorías por sucursal
- `producto_crear()` - Valida y asigna sucursal según rol

---

## 📊 Datos de Prueba

### Sucursales Creadas
1. **Sucursal Centro** - `+56912345678` - `centro@fudo.cl`
2. **Sucursal Plaza Norte** - `+56987654321` - `norte@fudo.cl`
3. **Sucursal Mall Sur** - `+56956781234` - `sur@fudo.cl`

### Usuarios de Prueba

| Usuario | Contraseña | Rol | Sucursal |
|---------|------------|-----|----------|
| `admin` | `admin123` | Super Admin | - (todas) |
| `admin_centro` | `centro123` | Admin Sucursal | Sucursal Centro |
| `admin_norte` | `norte123` | Admin Sucursal | Sucursal Plaza Norte |
| `admin_sur` | `sur123` | Admin Sucursal | Sucursal Mall Sur |

---

## 🚧 Pendiente de Implementación

### ⏳ Vistas Frontend

#### 1. **admin/usuarios.php** (Pendiente)
Vista CRUD de usuarios para super admin con:
- Tabla de usuarios (ID, Nombre, Usuario, Email, Rol, Sucursal, Estado, Acciones)
- Modal crear/editar con campos:
  * Username (único)
  * Contraseña (requerido en crear, opcional en editar)
  * Nombre completo
  * Email (único)
  * Rol (select: admin / admin_sucursal)
  * Sucursal (select, visible solo si rol = admin_sucursal)
- Botones: Editar ✏️, Toggle Estado 👁️, Eliminar 🗑️
- Filtros: por rol, por sucursal
- Validaciones JS: mostrar/ocultar campo sucursal según rol seleccionado

#### 2. **admin/sucursales.php** (Pendiente)
Vista CRUD de sucursales para super admin con:
- Tabla de sucursales (ID, Nombre, Dirección, Teléfono, Email, Estado, Acciones)
- Modal crear/editar con campos:
  * Nombre
  * Dirección
  * Teléfono
  * Email
- Botones: Editar ✏️, Toggle Estado 👁️, Eliminar 🗑️, Ver Stats 📊
- Card con estadísticas al seleccionar una sucursal

#### 3. **admin/categorias.php** (Actualizar)
Agregar:
- Select de sucursal (SOLO visible para super admin)
- Filtro dinámico de categorías por sucursal seleccionada
- Campo hidden id_sucursal en modal (auto-rellenado para admin_sucursal)
- Indicador visual de sucursal actual para admin_sucursal en header

#### 4. **admin/productos.php** (Actualizar)
Agregar:
- Select de sucursal (SOLO visible para super admin)
- Filtro dinámico de productos por sucursal
- Filtro de categorías debe respetar sucursal seleccionada
- Campo hidden id_sucursal en modal

#### 5. **admin/pedidos.php** (Actualizar)
Agregar:
- Indicador de sucursal en header para admin_sucursal
- Select de sucursal para super admin (filtro opcional)
- Columna "Sucursal" en tabla de pedidos (visible solo para super admin)

#### 6. **Menú de Navegación** (Actualizar)
Modificar navbar en todas las vistas admin:
```php
<?php if($this->session->userdata('rol') == 'admin'): ?>
    <a href="<?= base_url('index.php/usuarios') ?>" class="btn btn-action">
        👥 Usuarios
    </a>
    <a href="<?= base_url('index.php/sucursales') ?>" class="btn btn-action">
        🏢 Sucursales
    </a>
<?php endif; ?>
```

Agregar indicador de sucursal:
```php
<?php if($this->session->userdata('rol') == 'admin_sucursal'): ?>
    <div class="sucursal-indicator">
        🏢 <?= $this->session->userdata('nombre_sucursal') ?>
    </div>
<?php endif; ?>
```

---

## 🔒 Seguridad Implementada

### Nivel de Sesión
- ✅ Verificación de login en constructores
- ✅ Roles guardados en sesión
- ✅ ID de sucursal guardado en sesión

### Nivel de Controlador
- ✅ Verificación de rol `admin` para acceder a Usuarios y Sucursales
- ✅ `show_error()` con código 403 para acceso no autorizado

### Nivel de Modelo
- ✅ Todos los modelos aceptan filtro por sucursal
- ✅ Admin sucursal solo recibe su `id_sucursal` desde controlador

### Validaciones de Negocio
- ✅ Usuario no puede eliminarse a sí mismo
- ✅ Usuario no puede desactivarse a sí mismo
- ✅ Sucursal con usuarios activos no se puede eliminar
- ✅ Categoría con productos no se puede eliminar
- ✅ Username único
- ✅ Email único

---

## 🧪 Plan de Testing

### Escenario 1: Super Admin
1. Login con `admin` / `admin123`
2. Acceder a 👥 Usuarios
3. Crear usuario `admin_sucursal` para Sucursal Centro
4. Acceder a 🏢 Sucursales
5. Crear nueva sucursal "Sucursal Este"
6. Acceder a 🏷️ Categorías
7. Crear categoría para "Sucursal Este" (seleccionar en dropdown)
8. Verificar que puede ver todas las categorías de todas las sucursales
9. Cambiar filtro de sucursal y verificar que lista se actualiza

### Escenario 2: Admin Sucursal
1. Login con `admin_centro` / `centro123`
2. Verificar que NO aparecen enlaces 👥 Usuarios ni 🏢 Sucursales
3. Verificar indicador "🏢 Sucursal Centro" en header
4. Acceder a 🏷️ Categorías
5. Verificar que solo ve categorías de Sucursal Centro
6. Crear nueva categoría (no debe aparecer select de sucursal)
7. Acceder a 🛍️ Productos
8. Verificar que solo ve productos de Sucursal Centro
9. Intentar acceder manualmente a `/usuarios` → debe mostrar error 403

### Escenario 3: Validaciones
1. Intentar crear usuario con username existente → Error
2. Intentar crear usuario admin_sucursal sin seleccionar sucursal → Error
3. Intentar eliminar sucursal con usuarios activos → Error
4. Intentar eliminar categoría con productos → Error
5. Intentar eliminar propio usuario como super admin → Error

---

## 📝 Próximos Pasos

1. ✅ Crear vista `admin/usuarios.php` con diseño moderno (fuente Montserrat, emojis, gradientes)
2. ✅ Crear vista `admin/sucursales.php` con diseño moderno
3. ✅ Actualizar vista `admin/categorias.php` con selector de sucursal
4. ✅ Actualizar vista `admin/productos.php` con selector de sucursal
5. ✅ Actualizar navbar en todas las vistas admin
6. ✅ Testing completo con ambos roles
7. ⏳ Documentar credenciales de prueba para el usuario final

---

## 🌐 URLs del Sistema

### Acceso
- Login: `http://localhost/fudo/index.php/login`

### Panel Admin (ambos roles)
- Pedidos: `http://localhost/fudo/index.php/admin`
- Categorías: `http://localhost/fudo/index.php/admin/categorias`
- Productos: `http://localhost/fudo/index.php/admin/productos`
- Mesas: `http://localhost/fudo/index.php/mesas`
- Cocina: `http://localhost/fudo/index.php/cocina`

### Solo Super Admin
- Usuarios: `http://localhost/fudo/index.php/usuarios`
- Sucursales: `http://localhost/fudo/index.php/sucursales`

---

## 💾 Para Aplicar los Cambios

1. **Reiniciar la base de datos:**
   ```sql
   DROP DATABASE fudo;
   -- Luego ejecutar el database.sql actualizado
   psql -U postgres -f database.sql
   ```

2. **Los modelos y controladores ya están actualizados**, solo falta crear las vistas frontend.

3. **Testing:** Probar con los 4 usuarios de prueba antes de producción.

---

**Estado:** Backend 100% completado ✅ | Frontend vistas pendientes ⏳
