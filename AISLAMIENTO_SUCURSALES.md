# 🔐 Separación de Datos por Sucursal - Explicación Detallada

## 🎯 Concepto Principal

Cada **Admin Sucursal** trabaja en un **entorno completamente aislado**:
- Solo ve los datos de SU sucursal
- Solo puede crear/editar/eliminar recursos de SU sucursal
- NO puede ver ni acceder a datos de otras sucursales

El **Super Admin** tiene **visibilidad total**:
- Ve todas las sucursales
- Puede cambiar entre sucursales con un filtro
- Puede crear recursos para cualquier sucursal

---

## 📊 Ejemplo Práctico

### Escenario Real:

**Sucursal Centro tiene:**
- 3 categorías: Bebidas, Comidas, Postres
- 6 productos: Coca-Cola, Jugo, Hamburguesa, Pizza, Helado, Brownie
- 3 mesas: Mesa 1, Mesa 2, Mesa 3

**Sucursal Plaza Norte tiene:**
- 2 categorías: Bebidas, Comidas
- 3 productos: Coca-Cola, Jugo de Piña, Sándwich Club
- 2 mesas: Mesa 1, Mesa 2

**Sucursal Mall Sur tiene:**
- 1 categoría: Bebidas
- 2 productos: Pepsi, Limonada
- 2 mesas: Mesa 1, Mesa 2

---

## 👤 Experiencia del Admin Sucursal Centro

### Login: `admin_centro` / `centro123`

#### En Categorías (`/admin/categorias`):
```
🏷️ Gestión de Categorías
Sucursal: Sucursal Centro  <-- Indicador fijo

┌─────────────────────────────────────────────┐
│ ID │ Nombre   │ Estado │ Acciones          │
├─────────────────────────────────────────────┤
│ 1  │ Bebidas  │ Activa │ ✏️ 👁️ 🗑️         │
│ 2  │ Comidas  │ Activa │ ✏️ 👁️ 🗑️         │
│ 3  │ Postres  │ Activa │ ✏️ 👁️ 🗑️         │
└─────────────────────────────────────────────┘

❌ NO ve categorías de Sucursal Plaza Norte ni Mall Sur
✅ Solo ve sus 3 categorías
```

#### Al crear una categoría:
```
Modal: Nueva Categoría

Nombre: [___________]

[Campo sucursal NO aparece]
❌ No puede seleccionar sucursal
✅ Automáticamente se asigna a Sucursal Centro
```

#### En Productos (`/admin/productos`):
```
🛍️ Gestión de Productos
Sucursal: Sucursal Centro  <-- Indicador fijo

Filtro por categoría: [Todas ▼] <-- Solo ve categorías de su sucursal

┌──────────────────────────────────────────────────────────────┐
│ Nombre        │ Categoría │ Precio │ Disponible │ Acciones  │
├──────────────────────────────────────────────────────────────┤
│ Coca-Cola     │ Bebidas   │ $1,500 │ Sí        │ ✏️ 👁️ 🗑️ │
│ Jugo Naranja  │ Bebidas   │ $2,000 │ Sí        │ ✏️ 👁️ 🗑️ │
│ Hamburguesa   │ Comidas   │ $5,000 │ Sí        │ ✏️ 👁️ 🗑️ │
│ Pizza         │ Comidas   │ $7,000 │ Sí        │ ✏️ 👁️ 🗑️ │
│ Helado        │ Postres   │ $2,500 │ Sí        │ ✏️ 👁️ 🗑️ │
│ Brownie       │ Postres   │ $3,000 │ Sí        │ ✏️ 👁️ 🗑️ │
└──────────────────────────────────────────────────────────────┘

❌ NO ve productos de otras sucursales
✅ Solo ve sus 6 productos
```

#### Intentando acceder a URLs protegidas:
```
❌ http://localhost/fudo/index.php/usuarios
   → Error 403: No tienes permisos para acceder a esta sección

❌ http://localhost/fudo/index.php/sucursales
   → Error 403: No tienes permisos para acceder a esta sección
```

---

## 👑 Experiencia del Super Admin

### Login: `admin` / `admin123`

#### En Categorías (`/admin/categorias`):
```
🏷️ Gestión de Categorías

Filtro por sucursal: [Todas las sucursales ▼]
                     [Sucursal Centro      ]
                     [Sucursal Plaza Norte ]
                     [Sucursal Mall Sur    ]

┌─────────────────────────────────────────────────────────────┐
│ ID │ Nombre   │ Sucursal              │ Estado │ Acciones  │
├─────────────────────────────────────────────────────────────┤
│ 1  │ Bebidas  │ Sucursal Centro       │ Activa │ ✏️ 👁️ 🗑️ │
│ 2  │ Comidas  │ Sucursal Centro       │ Activa │ ✏️ 👁️ 🗑️ │
│ 3  │ Postres  │ Sucursal Centro       │ Activa │ ✏️ 👁️ 🗑️ │
│ 4  │ Bebidas  │ Sucursal Plaza Norte  │ Activa │ ✏️ 👁️ 🗑️ │
│ 5  │ Comidas  │ Sucursal Plaza Norte  │ Activa │ ✏️ 👁️ 🗑️ │
│ 6  │ Bebidas  │ Sucursal Mall Sur     │ Activa │ ✏️ 👁️ 🗑️ │
└─────────────────────────────────────────────────────────────┘

✅ Ve TODAS las categorías de TODAS las sucursales
✅ Puede filtrar por sucursal específica
```

#### Al crear una categoría:
```
Modal: Nueva Categoría

Nombre: [___________]

Sucursal: [Sucursal Centro ▼]  <-- OBLIGATORIO seleccionar
          [Sucursal Plaza Norte ]
          [Sucursal Mall Sur    ]

✅ Debe seleccionar para qué sucursal es la categoría
```

#### Menú de navegación (solo super admin):
```
┌────────────────────────────────────────────────────────┐
│ 📦 Pedidos │ 🏷️ Categorías │ 🛍️ Productos │          │
│ 🪑 Mesas   │ 🔥 Cocina      │ 👥 Usuarios   │ 🏢 Sucursales │
│                                               🚪 Salir │
└────────────────────────────────────────────────────────┘

✅ Enlaces 👥 Usuarios y 🏢 Sucursales SOLO visibles para super admin
```

---

## 🔒 Seguridad Implementada en el Backend

### Nivel 1: Constructor del Controlador
```php
public function __construct() {
    // ...
    $this->rol = $this->session->userdata('rol');
    $this->id_sucursal = $this->session->userdata('id_sucursal');
}
```

### Nivel 2: Filtrado en Métodos
```php
public function categorias() {
    // SI es admin_sucursal: usar SU id_sucursal
    // SI es admin: null (ve todas)
    $id_sucursal = ($this->rol == 'admin_sucursal') ? $this->id_sucursal : null;
    
    $data['categorias'] = $this->Categoria_model->obtener_todas($id_sucursal);
    //                                                            ↑
    //                                              Filtro automático
}
```

### Nivel 3: Modelo con Filtro
```php
public function obtener_todas($id_sucursal = null) {
    if ($id_sucursal !== null) {
        $this->db->where('id_sucursal', $id_sucursal);
        //                ↑
        //      Solo retorna datos de ESA sucursal
    }
    return $this->db->get('categorias')->result();
}
```

### Nivel 4: Asignación Automática al Crear
```php
public function categoria_crear() {
    // SI es admin_sucursal
    if($this->rol == 'admin_sucursal') {
        $id_sucursal_final = $this->id_sucursal;  // ← Forzado a SU sucursal
    } else {
        // Super admin: toma del formulario
        $id_sucursal_final = $this->input->post('id_sucursal');
    }
    
    $datos = [
        'nombre' => $nombre,
        'id_sucursal' => $id_sucursal_final  // ← Siempre asignado
    ];
}
```

---

## 📝 Verificación de Aislamiento

### Test 1: Admin Sucursal NO puede ver datos de otras sucursales

**Query que ejecuta el sistema para admin_centro:**
```sql
-- Obtener categorías
SELECT * FROM categorias 
WHERE id_sucursal = 1;  -- ← FORZADO a su sucursal

-- Resultado: Solo categorías de Sucursal Centro
```

**Query que ejecuta para super admin:**
```sql
-- Obtener categorías
SELECT * FROM categorias;  -- ← Sin filtro = todas

-- Resultado: Categorías de TODAS las sucursales
```

### Test 2: Admin Sucursal crea categoría

**Formulario envía:**
```json
{
  "nombre": "Ensaladas"
  // NO envía id_sucursal
}
```

**Backend procesa:**
```php
// Controlador detecta que es admin_sucursal
$id_sucursal_final = $this->id_sucursal;  // = 1 (Sucursal Centro)

// Inserta en BD
INSERT INTO categorias (nombre, id_sucursal)
VALUES ('Ensaladas', 1);
```

**Resultado:** Categoría creada SOLO para Sucursal Centro ✅

---

## 🚫 Restricciones por Rol

| Acción | Super Admin | Admin Sucursal |
|--------|-------------|----------------|
| Ver todas las categorías | ✅ Sí | ❌ No (solo su sucursal) |
| Crear categoría para cualquier sucursal | ✅ Sí | ❌ No (solo su sucursal) |
| Ver productos de otras sucursales | ✅ Sí | ❌ No |
| Editar productos de otras sucursales | ✅ Sí | ❌ No |
| Acceder a /usuarios | ✅ Sí | ❌ Error 403 |
| Acceder a /sucursales | ✅ Sí | ❌ Error 403 |
| Crear usuarios | ✅ Sí | ❌ No |
| Cambiar de sucursal (filtro) | ✅ Sí | ❌ No (fijo en su sucursal) |

---

## 🎨 UI Diferenciada

### Admin Sucursal ve:
```
┌──────────────────────────────────────────┐
│ 🏢 Sucursal Centro            ← Indicador│
│ ──────────────────────────────────────── │
│ 🏷️ Gestión de Categorías                │
│                                           │
│ [➕ Nueva Categoría]                      │
│                                           │
│ Tabla con categorías...                  │
└──────────────────────────────────────────┘

❌ Sin selector de sucursal
✅ Indicador fijo de su sucursal
```

### Super Admin ve:
```
┌──────────────────────────────────────────┐
│ Filtrar por: [Todas ▼] [Aplicar]         │
│ ──────────────────────────────────────── │
│ 🏷️ Gestión de Categorías                │
│                                           │
│ [➕ Nueva Categoría]                      │
│                                           │
│ Tabla con columna "Sucursal"...          │
└──────────────────────────────────────────┘

✅ Selector de sucursal en header
✅ Columna adicional mostrando sucursal
```

---

## 🔄 Flujo de Datos Completo

### Admin Sucursal crea producto:

```
1. Usuario: admin_centro (id_sucursal=1)
   ↓
2. Login → Sesión guarda: rol='admin_sucursal', id_sucursal=1
   ↓
3. Va a /admin/productos
   ↓
4. Controlador: productos()
   - Detecta rol = 'admin_sucursal'
   - Filtra: obtener_todos(id_sucursal=1)
   ↓
5. Modelo: obtener_todos(1)
   - Query: SELECT * FROM productos WHERE id_sucursal=1
   - Retorna: Solo productos de Sucursal Centro
   ↓
6. Vista muestra: 6 productos (de Sucursal Centro)
   ↓
7. Usuario crea nuevo producto "Ensalada César"
   - Formulario NO tiene campo sucursal
   ↓
8. Controlador: producto_crear()
   - Detecta rol = 'admin_sucursal'
   - Forzado: id_sucursal_final = 1
   - INSERT con id_sucursal=1
   ↓
9. Producto creado SOLO para Sucursal Centro ✅
   ↓
10. Admin de otras sucursales NO lo ven ✅
```

---

## ✅ Conclusión

El sistema garantiza **aislamiento total** de datos entre sucursales:

1. ✅ **Backend:** Filtros automáticos por `id_sucursal`
2. ✅ **Sesión:** Cada usuario lleva su `id_sucursal`
3. ✅ **Modelos:** Todos aceptan filtro opcional
4. ✅ **Controladores:** Aplican filtro según rol
5. ✅ **Seguridad:** Admin sucursal NO puede cambiar su `id_sucursal`
6. ✅ **UI:** Indicadores visuales claros

**Cada admin sucursal trabaja en su propio "universo" aislado** 🌍
