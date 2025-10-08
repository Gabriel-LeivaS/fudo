-- ============================================================
-- MIGRACIÓN: Sistema Multi-Sucursal con Roles
-- Fecha: 8 de octubre de 2025
-- Descripción: Agrega soporte para múltiples sucursales y roles
-- ============================================================

-- Conectar a la base de datos
\c fudo;

-- ============================================================
-- PASO 1: Crear tabla sucursales
-- ============================================================
CREATE TABLE IF NOT EXISTS sucursales (
    id_sucursal SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(255),
    telefono VARCHAR(20),
    email VARCHAR(100),
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT NOW()
);

-- Insertar sucursales de ejemplo
INSERT INTO sucursales (nombre, direccion, telefono, email) VALUES
('Sucursal Centro', 'Av. Principal 123, Centro', '+56912345678', 'centro@fudo.cl'),
('Sucursal Plaza Norte', 'Av. Norte 456, Plaza Norte', '+56987654321', 'norte@fudo.cl'),
('Sucursal Mall Sur', 'Av. Sur 789, Mall Sur', '+56956781234', 'sur@fudo.cl')
ON CONFLICT DO NOTHING;

-- ============================================================
-- PASO 2: Actualizar tabla usuarios_admin
-- ============================================================

-- Agregar nuevas columnas
ALTER TABLE usuarios_admin 
ADD COLUMN IF NOT EXISTS email VARCHAR(100),
ADD COLUMN IF NOT EXISTS rol VARCHAR(20) NOT NULL DEFAULT 'admin_sucursal',
ADD COLUMN IF NOT EXISTS id_sucursal INT REFERENCES sucursales(id_sucursal) ON DELETE SET NULL,
ADD COLUMN IF NOT EXISTS fecha_creacion TIMESTAMP DEFAULT NOW();

-- Actualizar usuario admin existente como super admin
UPDATE usuarios_admin 
SET rol = 'admin', 
    email = 'admin@fudo.cl'
WHERE usuario = 'admin' AND rol IS NULL;

-- Insertar usuarios admin_sucursal de ejemplo (solo si no existen)
INSERT INTO usuarios_admin (usuario, contrasena, nombre_completo, email, rol, id_sucursal)
SELECT 'admin_centro', crypt('centro123', gen_salt('bf')), 'Admin Centro', 'admin.centro@fudo.cl', 'admin_sucursal', 1
WHERE NOT EXISTS (SELECT 1 FROM usuarios_admin WHERE usuario = 'admin_centro');

INSERT INTO usuarios_admin (usuario, contrasena, nombre_completo, email, rol, id_sucursal)
SELECT 'admin_norte', crypt('norte123', gen_salt('bf')), 'Admin Norte', 'admin.norte@fudo.cl', 'admin_sucursal', 2
WHERE NOT EXISTS (SELECT 1 FROM usuarios_admin WHERE usuario = 'admin_norte');

INSERT INTO usuarios_admin (usuario, contrasena, nombre_completo, email, rol, id_sucursal)
SELECT 'admin_sur', crypt('sur123', gen_salt('bf')), 'Admin Sur', 'admin.sur@fudo.cl', 'admin_sucursal', 3
WHERE NOT EXISTS (SELECT 1 FROM usuarios_admin WHERE usuario = 'admin_sur');

-- ============================================================
-- PASO 3: Agregar id_sucursal a tabla categorias
-- ============================================================

ALTER TABLE categorias 
ADD COLUMN IF NOT EXISTS id_sucursal INT REFERENCES sucursales(id_sucursal) ON DELETE CASCADE;

-- Asignar sucursal por defecto a categorías existentes (Sucursal Centro)
UPDATE categorias 
SET id_sucursal = 1 
WHERE id_sucursal IS NULL;

-- Crear categorías de ejemplo para otras sucursales
INSERT INTO categorias (nombre, descripcion, id_sucursal, estado) VALUES
('Bebidas', 'Refrescos, jugos y café', 2, TRUE),
('Comidas', 'Entradas y platos principales', 2, TRUE),
('Bebidas', 'Refrescos, jugos y café', 3, TRUE)
ON CONFLICT DO NOTHING;

-- ============================================================
-- PASO 4: Agregar id_sucursal a tabla productos
-- ============================================================

ALTER TABLE productos 
ADD COLUMN IF NOT EXISTS id_sucursal INT REFERENCES sucursales(id_sucursal) ON DELETE CASCADE;

-- Asignar sucursal por defecto a productos existentes (Sucursal Centro)
UPDATE productos 
SET id_sucursal = 1 
WHERE id_sucursal IS NULL;

-- Crear productos de ejemplo para otras sucursales
-- Primero obtener IDs de categorías de sucursal 2 y 3
DO $$
DECLARE
    cat_bebidas_2 INT;
    cat_comidas_2 INT;
    cat_bebidas_3 INT;
BEGIN
    -- Obtener ID de categoría Bebidas de sucursal 2
    SELECT id_categoria INTO cat_bebidas_2 
    FROM categorias 
    WHERE nombre = 'Bebidas' AND id_sucursal = 2 
    LIMIT 1;
    
    -- Obtener ID de categoría Comidas de sucursal 2
    SELECT id_categoria INTO cat_comidas_2 
    FROM categorias 
    WHERE nombre = 'Comidas' AND id_sucursal = 2 
    LIMIT 1;
    
    -- Obtener ID de categoría Bebidas de sucursal 3
    SELECT id_categoria INTO cat_bebidas_3 
    FROM categorias 
    WHERE nombre = 'Bebidas' AND id_sucursal = 3 
    LIMIT 1;
    
    -- Insertar productos solo si encontró las categorías
    IF cat_bebidas_2 IS NOT NULL THEN
        INSERT INTO productos (id_categoria, id_sucursal, nombre, descripcion, precio, disponible) VALUES
        (cat_bebidas_2, 2, 'Coca-Cola', 'Refresco de 350ml', 1500, TRUE),
        (cat_bebidas_2, 2, 'Jugo de Piña', 'Natural y fresco', 2200, TRUE)
        ON CONFLICT DO NOTHING;
    END IF;
    
    IF cat_comidas_2 IS NOT NULL THEN
        INSERT INTO productos (id_categoria, id_sucursal, nombre, descripcion, precio, disponible) VALUES
        (cat_comidas_2, 2, 'Sándwich Club', 'Triple con pollo y tocino', 4500, TRUE)
        ON CONFLICT DO NOTHING;
    END IF;
    
    IF cat_bebidas_3 IS NOT NULL THEN
        INSERT INTO productos (id_categoria, id_sucursal, nombre, descripcion, precio, disponible) VALUES
        (cat_bebidas_3, 3, 'Pepsi', 'Refresco de 350ml', 1500, TRUE),
        (cat_bebidas_3, 3, 'Limonada', 'Natural', 1800, TRUE)
        ON CONFLICT DO NOTHING;
    END IF;
END $$;

-- ============================================================
-- PASO 5: Agregar id_sucursal a tabla mesas
-- ============================================================

ALTER TABLE mesas 
ADD COLUMN IF NOT EXISTS id_sucursal INT REFERENCES sucursales(id_sucursal) ON DELETE CASCADE;

-- Asignar sucursal por defecto a mesas existentes (Sucursal Centro)
UPDATE mesas 
SET id_sucursal = 1 
WHERE id_sucursal IS NULL;

-- Crear mesas de ejemplo para otras sucursales
INSERT INTO mesas (nombre, id_sucursal) VALUES
('Mesa 1', 2),
('Mesa 2', 2),
('Mesa 1', 3),
('Mesa 2', 3)
ON CONFLICT DO NOTHING;

-- ============================================================
-- PASO 6: Agregar id_sucursal a tabla pedidos
-- ============================================================

ALTER TABLE pedidos 
ADD COLUMN IF NOT EXISTS id_sucursal INT REFERENCES sucursales(id_sucursal) ON DELETE CASCADE;

-- Asignar sucursal por defecto a pedidos existentes (Sucursal Centro)
UPDATE pedidos 
SET id_sucursal = 1 
WHERE id_sucursal IS NULL;

-- ============================================================
-- PASO 7: Crear índices para optimizar consultas
-- ============================================================

CREATE INDEX IF NOT EXISTS idx_categorias_sucursal ON categorias(id_sucursal);
CREATE INDEX IF NOT EXISTS idx_productos_sucursal ON productos(id_sucursal);
CREATE INDEX IF NOT EXISTS idx_mesas_sucursal ON mesas(id_sucursal);
CREATE INDEX IF NOT EXISTS idx_pedidos_sucursal ON pedidos(id_sucursal);
CREATE INDEX IF NOT EXISTS idx_usuarios_admin_sucursal ON usuarios_admin(id_sucursal);
CREATE INDEX IF NOT EXISTS idx_usuarios_admin_rol ON usuarios_admin(rol);

-- ============================================================
-- VERIFICACIÓN: Mostrar resultados
-- ============================================================

\echo '============================================================'
\echo 'MIGRACIÓN COMPLETADA EXITOSAMENTE'
\echo '============================================================'
\echo ''
\echo 'Sucursales creadas:'
SELECT id_sucursal, nombre, activo FROM sucursales ORDER BY id_sucursal;

\echo ''
\echo 'Usuarios admin creados:'
SELECT id, usuario, nombre_completo, rol, id_sucursal FROM usuarios_admin ORDER BY id;

\echo ''
\echo 'Resumen de datos por sucursal:'
SELECT 
    s.id_sucursal,
    s.nombre as sucursal,
    COUNT(DISTINCT c.id_categoria) as categorias,
    COUNT(DISTINCT p.id_producto) as productos,
    COUNT(DISTINCT m.id_mesa) as mesas
FROM sucursales s
LEFT JOIN categorias c ON s.id_sucursal = c.id_sucursal
LEFT JOIN productos p ON s.id_sucursal = p.id_sucursal
LEFT JOIN mesas m ON s.id_sucursal = m.id_sucursal
GROUP BY s.id_sucursal, s.nombre
ORDER BY s.id_sucursal;

\echo ''
\echo '============================================================'
\echo 'CREDENCIALES DE PRUEBA:'
\echo '============================================================'
\echo 'Super Admin:'
\echo '  Usuario: admin | Contraseña: admin123'
\echo ''
\echo 'Admin Sucursal Centro:'
\echo '  Usuario: admin_centro | Contraseña: centro123'
\echo ''
\echo 'Admin Sucursal Plaza Norte:'
\echo '  Usuario: admin_norte | Contraseña: norte123'
\echo ''
\echo 'Admin Sucursal Mall Sur:'
\echo '  Usuario: admin_sur | Contraseña: sur123'
\echo '============================================================'
