-- ========================================
-- TEST DIRECTO: Actualizar permisos
-- ========================================

-- PASO 1: Ver estado ANTES del cambio
SELECT 
    id,
    usuario,
    permisos,
    permisos::json->'cocina' as cocina_actual
FROM 
    usuarios_admin
WHERE 
    usuario = 'usuario_centro' OR usuario LIKE '%test%';

-- PASO 2: Intentar UPDATE directo (cambiar cocina a false)
UPDATE usuarios_admin
SET permisos = '{"pedidos":true,"mesas":true,"cocina":false,"mi_carta":true,"categorias":false,"productos":false}'
WHERE usuario = 'usuario_centro';

-- PASO 3: Ver estado DESPUÉS del UPDATE
SELECT 
    id,
    usuario,
    permisos,
    permisos::json->'cocina' as cocina_despues
FROM 
    usuarios_admin
WHERE 
    usuario = 'usuario_centro';

-- PASO 4: Si hay usuario test, hacer lo mismo
UPDATE usuarios_admin
SET permisos = '{"pedidos":true,"mesas":true,"cocina":false,"mi_carta":true,"categorias":false,"productos":false}'
WHERE usuario LIKE '%test%';

SELECT 
    id,
    usuario,
    permisos,
    permisos::json->'cocina' as cocina_test
FROM 
    usuarios_admin
WHERE 
    usuario LIKE '%test%';

-- PASO 5: Ver TODA la tabla para detectar anomalías
SELECT 
    id,
    usuario,
    rol,
    LENGTH(permisos) as longitud_json,
    permisos,
    permisos::json->'cocina' as cocina
FROM 
    usuarios_admin
WHERE 
    rol = 'usuario'
ORDER BY 
    id;
