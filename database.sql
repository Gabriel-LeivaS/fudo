-- =========================
-- Base de datos: fudo
-- =========================

-- Crear base de datos
CREATE DATABASE fudo;
\c fudo;

-- Habilitar extensión necesaria para crypt()/gen_salt()
-- Requiere privilegios de superusuario. Si fallara, ejecutar manualmente en pgAdmin/psql.
CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- =========================
-- Tabla: categorias
-- =========================
CREATE TABLE categorias (
    id_categoria SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    id_sucursal INT REFERENCES sucursales(id_sucursal) ON DELETE CASCADE,
    estado BOOLEAN DEFAULT TRUE
);

INSERT INTO categorias (nombre, descripcion, id_sucursal) VALUES
('Bebidas', 'Refrescos, jugos y café', 1),
('Comidas', 'Entradas y platos principales', 1),
('Postres', 'Dulces y postres variados', 1),
('Bebidas', 'Refrescos, jugos y café', 2),
('Comidas', 'Entradas y platos principales', 2),
('Bebidas', 'Refrescos, jugos y café', 3);

-- =========================
-- Tabla: productos
-- =========================
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

INSERT INTO productos (id_categoria, id_sucursal, nombre, descripcion, precio) VALUES
(1, 1, 'Coca-Cola', 'Refresco de 350ml', 1500),
(1, 1, 'Jugo de Naranja', 'Natural y fresco', 2000),
(2, 1, 'Hamburguesa', 'Con carne, queso y lechuga', 5000),
(2, 1, 'Pizza Margarita', 'Tomate, queso y albahaca', 7000),
(3, 1, 'Helado Vainilla', 'Porción individual', 2500),
(3, 1, 'Brownie', 'Con nueces', 3000),
(4, 2, 'Coca-Cola', 'Refresco de 350ml', 1500),
(4, 2, 'Jugo de Piña', 'Natural y fresco', 2200),
(5, 2, 'Sándwich Club', 'Triple con pollo y tocino', 4500),
(6, 3, 'Pepsi', 'Refresco de 350ml', 1500),
(6, 3, 'Limonada', 'Natural', 1800);

-- =========================
-- Tabla: clientes
-- =========================
CREATE TABLE clientes (
    id_cliente SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    email VARCHAR(100),
    fecha_registro TIMESTAMP DEFAULT NOW()
);

INSERT INTO clientes (nombre, telefono, email) VALUES
('Cliente 1','+56911111111','cliente1@mail.com'),
('Cliente 2','+56922222222','cliente2@mail.com');

-- =========================
-- Tabla: mesas
-- =========================
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

-- =========================
-- Tabla: pedidos
-- =========================
CREATE TABLE pedidos (
    id_pedido SERIAL PRIMARY KEY,
    id_cliente INT REFERENCES clientes(id_cliente),
    id_mesa INT REFERENCES mesas(id_mesa),
    id_sucursal INT REFERENCES sucursales(id_sucursal) ON DELETE CASCADE,
    fecha TIMESTAMP DEFAULT NOW(),
    estado VARCHAR(50) DEFAULT 'Pendiente',
    total NUMERIC(10,2) NOT NULL
);

-- =========================
-- Tabla: detalle_pedido
-- =========================
CREATE TABLE detalle_pedido (
    id_detalle SERIAL PRIMARY KEY,
    id_pedido INT REFERENCES pedidos(id_pedido) ON DELETE CASCADE,
    id_producto INT REFERENCES productos(id_producto),
    cantidad INT NOT NULL,
    subtotal NUMERIC(10,2) NOT NULL
);

-- =========================
-- Tabla: sucursales
-- =========================
CREATE TABLE sucursales (
    id_sucursal SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(255),
    telefono VARCHAR(20),
    email VARCHAR(100),
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT NOW()
);

-- Sucursales de ejemplo
INSERT INTO sucursales (nombre, direccion, telefono, email) VALUES
('Sucursal Centro', 'Av. Principal 123, Centro', '+56912345678', 'centro@fudo.cl'),
('Sucursal Plaza Norte', 'Av. Norte 456, Plaza Norte', '+56987654321', 'norte@fudo.cl'),
('Sucursal Mall Sur', 'Av. Sur 789, Mall Sur', '+56956781234', 'sur@fudo.cl');

-- =========================
-- Tabla: usuarios_admin
-- =========================
CREATE TABLE usuarios_admin (
    id SERIAL PRIMARY KEY,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    contrasena TEXT NOT NULL, -- usar crypt()
    nombre_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    rol VARCHAR(20) NOT NULL DEFAULT 'admin_sucursal', -- 'admin' o 'admin_sucursal'
    id_sucursal INT REFERENCES sucursales(id_sucursal) ON DELETE SET NULL,
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

