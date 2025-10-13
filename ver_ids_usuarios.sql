-- ========================================
-- VER IDs REALES DE LOS USUARIOS
-- ========================================

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
    id;

-- Resultado esperado:
-- id  | usuario         | permisos con cocina
-- ----+-----------------+---------------------
-- 5   | usuario_centro  | {"cocina": false}
-- 6   | usuario_norte   | {"cocina": true}
-- 7 o más | test       | {"cocina": ...}
