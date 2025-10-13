-- ========================================
-- VERIFICAR TRIGGERS Y DEFAULTS EN BD
-- ========================================

-- 1. Verificar si hay TRIGGERS en la tabla usuarios_admin
SELECT 
    trigger_name,
    event_manipulation,
    event_object_table,
    action_statement,
    action_timing
FROM 
    information_schema.triggers
WHERE 
    event_object_table = 'usuarios_admin';

-- 2. Verificar columna permisos (tipo, default, constraints)
SELECT 
    column_name,
    data_type,
    column_default,
    is_nullable
FROM 
    information_schema.columns
WHERE 
    table_name = 'usuarios_admin' 
    AND column_name = 'permisos';

-- 3. Ver el último usuario creado
SELECT 
    id,
    usuario,
    rol,
    permisos,
    fecha_creacion
FROM 
    usuarios_admin
ORDER BY 
    fecha_creacion DESC
LIMIT 5;

-- 4. Verificar el usuario test que acabas de crear
SELECT 
    id,
    usuario,
    nombre_completo,
    rol,
    permisos,
    permisos::json->'cocina' as permiso_cocina,
    fecha_creacion
FROM 
    usuarios_admin
WHERE 
    usuario LIKE '%test%'
ORDER BY 
    fecha_creacion DESC;

-- 5. Ver historial de cambios (si hay tabla de auditoría)
SELECT 
    table_name
FROM 
    information_schema.tables
WHERE 
    table_schema = 'public' 
    AND (table_name LIKE '%audit%' OR table_name LIKE '%log%' OR table_name LIKE '%history%');
