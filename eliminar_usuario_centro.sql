-- ========================================
-- ELIMINAR USUARIO CENTRO
-- ========================================

-- Ver el usuario ANTES de eliminar
SELECT 
    id,
    usuario,
    nombre_completo,
    rol,
    permisos
FROM 
    usuarios_admin
WHERE 
    usuario = 'usuario_centro';

-- ELIMINAR el usuario
DELETE FROM usuarios_admin
WHERE usuario = 'usuario_centro';

-- Verificar que se eliminó
SELECT 
    id,
    usuario,
    nombre_completo,
    rol
FROM 
    usuarios_admin
WHERE 
    rol = 'usuario'
ORDER BY 
    id;

-- Mensaje de confirmación
-- Debería mostrar solo usuario_norte (y otros si existen)
-- usuario_centro ya NO debe aparecer
