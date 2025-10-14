<?php
$conn = pg_connect('host=localhost port=5432 dbname=fudo user=postgres password=1234');
if (!$conn) {
    die("Error de conexión\n");
}

echo "=== ESTRUCTURA DE TABLA usuarios_admin ===\n";
$result = pg_query($conn, "SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'usuarios_admin' ORDER BY ordinal_position");
while($row = pg_fetch_assoc($result)) {
    echo $row['column_name'] . ' - ' . $row['data_type'] . "\n";
}

echo "\n=== USUARIOS CON ROL 'usuario' ===\n";
$result = pg_query($conn, "SELECT id, usuario, rol, permisos FROM usuarios_admin WHERE rol = 'usuario'");
while($row = pg_fetch_assoc($result)) {
    echo "ID: " . $row['id'] . ", Usuario: " . $row['usuario'] . ", Rol: " . $row['rol'] . "\n";
    echo "Permisos: " . $row['permisos'] . "\n\n";
}

echo "\n=== TODOS LOS ROLES DISPONIBLES ===\n";
$result = pg_query($conn, "SELECT DISTINCT rol FROM usuarios_admin");
while($row = pg_fetch_assoc($result)) {
    echo "Rol: " . $row['rol'] . "\n";
}

pg_close($conn);
