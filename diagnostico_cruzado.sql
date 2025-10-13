-- ========================================
-- DIAGNÓSTICO: Comportamiento extraño
-- "Si activo en usuario_test, se desactiva en usuario_centro"
-- ========================================

-- 1. Ver TODOS los usuarios con rol 'usuario'
SELECT 
    id,
    usuario,
    nombre_completo,
    rol,
    id_sucursal,
    permisos,
    permisos::json->>'cocina' as cocina_valor,
    fecha_creacion
FROM 
    usuarios_admin
WHERE 
    rol = 'usuario'
ORDER BY 
    id;

-- 2. Verificar si hay IDs duplicados (no debería haber)
SELECT 
    id,
    COUNT(*) as veces_repetido
FROM 
    usuarios_admin
GROUP BY 
    id
HAVING 
    COUNT(*) > 1;

-- 3. Ver si hay usuarios con el mismo nombre
SELECT 
    usuario,
    COUNT(*) as cantidad
FROM 
    usuarios_admin
GROUP BY 
    usuario
HAVING 
    COUNT(*) > 1;

-- 4. Ver estructura completa de usuario_centro y test
SELECT 
    id,
    usuario,
    nombre_completo,
    email,
    rol,
    id_sucursal,
    permisos,
    activo,
    fecha_creacion
FROM 
    usuarios_admin
WHERE 
    usuario IN ('usuario_centro', 'test')
    OR usuario LIKE '%test%'
ORDER BY 
    fecha_creacion;

-- 5. Verificar si el campo 'id' es realmente la PRIMARY KEY
SELECT 
    c.column_name,
    c.data_type,
    tc.constraint_type
FROM 
    information_schema.table_constraints tc
JOIN 
    information_schema.constraint_column_usage AS ccu USING (constraint_schema, constraint_name)
JOIN 
    information_schema.columns AS c ON c.table_schema = tc.constraint_schema
    AND tc.table_name = c.table_name AND ccu.column_name = c.column_name
WHERE 
    tc.table_name = 'usuarios_admin'
    AND tc.constraint_type = 'PRIMARY KEY';

-- 6. TEST CRÍTICO: Actualizar SOLO usuario_centro
UPDATE usuarios_admin
SET permisos = '{"pedidos":true,"mesas":true,"cocina":true,"mi_carta":true,"categorias":false,"productos":false}'
WHERE usuario = 'usuario_centro';

-- Ver resultado INMEDIATAMENTE
SELECT 
    id,
    usuario,
    permisos::json->>'cocina' as cocina
FROM 
    usuarios_admin
WHERE 
    usuario IN ('usuario_centro', 'test')
    OR usuario LIKE '%test%';

-- 7. Ahora actualizar SOLO el usuario test
UPDATE usuarios_admin
SET permisos = '{"pedidos":true,"mesas":true,"cocina":false,"mi_carta":true,"categorias":false,"productos":false}'
WHERE usuario LIKE '%test%';

-- Ver resultado INMEDIATAMENTE
SELECT 
    id,
    usuario,
    permisos::json->>'cocina' as cocina
FROM 
    usuarios_admin
WHERE 
    usuario IN ('usuario_centro', 'test')
    OR usuario LIKE '%test%';

-- 8. VERIFICAR: ¿Se mantienen los valores o se intercambian?
