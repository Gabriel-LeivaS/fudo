<?php
// Script de diagnóstico simple
session_start();

// Conexión a base de datos
$db_host = 'localhost';
$db_name = 'fudo';
$db_user = 'postgres';
$db_pass = 'root';

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>🔍 Diagnóstico FUDO</title>
<style>
body{font-family:'Segoe UI',sans-serif;padding:20px;background:#f5f5f5;margin:0;}
.section{background:white;padding:20px;margin:20px auto;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);max-width:900px;}
table{background:white;border-collapse:collapse;width:100%;margin:10px 0;}
th,td{border:1px solid #ddd;padding:12px;text-align:left;}
th{background:#4CAF50;color:white;font-weight:600;}
.success{color:green;font-weight:bold;}
.error{color:red;font-weight:bold;}
.warning{color:orange;font-weight:bold;}
h1{color:#333;text-align:center;}
h2{color:#666;border-bottom:2px solid #4CAF50;padding-bottom:10px;margin-top:0;}
.btn{display:inline-block;padding:10px 20px;margin:5px;text-decoration:none;border-radius:5px;color:white;font-weight:bold;}
.btn-green{background:#4CAF50;}
.btn-blue{background:#2196F3;}
.btn-red{background:#f44336;}
pre{background:#f4f4f4;padding:10px;border-radius:5px;overflow-x:auto;}
</style>
</head>
<body>

<h1>🔍 Diagnóstico Completo del Sistema FUDO</h1>

<!-- ===== 1. VERIFICAR SESIÓN ===== -->
<div class="section">
<h2>1️⃣ Estado de Sesión PHP</h2>
<?php
if(isset($_SESSION) && !empty($_SESSION)) {
    echo "<p class='success'>✅ Sesión activa</p>";
    echo "<table>";
    echo "<tr><th>Campo</th><th>Valor</th></tr>";
    foreach($_SESSION as $key => $value) {
        $display_value = is_array($value) ? json_encode($value) : ($value ?? '<span class="error">NULL</span>');
        echo "<tr><td><strong>".htmlspecialchars($key)."</strong></td><td>".htmlspecialchars($display_value)."</td></tr>";
    }
    echo "</table>";
    
    $rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'NO DEFINIDO';
    if($rol == 'admin') {
        echo "<p class='success'>✅ ROL = 'admin' - Los enlaces DEBERÍAN aparecer</p>";
    } else {
        echo "<p class='warning'>⚠️ ROL = '$rol' - No es super admin. Los enlaces NO aparecerán.</p>";
    }
} else {
    echo "<p class='error'>❌ No hay sesión activa (sesión vacía)</p>";
    echo "<p>Debes <a href='http://localhost/fudo/index.php/login' class='btn btn-blue'>🔐 Iniciar Sesión</a></p>";
}
?>
</div>

<!-- ===== 2. VERIFICAR BASE DE DATOS ===== -->
<div class="section">
<h2>2️⃣ Datos en Base de Datos PostgreSQL</h2>
<?php
try {
    $conn = pg_connect("host=$db_host dbname=$db_name user=$db_user password=$db_pass");
    
    if($conn) {
        echo "<p class='success'>✅ Conexión a base de datos exitosa</p>";
        
        $query = "SELECT id, usuario, rol, id_sucursal, activo FROM usuarios_admin WHERE usuario = 'admin'";
        $result = pg_query($conn, $query);
        
        if($result && pg_num_rows($result) > 0) {
            $admin = pg_fetch_object($result);
            echo "<p class='success'>✅ Usuario 'admin' encontrado en BD</p>";
            echo "<table>";
            echo "<tr><th>Campo</th><th>Valor</th></tr>";
            echo "<tr><td><strong>ID</strong></td><td>{$admin->id}</td></tr>";
            echo "<tr><td><strong>Usuario</strong></td><td>{$admin->usuario}</td></tr>";
            echo "<tr><td><strong>Rol</strong></td><td><strong style='color:".($admin->rol == 'admin' ? 'green' : 'red')."'>{$admin->rol}</strong></td></tr>";
            echo "<tr><td><strong>ID Sucursal</strong></td><td>".($admin->id_sucursal ?? 'NULL')."</td></tr>";
            echo "<tr><td><strong>Activo</strong></td><td>".($admin->activo == 't' ? 'Sí' : 'No')."</td></tr>";
            echo "</table>";
            
            if($admin->rol != 'admin') {
                echo "<p class='error'>❌ ERROR CRÍTICO: El rol en BD NO es 'admin', es: <strong>{$admin->rol}</strong></p>";
                echo "<p><strong>SOLUCIÓN:</strong> Ejecuta este comando SQL en PostgreSQL:</p>";
                echo "<pre>UPDATE usuarios_admin SET rol = 'admin' WHERE usuario = 'admin';</pre>";
            } else {
                echo "<p class='success'>✅ El rol en BD es correcto: 'admin'</p>";
            }
        } else {
            echo "<p class='error'>❌ Usuario 'admin' NO existe en la base de datos</p>";
        }
        
        pg_close($conn);
    } else {
        echo "<p class='error'>❌ No se pudo conectar a la base de datos</p>";
    }
} catch(Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
</div>

<!-- ===== 3. VERIFICAR ARCHIVOS ===== -->
<div class="section">
<h2>3️⃣ Verificación de Archivos de Vista</h2>
<?php
$archivos = [
    'pedidos.php' => 'application/views/admin/pedidos.php',
    'categorias.php' => 'application/views/admin/categorias.php',
    'productos.php' => 'application/views/admin/productos.php'
];

foreach($archivos as $nombre => $ruta) {
    if(file_exists($ruta)) {
        $contenido = file_get_contents($ruta);
        $tiene_usuarios = strpos($contenido, 'Usuarios') !== false;
        $tiene_sucursales = strpos($contenido, 'Sucursales') !== false;
        $tiene_if_rol = strpos($contenido, "userdata('rol')") !== false;
        
        echo "<h3>📄 $nombre</h3>";
        echo "<ul>";
        echo "<li>".($tiene_usuarios ? "<span class='success'>✅</span>" : "<span class='error'>❌</span>")." Contiene 'Usuarios'</li>";
        echo "<li>".($tiene_sucursales ? "<span class='success'>✅</span>" : "<span class='error'>❌</span>")." Contiene 'Sucursales'</li>";
        echo "<li>".($tiene_if_rol ? "<span class='success'>✅</span>" : "<span class='error'>❌</span>")." Contiene verificación de rol</li>";
        echo "</ul>";
    } else {
        echo "<p class='error'>❌ No existe: $ruta</p>";
    }
}
?>
</div>

<!-- ===== 4. DIAGNÓSTICO Y SOLUCIÓN ===== -->
<div class="section">
<h2>4️⃣ Diagnóstico y Solución</h2>
<?php
$tiene_sesion = isset($_SESSION) && !empty($_SESSION);
$rol_sesion = isset($_SESSION['rol']) ? $_SESSION['rol'] : null;

echo "<h3>🔍 Análisis:</h3>";

if(!$tiene_sesion) {
    echo "<p class='error'>❌ <strong>PROBLEMA:</strong> No hay sesión activa</p>";
    echo "<p><strong>SOLUCIÓN:</strong></p>";
    echo "<ol>";
    echo "<li>Inicia sesión con admin/admin123</li>";
    echo "</ol>";
    echo "<a href='http://localhost/fudo/index.php/login' class='btn btn-blue'>🔐 Ir a Login</a>";
} elseif($rol_sesion != 'admin') {
    echo "<p class='error'>❌ <strong>PROBLEMA:</strong> El rol en sesión es '$rol_sesion', no 'admin'</p>";
    echo "<p><strong>SOLUCIÓN:</strong></p>";
    echo "<ol>";
    echo "<li>Cierra sesión</li>";
    echo "<li>Vuelve a iniciar sesión con admin/admin123</li>";
    echo "<li>La sesión se actualizará con el rol correcto de la base de datos</li>";
    echo "</ol>";
    echo "<a href='http://localhost/fudo/index.php/login/salir' class='btn btn-red'>🚪 Cerrar Sesión</a>";
    echo "<a href='http://localhost/fudo/index.php/login' class='btn btn-blue'>🔐 Ir a Login</a>";
} else {
    echo "<p class='success'>✅ <strong>TODO ESTÁ CORRECTO:</strong> Sesión activa con rol 'admin'</p>";
    echo "<p>Si los enlaces no aparecen, prueba:</p>";
    echo "<ol>";
    echo "<li>Refresca la página con <strong>Ctrl + Shift + R</strong> (limpia caché)</li>";
    echo "<li>O cierra todas las pestañas y abre de nuevo</li>";
    echo "</ol>";
    echo "<a href='http://localhost/fudo/index.php/admin' class='btn btn-green'>📦 Ir al Admin</a>";
}
?>
</div>

<div style="text-align:center;padding:20px;">
<a href="http://localhost/fudo/index.php/admin" class="btn btn-green">⬅️ Volver al Admin</a>
<a href="http://localhost/fudo/index.php/login" class="btn btn-blue">🔐 Ir a Login</a>
</div>

</body>
</html>
