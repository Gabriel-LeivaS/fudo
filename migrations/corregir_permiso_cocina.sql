-- ============================================================
-- Script de Corrección: Quitar permiso Cocina de usuarios
-- Fecha: 13 de octubre de 2025
-- Problema: Usuarios tienen cocina=true por defecto desde migración
-- ============================================================

-- 1. VER ESTADO ACTUAL de todos los usuarios con rol 'usuario'
SELECT 
    id,
    usuario,
    nombre_completo,
    rol,
    permisos
FROM usuarios_admin
WHERE rol = 'usuario'
ORDER BY usuario;

-- 2. ACTUALIZAR usuario_centro específicamente (REEMPLAZAR 'usuario_centro' con el nombre real)
UPDATE usuarios_admin
SET permisos = '{"pedidos":true,"mesas":true,"cocina":false,"mi_carta":true,"categorias":false,"productos":false}'
WHERE usuario = 'usuario_centro' AND rol = 'usuario';

-- 3. VERIFICAR que se actualizó correctamente
SELECT 
    usuario,
    permisos,
    permisos::json->'cocina' as permiso_cocina
FROM usuarios_admin
WHERE usuario = 'usuario_centro';

-- ============================================================
-- EXPLICACIÓN:
-- ============================================================
-- El problema era que la migración inicial (agregar_permisos_usuarios.sql)
-- estableció cocina=true por defecto para TODOS los usuarios con rol 'usuario'.
--
-- Aunque edites desde el panel admin y desmarques el checkbox,
-- SI NO SE GUARDÓ CORRECTAMENTE EN LA BD, seguirá apareciendo.
--
-- POSIBLES CAUSAS:
-- 1. El UPDATE desde Usuarios.php no se ejecutó correctamente
-- 2. Hay un problema con el formato JSON al guardar
-- 3. La sesión no se destruyó correctamente al logout
-- ============================================================

-- 4. OPCIÓN ALTERNATIVA: Actualizar TODOS los usuarios (quitar cocina a todos)
-- Descomentar si quieres aplicar a todos:
-- UPDATE usuarios_admin
-- SET permisos = '{"pedidos":true,"mesas":true,"cocina":false,"mi_carta":true,"categorias":false,"productos":false}'
-- WHERE rol = 'usuario';

-- 5. VERIFICAR TODOS después de actualizar
-- SELECT usuario, permisos FROM usuarios_admin WHERE rol = 'usuario';
