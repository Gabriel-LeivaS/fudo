-- ========================================
-- ELIMINAR USUARIOS DE PRUEBA
-- ========================================

-- Ver todos los usuarios con rol 'usuario' ANTES de eliminar
SELECT 
    id,
    usuario,
    nombre_completo,
    rol,
    id_sucursal,
    permisos,
    fecha_creacion
FROM 
    usuarios_admin
WHERE 
    rol = 'usuario'
ORDER BY 
    fecha_creacion;

-- ELIMINAR usuario_centro
DELETE FROM usuarios_admin
WHERE usuario = 'usuario_centro';

-- ELIMINAR usuarios test (cualquiera que contenga 'test')
DELETE FROM usuarios_admin
WHERE usuario LIKE '%test%';

-- Verificar qué usuarios quedan
SELECT 
    id,
    usuario,
    nombre_completo,
    rol,
    id_sucursal
FROM 
    usuarios_admin
WHERE 
    rol = 'usuario'
ORDER BY 
    id;

-- Resultado esperado:
-- Solo debería quedar usuario_norte (si no lo has eliminado)
