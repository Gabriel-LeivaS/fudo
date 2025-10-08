-- ============================================================
-- FIX: Actualizar contraseña del usuario admin
-- Fecha: 8 de octubre de 2025
-- Descripción: Corrige la contraseña del super admin
-- ============================================================

-- Conectar a la base de datos
\c fudo;

-- Habilitar extensión pgcrypto si no está habilitada
CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- Actualizar contraseña del admin
UPDATE usuarios_admin 
SET contrasena = crypt('admin123', gen_salt('bf')),
    rol = 'admin',
    email = 'admin@fudo.cl',
    activo = TRUE
WHERE usuario = 'admin';

-- Verificar que se actualizó
\echo '============================================================'
\echo 'Verificación de usuario admin:'
\echo '============================================================'
SELECT id, usuario, nombre_completo, email, rol, activo, id_sucursal 
FROM usuarios_admin 
WHERE usuario = 'admin';

\echo ''
\echo '============================================================'
\echo 'Prueba de autenticación:'
\echo '============================================================'
SELECT 
    CASE 
        WHEN contrasena = crypt('admin123', contrasena) 
        THEN '✅ Contraseña CORRECTA - admin123' 
        ELSE '❌ Contraseña INCORRECTA' 
    END as resultado
FROM usuarios_admin 
WHERE usuario = 'admin';

\echo ''
\echo '============================================================'
\echo 'CREDENCIALES:'
\echo '============================================================'
\echo 'Usuario: admin'
\echo 'Contraseña: admin123'
\echo 'Rol: admin (super admin)'
\echo '============================================================'
