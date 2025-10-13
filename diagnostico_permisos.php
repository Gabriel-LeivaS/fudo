<?php
/**
 * SCRIPT DE DIAGNÓSTICO DE PERMISOS
 * Ejecutar desde navegador: http://localhost/fudo/diagnostico_permisos.php
 */

// Configuración de base de datos
$host = "localhost";
$port = "5432";
$dbname = "fudo_db";
$user = "postgres";
$password = "tu_password"; // CAMBIAR ESTO

echo "<html><head><style>
    body { font-family: 'Courier New', monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
    h2 { color: #4ec9b0; border-bottom: 2px solid #4ec9b0; padding-bottom: 10px; }
    h3 { color: #dcdcaa; margin-top: 20px; }
    .section { background: #252526; padding: 15px; margin: 15px 0; border-radius: 8px; border-left: 4px solid #007acc; }
    .error { color: #f48771; font-weight: bold; }
    .success { color: #4ec9b0; font-weight: bold; }
    .warning { color: #dcdcaa; font-weight: bold; }
    .code { background: #1e1e1e; padding: 10px; border-radius: 4px; border: 1px solid #3c3c3c; margin: 10px 0; }
    pre { margin: 0; color: #ce9178; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { padding: 8px; text-align: left; border: 1px solid #3c3c3c; }
    th { background: #007acc; color: white; }
    tr:nth-child(even) { background: #2d2d30; }
</style></head><body>";

echo "<h2>🔍 DIAGNÓSTICO COMPLETO DE PERMISOS - FUDO</h2>";
echo "<p>Fecha: " . date('Y-m-d H:i:s') . "</p>";

try {
    // Conectar a base de datos
    $conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
    
    if (!$conn) {
        throw new Exception("Error de conexión a PostgreSQL");
    }
    
    echo "<div class='section'>";
    echo "<h3 class='success'>✅ Conexión a base de datos exitosa</h3>";
    echo "</div>";

    // 1. VERIFICAR ESTRUCTURA DE TABLA
    echo "<div class='section'>";
    echo "<h3>📋 1. ESTRUCTURA DE TABLA usuarios_admin</h3>";
    
    $query_estructura = "
        SELECT column_name, data_type, is_nullable, column_default
        FROM information_schema.columns
        WHERE table_name = 'usuarios_admin'
        ORDER BY ordinal_position
    ";
    $result = pg_query($conn, $query_estructura);
    
    echo "<table>";
    echo "<tr><th>Columna</th><th>Tipo</th><th>Nullable</th><th>Default</th></tr>";
    while ($row = pg_fetch_assoc($result)) {
        $highlight = $row['column_name'] == 'permisos' ? " style='background:#264f78;'" : "";
        echo "<tr$highlight>";
        echo "<td>" . $row['column_name'] . "</td>";
        echo "<td>" . $row['data_type'] . "</td>";
        echo "<td>" . $row['is_nullable'] . "</td>";
        echo "<td>" . ($row['column_default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";

    // 2. VERIFICAR TODOS LOS USUARIOS CON ROL 'usuario'
    echo "<div class='section'>";
    echo "<h3>👥 2. USUARIOS CON ROL 'usuario'</h3>";
    
    $query_usuarios = "
        SELECT id, usuario, nombre_completo, rol, id_sucursal, permisos
        FROM usuarios_admin
        WHERE rol = 'usuario'
        ORDER BY id
    ";
    $result = pg_query($conn, $query_usuarios);
    
    echo "<table>";
    echo "<tr><th>ID</th><th>Usuario</th><th>Nombre</th><th>Sucursal</th><th>Permisos JSON</th></tr>";
    
    $usuarios_encontrados = [];
    while ($row = pg_fetch_assoc($result)) {
        $usuarios_encontrados[] = $row;
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td><strong>" . $row['usuario'] . "</strong></td>";
        echo "<td>" . $row['nombre_completo'] . "</td>";
        echo "<td>" . $row['id_sucursal'] . "</td>";
        echo "<td><pre>" . ($row['permisos'] ?? '<span class="error">NULL</span>') . "</pre></td>";
        echo "</tr>";
    }
    echo "</table>";
    
    if (empty($usuarios_encontrados)) {
        echo "<p class='warning'>⚠️ No se encontraron usuarios con rol 'usuario'</p>";
    }
    echo "</div>";

    // 3. ANALIZAR PERMISOS JSON DE CADA USUARIO
    echo "<div class='section'>";
    echo "<h3>🔍 3. ANÁLISIS DETALLADO DE PERMISOS</h3>";
    
    foreach ($usuarios_encontrados as $usuario) {
        echo "<div class='code'>";
        echo "<h4 style='color:#4ec9b0; margin:0 0 10px 0;'>Usuario: {$usuario['usuario']}</h4>";
        
        if (empty($usuario['permisos'])) {
            echo "<p class='error'>❌ Campo permisos está VACÍO o NULL</p>";
            echo "<p class='warning'>⚠️ PROBLEMA: Este usuario no tiene permisos configurados en la BD</p>";
        } else {
            echo "<p class='success'>✅ Campo permisos tiene contenido</p>";
            echo "<pre style='color:#ce9178;'>" . $usuario['permisos'] . "</pre>";
            
            // Intentar parsear JSON
            $permisos_array = json_decode($usuario['permisos'], true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo "<p class='error'>❌ ERROR: JSON inválido - " . json_last_error_msg() . "</p>";
            } else {
                echo "<p class='success'>✅ JSON válido</p>";
                echo "<table style='margin-top:10px;'>";
                echo "<tr><th>Sección</th><th>Valor</th><th>Estado</th></tr>";
                
                $secciones = ['pedidos', 'mesas', 'cocina', 'mi_carta', 'categorias', 'productos'];
                foreach ($secciones as $seccion) {
                    $valor = isset($permisos_array[$seccion]) ? ($permisos_array[$seccion] ? 'true' : 'false') : 'NO EXISTE';
                    $estado = isset($permisos_array[$seccion]) && $permisos_array[$seccion] === true ? 'PERMITIDO' : 'DENEGADO';
                    $color = $estado == 'PERMITIDO' ? '#4ec9b0' : '#f48771';
                    
                    echo "<tr>";
                    echo "<td>{$seccion}</td>";
                    echo "<td>{$valor}</td>";
                    echo "<td style='color:{$color}; font-weight:bold;'>{$estado}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
        echo "</div>";
    }
    echo "</div>";

    // 4. VERIFICAR SI HAY VALORES POR DEFECTO HARDCODEADOS
    echo "<div class='section'>";
    echo "<h3>⚙️ 4. BUSCAR VALORES POR DEFECTO EN CÓDIGO</h3>";
    echo "<p class='warning'>⚠️ ARCHIVOS A REVISAR MANUALMENTE:</p>";
    echo "<ul>";
    echo "<li><strong>application/controllers/Login.php</strong> (línea ~25): Verificar carga de permisos en sesión</li>";
    echo "<li><strong>application/views/admin/usuarios.php</strong> (línea ~822): Buscar permisos por defecto en JavaScript</li>";
    echo "<li><strong>application/controllers/Usuarios.php</strong> (línea ~185): Verificar procesamiento al editar</li>";
    echo "</ul>";
    
    echo "<div class='code'>";
    echo "<h4 style='color:#dcdcaa;'>🔎 Línea sospechosa encontrada (usuarios.php:822)</h4>";
    echo "<pre style='color:#f48771;'>let permisos = {pedidos: true, mesas: true, cocina: true, mi_carta: true, categorias: false, productos: false};</pre>";
    echo "<p class='warning'>⚠️ Este objeto por defecto se usa como FALLBACK si el JSON no puede parsearse</p>";
    echo "<p>Sin embargo, debería estar siendo sobreescrito por los datos reales de la BD</p>";
    echo "</div>";
    echo "</div>";

    // 5. SIMULAR CARGA DE SESIÓN
    echo "<div class='section'>";
    echo "<h3>🔐 5. SIMULACIÓN DE CARGA EN SESIÓN PHP</h3>";
    
    if (!empty($usuarios_encontrados)) {
        $primer_usuario = $usuarios_encontrados[0];
        
        echo "<p>Simulando login del usuario: <strong>{$primer_usuario['usuario']}</strong></p>";
        echo "<div class='code'>";
        echo "<pre style='color:#ce9178;'>";
        echo "// En Login.php línea ~25\n";
        echo "\$permisos = null;\n";
        echo "if(\$u->rol == 'usuario' && !empty(\$u->permisos)) {\n";
        echo "    \$permisos = json_decode(\$u->permisos, true);\n";
        echo "}\n\n";
        
        echo "// Resultado:\n";
        if (empty($primer_usuario['permisos'])) {
            echo "\$permisos = null  // ❌ PROBLEMA: Campo vacío en BD\n";
        } else {
            $permisos_decoded = json_decode($primer_usuario['permisos'], true);
            echo "\$permisos = " . print_r($permisos_decoded, true) . "\n";
        }
        echo "</pre>";
        echo "</div>";
        
        // Simular validación en navbar
        echo "<h4 style='color:#dcdcaa; margin-top:20px;'>Simulación en navbar (pedidos.php línea ~221)</h4>";
        echo "<div class='code'>";
        echo "<pre style='color:#ce9178;'>";
        echo "// Función tiene_permiso\n";
        echo "\$rol = 'usuario';\n";
        
        if (empty($primer_usuario['permisos'])) {
            echo "\$permisos = null;  // ❌ PROBLEMA\n\n";
            echo "// Validación para 'cocina':\n";
            echo "if(\$rol == 'usuario' && is_array(\$permisos)) {  // FALSE porque \$permisos es null\n";
            echo "    return isset(\$permisos['cocina']) && \$permisos['cocina'] === true;\n";
            echo "}\n";
            echo "return false;  // ❌ Debería retornar false y OCULTAR enlace\n";
        } else {
            $permisos_decoded = json_decode($primer_usuario['permisos'], true);
            echo "\$permisos = " . var_export($permisos_decoded, true) . ";\n\n";
            echo "// Validación para 'cocina':\n";
            echo "if(\$rol == 'usuario' && is_array(\$permisos)) {  // TRUE\n";
            $cocina = isset($permisos_decoded['cocina']) && $permisos_decoded['cocina'] === true;
            echo "    return isset(\$permisos['cocina']) && \$permisos['cocina'] === true;  // " . ($cocina ? 'TRUE ✅' : 'FALSE ❌') . "\n";
            echo "}\n";
            echo "// Resultado: " . ($cocina ? 'MOSTRAR enlace Cocina' : 'OCULTAR enlace Cocina') . "\n";
        }
        echo "</pre>";
        echo "</div>";
    }
    echo "</div>";

    // 6. RECOMENDACIONES
    echo "<div class='section'>";
    echo "<h3>💡 6. DIAGNÓSTICO Y RECOMENDACIONES</h3>";
    
    $problemas = [];
    $soluciones = [];
    
    foreach ($usuarios_encontrados as $u) {
        if (empty($u['permisos'])) {
            $problemas[] = "Usuario '{$u['usuario']}' tiene campo permisos NULL/vacío en BD";
            $soluciones[] = "Ejecutar UPDATE para ese usuario específico";
        } else {
            $p = json_decode($u['permisos'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $problemas[] = "Usuario '{$u['usuario']}' tiene JSON corrupto: " . json_last_error_msg();
                $soluciones[] = "Corregir JSON en BD para ese usuario";
            } else {
                if (isset($p['cocina']) && $p['cocina'] === true) {
                    $problemas[] = "Usuario '{$u['usuario']}' TIENE permiso cocina=true en BD (debería ser false)";
                    $soluciones[] = "Editar usuario desde panel admin y desmarcar 'Cocina', luego verificar que se guardó correctamente";
                }
            }
        }
    }
    
    if (empty($problemas)) {
        echo "<p class='success'>✅ No se detectaron problemas en la BD</p>";
        echo "<p class='warning'>⚠️ PERO el usuario sigue viendo 'Cocina' después de re-login</p>";
        echo "<p>Esto indica que el problema está en:</p>";
        echo "<ul>";
        echo "<li>❌ La sesión no se está destruyendo correctamente al hacer logout</li>";
        echo "<li>❌ El navegador está cacheando la sesión antigua</li>";
        echo "<li>❌ Hay múltiples sesiones activas (cookies duplicadas)</li>";
        echo "</ul>";
        
        echo "<h4 style='color:#dcdcaa;'>🔧 PASOS PARA SOLUCIONAR:</h4>";
        echo "<ol>";
        echo "<li><strong>Cerrar COMPLETAMENTE el navegador</strong> (todas las ventanas)</li>";
        echo "<li><strong>Borrar cookies</strong> del sitio (F12 → Application → Cookies)</li>";
        echo "<li><strong>Abrir navegador en modo incógnito</strong></li>";
        echo "<li><strong>Iniciar sesión nuevamente</strong> con usuario_centro</li>";
        echo "<li><strong>Verificar</strong> si ahora NO aparece 'Cocina'</li>";
        echo "</ol>";
    } else {
        echo "<p class='error'>❌ SE DETECTARON " . count($problemas) . " PROBLEMA(S):</p>";
        echo "<ol>";
        foreach ($problemas as $p) {
            echo "<li class='error'>{$p}</li>";
        }
        echo "</ol>";
        
        echo "<h4 style='color:#dcdcaa;'>🔧 SOLUCIONES:</h4>";
        echo "<ol>";
        foreach ($soluciones as $s) {
            echo "<li class='success'>{$s}</li>";
        }
        echo "</ol>";
    }
    echo "</div>";

    // 7. SQL DE CORRECCIÓN
    echo "<div class='section'>";
    echo "<h3>🛠️ 7. SCRIPTS SQL DE CORRECCIÓN</h3>";
    
    echo "<h4 style='color:#dcdcaa;'>Opción 1: Actualizar usuario específico (RECOMENDADO)</h4>";
    echo "<div class='code'><pre style='color:#ce9178;'>";
    echo "-- Reemplazar 'usuario_centro' con el nombre de usuario real\n";
    echo "UPDATE usuarios_admin\n";
    echo "SET permisos = '{\"pedidos\":true,\"mesas\":true,\"cocina\":false,\"mi_carta\":true,\"categorias\":false,\"productos\":false}'\n";
    echo "WHERE usuario = 'usuario_centro' AND rol = 'usuario';\n";
    echo "</pre></div>";
    
    echo "<h4 style='color:#dcdcaa;'>Opción 2: Resetear TODOS los usuarios con rol 'usuario'</h4>";
    echo "<div class='code'><pre style='color:#ce9178;'>";
    echo "UPDATE usuarios_admin\n";
    echo "SET permisos = '{\"pedidos\":true,\"mesas\":true,\"cocina\":false,\"mi_carta\":true,\"categorias\":false,\"productos\":false}'\n";
    echo "WHERE rol = 'usuario';\n";
    echo "</pre></div>";
    
    echo "<h4 style='color:#dcdcaa;'>Opción 3: Verificar después de actualizar</h4>";
    echo "<div class='code'><pre style='color:#ce9178;'>";
    echo "SELECT usuario, permisos\n";
    echo "FROM usuarios_admin\n";
    echo "WHERE rol = 'usuario';\n";
    echo "</pre></div>";
    echo "</div>";

    pg_close($conn);
    
} catch (Exception $e) {
    echo "<div class='section'>";
    echo "<p class='error'>❌ ERROR: " . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<div class='section' style='margin-top:30px; border-color:#4ec9b0;'>";
echo "<h3 style='color:#4ec9b0;'>📝 RESUMEN EJECUTIVO</h3>";
echo "<p>Este diagnóstico verifica:</p>";
echo "<ul>";
echo "<li>✅ Estructura de la tabla usuarios_admin</li>";
echo "<li>✅ Permisos almacenados en BD para cada usuario</li>";
echo "<li>✅ Validez del JSON de permisos</li>";
echo "<li>✅ Simulación de carga en sesión PHP</li>";
echo "<li>✅ Comportamiento esperado en navbars</li>";
echo "</ul>";
echo "<p><strong>Próximos pasos:</strong> Ejecutar los SQL de corrección y probar con navegador en modo incógnito</p>";
echo "</div>";

echo "</body></html>";
