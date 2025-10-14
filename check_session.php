<?php
session_start();

header('Content-Type: application/json');

$response = [
    'session_active' => !empty($_SESSION),
    'session_id' => session_id(),
    'user_data' => []
];

if (!empty($_SESSION)) {
    // Mostrar datos de sesión relevantes (sin contraseñas)
    $safe_keys = ['logueado', 'id_usuario', 'usuario', 'rol', 'id_sucursal', 'nombre_completo', 'permisos'];
    
    foreach ($safe_keys as $key) {
        if (isset($_SESSION[$key])) {
            $response['user_data'][$key] = $_SESSION[$key];
        }
    }
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
