-- ============================================================
-- Migración: Agregar columna permisos a usuarios_admin
-- Fecha: 13 de octubre de 2025
-- Descripción: Agrega sistema de permisos granulares para rol 'usuario'
-- ============================================================

-- Agregar columna permisos (JSON)
ALTER TABLE usuarios_admin 
ADD COLUMN permisos TEXT;

-- Comentar la columna
COMMENT ON COLUMN usuarios_admin.permisos IS 'JSON con permisos personalizados para rol usuario: {"pedidos":true,"mesas":true,"cocina":true,"mi_carta":true,"categorias":false,"productos":false}';

-- Actualizar usuarios existentes con rol 'usuario' con permisos por defecto (solo lectura básica)
UPDATE usuarios_admin 
SET permisos = '{"pedidos":true,"mesas":true,"cocina":true,"mi_carta":true,"categorias":false,"productos":false}'
WHERE rol = 'usuario' AND permisos IS NULL;

-- Verificación
\echo 'Migración completada. Usuarios con rol usuario:'
SELECT id, usuario, nombre_completo, rol, permisos FROM usuarios_admin WHERE rol = 'usuario';
