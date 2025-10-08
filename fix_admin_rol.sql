-- ============================================================
-- FIX: Corregir rol del usuario admin
-- ============================================================

\c fudo;

-- Actualizar el rol del usuario admin a 'admin' (super admin)
UPDATE usuarios_admin 
SET rol = 'admin', 
    email = 'admin@fudo.cl'
WHERE usuario = 'admin';

-- Verificar el cambio
SELECT id, usuario, nombre_completo, rol, email, id_sucursal 
FROM usuarios_admin 
WHERE usuario = 'admin';

\echo ''
\echo '============================================================'
\echo '✅ ROL CORREGIDO: El usuario admin ahora tiene rol = admin'
\echo '============================================================'
\echo ''
\echo 'PRÓXIMO PASO:'
\echo '1. Cierra sesión en http://localhost/fudo/index.php/login/salir'
\echo '2. Vuelve a iniciar sesión con admin/admin123'
\echo '3. Los enlaces 👥 Usuarios y 🏢 Sucursales aparecerán'
\echo '============================================================'
