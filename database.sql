-- ============================================================
-- FUDO - Base de datos Sistema Multi-Sucursal
-- Fecha: 9 de octubre de 2025
-- Descripción: Base de datos completa con soporte multi-sucursal
--              Incluye 3 roles: admin, admin_sucursal y usuario
-- ============================================================

-- Crear base de datos
CREATE DATABASE fudo;
\c fudo;

-- Habilitar extensión necesaria para crypt()/gen_salt()
-- Requiere privilegios de superusuario. Si fallara, ejecutar manualmente en pgAdmin/psql.
CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- ============================================================
-- PASO 1: Tabla sucursales (DEBE SER PRIMERA)
-- ============================================================
CREATE TABLE sucursales (
    id_sucursal SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(255),
    telefono VARCHAR(20),
    email VARCHAR(100),
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT NOW(),
    whatsapp VARCHAR(20),
    instagram VARCHAR(50)
);


-- Sucursales de ejemplo
INSERT INTO sucursales (nombre, direccion, telefono, email) VALUES
('Sucursal Centro', 'Av. Principal 123, Centro', '+56912345678', 'centro@fudo.cl'),
('Sucursal Plaza Norte', 'Av. Norte 456, Plaza Norte', '+56987654321', 'norte@fudo.cl'),
('Sucursal Mall Sur', 'Av. Sur 789, Mall Sur', '+56956781234', 'sur@fudo.cl');

-- ============================================================
-- PASO 2: Tabla usuarios_admin
-- Roles disponibles:
--   * admin: Super administrador con acceso total al sistema
--   * admin_sucursal: Administrador de sucursal con permisos de gestión operativa
--   * usuario: Usuario con permisos personalizables (visualización y opcionalmente edición)
-- ============================================================
CREATE TABLE usuarios_admin (
    id SERIAL PRIMARY KEY,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    contrasena TEXT NOT NULL, -- usar crypt()
    nombre_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    rol VARCHAR(20) NOT NULL DEFAULT 'admin_sucursal', -- 'admin', 'admin_sucursal' o 'usuario'
    id_sucursal INT REFERENCES sucursales(id_sucursal) ON DELETE SET NULL,
    permisos TEXT, -- JSON con permisos personalizados para rol 'usuario': {"pedidos":true,"mesas":true,"cocina":true,"mi_carta":true,"categorias":false,"productos":false}
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT NOW()
);

-- Super Admin (sin sucursal asignada)
INSERT INTO usuarios_admin (usuario, contrasena, nombre_completo, email, rol)
VALUES ('admin', crypt('admin123', gen_salt('bf')), 'Super Administrador', 'admin@fudo.cl', 'admin');

-- Admin Sucursal Centro
INSERT INTO usuarios_admin (usuario, contrasena, nombre_completo, email, rol, id_sucursal)
VALUES ('admin_centro', crypt('centro123', gen_salt('bf')), 'Admin Centro', 'admin.centro@fudo.cl', 'admin_sucursal', 1);

-- Admin Sucursal Norte
INSERT INTO usuarios_admin (usuario, contrasena, nombre_completo, email, rol, id_sucursal)
VALUES ('admin_norte', crypt('norte123', gen_salt('bf')), 'Admin Norte', 'admin.norte@fudo.cl', 'admin_sucursal', 2);

-- Admin Sucursal Sur
INSERT INTO usuarios_admin (usuario, contrasena, nombre_completo, email, rol, id_sucursal)
VALUES ('admin_sur', crypt('sur123', gen_salt('bf')), 'Admin Sur', 'admin.sur@fudo.cl', 'admin_sucursal', 3);

-- Usuario Solo Lectura - Sucursal Centro (permisos básicos por defecto)
INSERT INTO usuarios_admin (usuario, contrasena, nombre_completo, email, rol, id_sucursal, permisos)
VALUES ('usuario_centro', crypt('centro123', gen_salt('bf')), 'Usuario Centro', 'usuario.centro@fudo.cl', 'usuario', 1, '{"pedidos":true,"mesas":true,"cocina":true,"mi_carta":true,"categorias":false,"productos":false}');

-- Usuario Solo Lectura - Sucursal Norte (permisos completos de ejemplo)
INSERT INTO usuarios_admin (usuario, contrasena, nombre_completo, email, rol, id_sucursal, permisos)
VALUES ('usuario_norte', crypt('norte123', gen_salt('bf')), 'Usuario Norte', 'usuario.norte@fudo.cl', 'usuario', 2, '{"pedidos":true,"mesas":true,"cocina":true,"mi_carta":true,"categorias":true,"productos":true}');

-- ============================================================
-- PASO 3: Tabla categorias
-- ============================================================
CREATE TABLE categorias (
    id_categoria SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    id_sucursal INT REFERENCES sucursales(id_sucursal) ON DELETE CASCADE,
    estado BOOLEAN DEFAULT TRUE
);

INSERT INTO categorias (nombre, descripcion, id_sucursal, estado) VALUES
('Bebidas', 'Refrescos, jugos y café', 1, TRUE),
('Comidas', 'Entradas y platos principales', 1, TRUE),
('Postres', 'Dulces y postres variados', 1, TRUE),
('Bebidas', 'Refrescos, jugos y café', 2, TRUE),
('Comidas', 'Entradas y platos principales', 2, TRUE),
('Bebidas', 'Refrescos, jugos y café', 3, TRUE);

-- ============================================================
-- PASO 4: Tabla productos
-- ============================================================
CREATE TABLE productos (
    id_producto SERIAL PRIMARY KEY,
    id_categoria INT REFERENCES categorias(id_categoria),
    id_sucursal INT REFERENCES sucursales(id_sucursal) ON DELETE CASCADE,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio NUMERIC(10,2) NOT NULL,
    imagen VARCHAR(255),
    disponible BOOLEAN DEFAULT TRUE
);

INSERT INTO productos (id_categoria, id_sucursal, nombre, descripcion, precio, disponible) VALUES
(1, 1, 'Coca-Cola', 'Refresco de 350ml', 1500, TRUE),
(1, 1, 'Jugo de Naranja', 'Natural y fresco', 2000, TRUE),
(2, 1, 'Hamburguesa', 'Con carne, queso y lechuga', 5000, TRUE),
(2, 1, 'Pizza Margarita', 'Tomate, queso y albahaca', 7000, TRUE),
(3, 1, 'Helado Vainilla', 'Porción individual', 2500, TRUE),
(3, 1, 'Brownie', 'Con nueces', 3000, TRUE),
(4, 2, 'Coca-Cola', 'Refresco de 350ml', 1500, TRUE),
(4, 2, 'Jugo de Piña', 'Natural y fresco', 2200, TRUE),
(5, 2, 'Sándwich Club', 'Triple con pollo y tocino', 4500, TRUE),
(6, 3, 'Pepsi', 'Refresco de 350ml', 1500, TRUE),
(6, 3, 'Limonada', 'Natural', 1800, TRUE);

-- ============================================================
-- PASO 5: Tabla clientes
-- ============================================================
CREATE TABLE clientes (
    id_cliente SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    rut VARCHAR(15) UNIQUE,
    telefono VARCHAR(20),
    email VARCHAR(100),
    fecha_registro TIMESTAMP DEFAULT NOW()
);

INSERT INTO clientes (nombre, telefono, email) VALUES
('Cliente 1', '+56911111111', 'cliente1@mail.com'),
('Cliente 2', '+56922222222', 'cliente2@mail.com');

-- ============================================================
-- PASO 6: Tabla mesas
-- ============================================================
CREATE TABLE mesas (
    id_mesa SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    id_sucursal INT REFERENCES sucursales(id_sucursal) ON DELETE CASCADE,
    codigo_qr VARCHAR(255) UNIQUE
);

INSERT INTO mesas (nombre, id_sucursal) VALUES
('Mesa 1', 1),
('Mesa 2', 1),
('Mesa 3', 1),
('Mesa 1', 2),
('Mesa 2', 2),
('Mesa 1', 3),
('Mesa 2', 3);

-- ============================================================
-- PASO 7: Tabla pedidos
-- ============================================================
CREATE TABLE pedidos (
    id_pedido SERIAL PRIMARY KEY,
    id_cliente INT REFERENCES clientes(id_cliente),
    id_mesa INT REFERENCES mesas(id_mesa),
    id_sucursal INT REFERENCES sucursales(id_sucursal) ON DELETE CASCADE,
    fecha TIMESTAMP DEFAULT NOW(),
    estado VARCHAR(50) DEFAULT 'Pendiente',
    total NUMERIC(10,2) NOT NULL
);

-- ============================================================
-- PASO 8: Tabla detalle_pedido
-- ============================================================
CREATE TABLE detalle_pedido (
    id_detalle SERIAL PRIMARY KEY,
    id_pedido INT REFERENCES pedidos(id_pedido) ON DELETE CASCADE,
    id_producto INT REFERENCES productos(id_producto),
    cantidad INT NOT NULL,
    subtotal NUMERIC(10,2) NOT NULL
);

-- ============================================================
-- PASO 9: Crear índices para optimizar consultas
-- ============================================================
CREATE INDEX idx_categorias_sucursal ON categorias(id_sucursal);
CREATE INDEX idx_productos_sucursal ON productos(id_sucursal);
CREATE INDEX idx_productos_categoria ON productos(id_categoria);
CREATE INDEX idx_mesas_sucursal ON mesas(id_sucursal);
CREATE INDEX idx_pedidos_sucursal ON pedidos(id_sucursal);
CREATE INDEX idx_pedidos_mesa ON pedidos(id_mesa);
CREATE INDEX idx_pedidos_cliente ON pedidos(id_cliente);
CREATE INDEX idx_detalle_pedido ON detalle_pedido(id_pedido);
CREATE INDEX idx_usuarios_admin_sucursal ON usuarios_admin(id_sucursal);
CREATE INDEX idx_usuarios_admin_rol ON usuarios_admin(rol);

-- ============================================================
-- VERIFICACIÓN: Resumen de la base de datos
-- ============================================================
\echo '============================================================'
\echo 'BASE DE DATOS CREADA EXITOSAMENTE'
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
\echo 'CREDENCIALES DE ACCESO:'
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
\echo ''
\echo 'Usuario Solo Lectura - Centro:'
\echo '  Usuario: usuario_centro | Contraseña: centro123'
\echo ''
\echo 'Usuario Solo Lectura - Norte:'
\echo '  Usuario: usuario_norte | Contraseña: norte123'
\echo '============================================================'