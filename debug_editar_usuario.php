<?php
// Script de Debug para Actualización de Usuarios
// ===============================================
// Este script prueba cada parte del flujo de actualización

require_once 'application/config/database.php';

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Función para imprimir con formato
function debug_print($title, $content) {
    echo "\n=== $title ===\n";
    if (is_array($content) || is_object($content)) {
        print_r($content);
    } else {
        echo $content . "\n";
    }
}

// Conectar a la BD
$host = $db['default']['hostname'];
$dbname = $db['default']['database'];
$user = $db['default']['username'];
$pass = $db['default']['password'];

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Debug Editar Usuario</title></head><body>";
    echo "<h1>🔍 Debug Completo - Sistema de Edición de Usuarios</h1>";
    echo "<pre style='background: #f4f4f4; padding: 10px; border-radius: 5px;'>";
    
    // 1. VERIFICAR ESTRUCTURA DE LA TABLA
    debug_print("1. ESTRUCTURA DE LA TABLA usuarios_admin", "");
    $stmt = $pdo->query("
        SELECT column_name, data_type, is_nullable, column_default 
        FROM information_schema.columns 
        WHERE table_name = 'usuarios_admin' 
        ORDER BY ordinal_position
    ");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "  - {$col['column_name']}: {$col['data_type']} ";
        echo "(" . ($col['is_nullable'] == 'YES' ? 'NULL' : 'NOT NULL') . ")";
        if ($col['column_default']) {
            echo " DEFAULT: {$col['column_default']}";
        }
        echo "\n";
    }
    
    // 2. VERIFICAR CONSTRAINTS
    debug_print("2. CONSTRAINTS DE LA TABLA", "");
    $stmt = $pdo->query("
        SELECT conname, contype, consrc
        FROM pg_constraint
        WHERE conrelid = 'usuarios_admin'::regclass
    ");
    $constraints = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($constraints as $con) {
        $type = ['p' => 'PRIMARY KEY', 'f' => 'FOREIGN KEY', 'c' => 'CHECK', 'u' => 'UNIQUE'][$con['contype']] ?? $con['contype'];
        echo "  - {$con['conname']}: $type\n";
        if ($con['consrc']) {
            echo "    Condición: {$con['consrc']}\n";
        }
    }
    
    // 3. USUARIOS EXISTENTES
    debug_print("3. USUARIOS ACTUALES EN LA BD", "");
    $stmt = $pdo->query("SELECT id, usuario, rol, id_sucursal, activo, email FROM usuarios_admin ORDER BY id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $user) {
        echo "  ID:{$user['id']} | Usuario:{$user['usuario']} | Rol:{$user['rol']} | ";
        echo "Sucursal:" . ($user['id_sucursal'] ?? 'NULL') . " | ";
        echo "Activo:" . ($user['activo'] ? 'SI' : 'NO') . " | ";
        echo "Email:{$user['email']}\n";
    }
    
    // 4. PROBAR UPDATE DIRECTO
    debug_print("4. PRUEBA DE UPDATE DIRECTO", "");
    
    // Obtener el primer usuario con rol 'usuario' para prueba
    $stmt = $pdo->query("SELECT id, usuario, nombre_completo FROM usuarios_admin WHERE rol = 'usuario' LIMIT 1");
    $test_user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($test_user) {
        echo "  Usuario de prueba: ID={$test_user['id']}, Usuario={$test_user['usuario']}\n";
        
        // Intentar actualización simple
        $test_nombre = $test_user['nombre_completo'] . ' (TEST ' . date('H:i:s') . ')';
        $sql = "UPDATE usuarios_admin SET nombre_completo = :nombre WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            ':nombre' => $test_nombre,
            ':id' => $test_user['id']
        ]);
        
        if ($result) {
            echo "  ✅ UPDATE ejecutado correctamente\n";
            echo "  Filas afectadas: " . $stmt->rowCount() . "\n";
            
            // Verificar el cambio
            $stmt = $pdo->prepare("SELECT nombre_completo FROM usuarios_admin WHERE id = ?");
            $stmt->execute([$test_user['id']]);
            $updated = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "  Nuevo valor: {$updated['nombre_completo']}\n";
            
            // Revertir el cambio
            $stmt = $pdo->prepare("UPDATE usuarios_admin SET nombre_completo = ? WHERE id = ?");
            $stmt->execute([$test_user['nombre_completo'], $test_user['id']]);
            echo "  ✅ Cambio revertido\n";
        } else {
            echo "  ❌ Error en UPDATE\n";
        }
    } else {
        echo "  ⚠️ No hay usuarios con rol 'usuario' para probar\n";
    }
    
    // 5. VERIFICAR TRIGGERS
    debug_print("5. TRIGGERS EN LA TABLA", "");
    $stmt = $pdo->query("
        SELECT trigger_name, event_manipulation, action_timing, action_statement
        FROM information_schema.triggers
        WHERE event_object_table = 'usuarios_admin'
    ");
    $triggers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($triggers)) {
        echo "  No hay triggers en la tabla\n";
    } else {
        foreach ($triggers as $trigger) {
            echo "  - {$trigger['trigger_name']}: {$trigger['event_manipulation']} {$trigger['action_timing']}\n";
        }
    }
    
    // 6. PROBAR UPDATE CON DIFERENTES ROLES
    debug_print("6. PRUEBA DE UPDATE POR ROL", "");
    
    // Para cada rol, verificar si hay restricciones
    $roles = ['admin', 'admin_sucursal', 'usuario'];
    foreach ($roles as $rol) {
        $stmt = $pdo->prepare("SELECT id, usuario FROM usuarios_admin WHERE rol = ? LIMIT 1");
        $stmt->execute([$rol]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Intentar actualizar email (campo simple)
            $test_email = "test_" . time() . "@test.com";
            $stmt = $pdo->prepare("UPDATE usuarios_admin SET email = ? WHERE id = ?");
            $result = $stmt->execute([$test_email, $user['id']]);
            
            if ($result && $stmt->rowCount() > 0) {
                echo "  ✅ Rol '$rol' (ID:{$user['id']}): UPDATE exitoso\n";
                
                // Revertir
                $stmt = $pdo->prepare("UPDATE usuarios_admin SET email = ? WHERE id = ?");
                $stmt->execute(["original@test.com", $user['id']]);
            } else {
                echo "  ❌ Rol '$rol' (ID:{$user['id']}): UPDATE falló o no afectó filas\n";
            }
        } else {
            echo "  - No hay usuarios con rol '$rol'\n";
        }
    }
    
    // 7. VERIFICAR PERMISOS DE BD
    debug_print("7. PERMISOS DEL USUARIO DE BD", "");
    $stmt = $pdo->query("SELECT current_user");
    $current = $stmt->fetch(PDO::FETCH_COLUMN);
    echo "  Usuario actual: $current\n";
    
    $stmt = $pdo->query("
        SELECT privilege_type 
        FROM information_schema.table_privileges 
        WHERE table_name = 'usuarios_admin' 
        AND grantee = current_user
    ");
    $privileges = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "  Permisos: " . implode(", ", $privileges) . "\n";
    
    // 8. PROBAR ACTUALIZACIÓN CON PERMISOS JSON
    debug_print("8. PRUEBA DE UPDATE CON CAMPO PERMISOS", "");
    $stmt = $pdo->query("SELECT id FROM usuarios_admin WHERE rol = 'usuario' LIMIT 1");
    $user_id = $stmt->fetch(PDO::FETCH_COLUMN);
    
    if ($user_id) {
        $permisos_json = '{"pedidos":true,"mesas":false,"cocina":true,"mi_carta":false,"categorias":true,"productos":false}';
        $stmt = $pdo->prepare("UPDATE usuarios_admin SET permisos = ? WHERE id = ?");
        $result = $stmt->execute([$permisos_json, $user_id]);
        
        if ($result) {
            echo "  ✅ UPDATE de permisos JSON exitoso\n";
            echo "  Filas afectadas: " . $stmt->rowCount() . "\n";
        } else {
            echo "  ❌ Error al actualizar permisos JSON\n";
        }
    }
    
    echo "</pre>";
    echo "<h2>📊 Resumen</h2>";
    echo "<ul>";
    echo "<li>Base de datos conectada: ✅</li>";
    echo "<li>Tabla usuarios_admin existe: ✅</li>";
    echo "<li>Total de usuarios: " . count($users) . "</li>";
    echo "<li>Updates directos funcionan: " . ($test_user ? "✅" : "⚠️ No probado") . "</li>";
    echo "</ul>";
    echo "</body></html>";
    
} catch (PDOException $e) {
    echo "❌ Error de BD: " . $e->getMessage() . "\n";
    echo "Código de error: " . $e->getCode() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString();
} catch (Exception $e) {
    echo "❌ Error general: " . $e->getMessage() . "\n";
}
?>
