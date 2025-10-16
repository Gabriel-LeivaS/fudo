<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Pedido_model');
        $this->load->model('Categoria_model');
        $this->load->model('Producto_model');
        $this->load->model('Sucursal_model');
        $this->load->model('Usuario_model');
        $this->load->library('session');
        $this->load->helper('url');

        if(!$this->session->userdata('logueado')) {
            redirect('login');
        }

        // Obtener datos del usuario en sesión
        $this->rol = $this->session->userdata('rol');
        $this->id_sucursal = $this->session->userdata('id_sucursal');
        $this->permisos = $this->session->userdata('permisos');
        
        // Verificar si la sucursal sigue activa (solo para usuarios no admin)
        $this->verificar_sucursal_activa();
    }
    
    /**
     * Verificar si el usuario tiene permiso para acceder a una sección
     */
    private function tiene_permiso($seccion) {
        // Super admin: Solo acceso a secciones administrativas
        if($this->rol == 'admin') {
            return in_array($seccion, ['categorias', 'productos', 'usuarios', 'sucursales']);
        }
        
        // Pedidos: Solo admin_sucursal y usuarios con permiso
        if($seccion == 'pedidos') {
            return $this->rol == 'admin_sucursal' || ($this->rol == 'usuario' && is_array($this->permisos) && isset($this->permisos['pedidos']) && $this->permisos['pedidos'] === true);
        }
        
        // Admin sucursal: acceso completo
        if($this->rol == 'admin_sucursal') {
            return true;
        }
        
        // Usuario: verificar permisos específicos
        if($this->rol == 'usuario') {
            if(is_array($this->permisos) && isset($this->permisos[$seccion])) {
                return $this->permisos[$seccion] === true;
            }
            return false;
        }
        
        return false;
    }

    public function index() {
        // Si es super admin, mostrar dashboard general
        if($this->rol == 'admin') {
            redirect('admin/categorias');
            return;
        }

        // Permitir acceso a admin_sucursal y usuario con permiso
        if($this->rol == 'admin_sucursal' || ($this->rol == 'usuario' && $this->tiene_permiso('pedidos'))) {
            // Mostrar pedidos de su sucursal
            $id_sucursal = $this->id_sucursal;
        } else {
            show_error('No tienes permisos para acceder a esta sección', 403);
            return;
        }
        
        // Mostrar pedidos de su sucursal
        $data['pedidos'] = $this->Pedido_model->obtener_pedidos_pendientes($id_sucursal);
        $this->load->view('admin/pedidos', $data);
    }

    public function obtener_pedidos_ajax() {
        // Endpoint AJAX para actualización automática de pedidos
        header('Content-Type: application/json');
        
        // Solo admin_sucursal puede acceder
        if($this->rol != 'admin_sucursal') {
            echo json_encode(['error' => 'No autorizado']);
            return;
        }
        
        $pedidos = $this->Pedido_model->obtener_pedidos_pendientes($this->id_sucursal);
        echo json_encode(['pedidos' => $pedidos]);
    }

    public function detalle_pedido_json($id_pedido) {
        header('Content-Type: application/json');
        
        // Obtener información del pedido
        $pedido = $this->db->get_where('pedidos', ['id_pedido' => $id_pedido])->row();
        
        if(!$pedido) {
            echo json_encode(['error' => 'Pedido no encontrado']);
            return;
        }
        
        // Obtener detalle de productos
        $detalle = $this->Pedido_model->obtener_detalle_pedido($id_pedido);
        
        $response = [
            'pedido' => $pedido,
            'detalle' => $detalle
        ];
        
        echo json_encode($response);
    }

    public function detalle($id_pedido) {
        $data['detalle'] = $this->Pedido_model->obtener_detalle_pedido($id_pedido);
        $this->load->view('admin/detalle_pedido', $data);
    }

    public function actualizar_estado() {
        // Validar permisos de escritura
        if(!$this->tiene_permiso('pedidos')) {
            echo json_encode(['error' => 'No tiene permisos para modificar pedidos']);
            return;
        }

        $data = json_decode($this->input->raw_input_stream, TRUE);
        if(!isset($data['id_pedido']) || !isset($data['estado'])){
            echo json_encode(['error'=>'Datos incompletos']);
            return;
        }
        $ok = $this->Pedido_model->actualizar_estado($data['id_pedido'],$data['estado']);
        echo json_encode(['ok'=>$ok]);
    }

    // ============================================
    // GESTIÓN DE CATEGORÍAS
    // ============================================

    public function categorias() {
        // Verificar permisos
        if(!$this->tiene_permiso('categorias')) {
            show_error('No tienes permisos para acceder a esta sección', 403);
            return;
        }
        
        // Filtrar por sucursal si es admin_sucursal o usuario
        $id_sucursal = ($this->rol == 'admin_sucursal' || $this->rol == 'usuario') ? $this->id_sucursal : null;
        $data['categorias'] = $this->Categoria_model->obtener_todas($id_sucursal);
        
        // Pasar sucursales solo si es super admin
        if($this->rol == 'admin') {
            $data['sucursales'] = $this->Sucursal_model->obtener_activas();
        }
        
        $this->load->view('admin/categorias', $data);
    }

    public function categoria_crear() {
        // Validar permisos de escritura
        if(!$this->tiene_permiso('categorias')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No tiene permisos para crear categorías']);
            return;
        }

        header('Content-Type: application/json');
        $nombre = trim($this->input->post('nombre'));
        $id_sucursal_form = $this->input->post('id_sucursal');
        
        if(empty($nombre)) {
            echo json_encode(['success' => false, 'message' => 'El nombre es obligatorio']);
            return;
        }

        // Determinar id_sucursal según rol
        if($this->rol == 'admin_sucursal') {
            // Admin sucursal: usar su sucursal
            $id_sucursal_final = $this->id_sucursal;
        } else {
            // Super admin: debe seleccionar sucursal
            if(empty($id_sucursal_form)) {
                echo json_encode(['success' => false, 'message' => 'Debe seleccionar una sucursal']);
                return;
            }
            $id_sucursal_final = $id_sucursal_form;
        }

        $datos = [
            'nombre' => $nombre,
            'id_sucursal' => $id_sucursal_final,
            'estado' => true
        ];

        if($this->Categoria_model->crear($datos)) {
            echo json_encode(['success' => true, 'message' => 'Categoría creada exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al crear la categoría']);
        }
    }

    public function categoria_editar() {
        // Validar permisos de escritura
        if(!$this->tiene_permiso('categorias')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No tiene permisos para editar categorías']);
            return;
        }

        header('Content-Type: application/json');
        $id = $this->input->post('id_categoria');
        $nombre = trim($this->input->post('nombre'));
        
        if(empty($id) || empty($nombre)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        // SEGURIDAD: Verificar que la categoría pertenece a la sucursal del usuario
        if($this->rol == 'admin_sucursal') {
            $categoria = $this->Categoria_model->obtener_por_id($id);
            if(!$categoria || $categoria->id_sucursal != $this->id_sucursal) {
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para editar esta categoría']);
                return;
            }
        }

        $datos = ['nombre' => $nombre];

        if($this->Categoria_model->actualizar($id, $datos)) {
            echo json_encode(['success' => true, 'message' => 'Categoría actualizada exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar la categoría']);
        }
    }

    public function categoria_eliminar() {
        // Validar permisos de escritura
        if(!$this->tiene_permiso('categorias')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No tiene permisos para eliminar categorías']);
            return;
        }

        header('Content-Type: application/json');
        $id = $this->input->post('id_categoria');
        
        if(empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID de categoría no proporcionado']);
            return;
        }

        // SEGURIDAD: Verificar que la categoría pertenece a la sucursal del usuario
        if($this->rol == 'admin_sucursal') {
            $categoria = $this->Categoria_model->obtener_por_id($id);
            if(!$categoria || $categoria->id_sucursal != $this->id_sucursal) {
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para eliminar esta categoría']);
                return;
            }
        }

        // Verificar si tiene productos asociados
        if($this->Categoria_model->tiene_productos($id)) {
            echo json_encode(['success' => false, 'message' => 'No se puede eliminar: la categoría tiene productos asociados']);
            return;
        }

        if($this->Categoria_model->eliminar($id)) {
            echo json_encode(['success' => true, 'message' => 'Categoría eliminada exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar la categoría']);
        }
    }

    public function categoria_toggle_estado() {
        // Validar permisos de escritura
        if(!$this->tiene_permiso('categorias')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No tiene permisos para modificar categorías']);
            return;
        }

        header('Content-Type: application/json');
        $id = $this->input->post('id_categoria');
        $estado_post = $this->input->post('estado');
        
        // Convertir correctamente el estado string a booleano
        $estado = ($estado_post === 'true' || $estado_post === true || $estado_post === '1' || $estado_post === 1);
        
        if(empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID de categoría no proporcionado']);
            return;
        }

        // SEGURIDAD: Verificar que la categoría pertenece a la sucursal del usuario
        if($this->rol == 'admin_sucursal') {
            $categoria = $this->Categoria_model->obtener_por_id($id);
            if(!$categoria || $categoria->id_sucursal != $this->id_sucursal) {
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para modificar esta categoría']);
                return;
            }
        }

        if($this->Categoria_model->cambiar_estado($id, $estado)) {
            $mensaje = $estado ? 'Categoría activada correctamente' : 'Categoría desactivada correctamente';
            echo json_encode(['success' => true, 'message' => $mensaje]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado']);
        }
    }

    // ============================================
    // GESTIÓN DE PRODUCTOS
    // ============================================

    public function productos() {
        // Verificar permisos
        if(!$this->tiene_permiso('productos')) {
            show_error('No tienes permisos para acceder a esta sección', 403);
            return;
        }
        
        // Filtrar por sucursal si es admin_sucursal o usuario
        $id_sucursal = ($this->rol == 'admin_sucursal' || $this->rol == 'usuario') ? $this->id_sucursal : null;
        $data['productos'] = $this->Producto_model->obtener_todos($id_sucursal);
        $data['categorias'] = $this->Categoria_model->obtener_todas($id_sucursal);
        
        // Pasar sucursales solo si es super admin
        if($this->rol == 'admin') {
            $data['sucursales'] = $this->Sucursal_model->obtener_activas();
        }
        
        $this->load->view('admin/productos', $data);
    }

    public function producto_crear() {
        // Validar permisos de escritura
        if(!$this->tiene_permiso('productos')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No tiene permisos para crear productos']);
            return;
        }

        header('Content-Type: application/json');
        $nombre = trim($this->input->post('nombre'));
        $descripcion = trim($this->input->post('descripcion'));
        $precio = $this->input->post('precio');
        $id_categoria = $this->input->post('id_categoria');
        $id_sucursal_form = $this->input->post('id_sucursal');
        $stock = $this->input->post('stock') ?? 0;
        
        if(empty($nombre) || empty($precio) || empty($id_categoria)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos (nombre, precio y categoría son obligatorios)']);
            return;
        }

        if(!is_numeric($precio) || $precio < 0) {
            echo json_encode(['success' => false, 'message' => 'El precio debe ser un número válido']);
            return;
        }

        if(!is_numeric($stock) || $stock < 0) {
            echo json_encode(['success' => false, 'message' => 'El stock debe ser un número válido']);
            return;
        }

        // Determinar id_sucursal según rol
        if($this->rol == 'admin_sucursal') {
            // Admin sucursal: usar su sucursal
            $id_sucursal_final = $this->id_sucursal;
        } else {
            // Super admin: debe seleccionar sucursal
            if(empty($id_sucursal_form)) {
                echo json_encode(['success' => false, 'message' => 'Debe seleccionar una sucursal']);
                return;
            }
            $id_sucursal_final = $id_sucursal_form;
        }

        $datos = [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => $precio,
            'id_categoria' => $id_categoria,
            'id_sucursal' => $id_sucursal_final,
            'disponible' => true,
            'stock' => (int)$stock
        ];

        if($this->Producto_model->crear($datos)) {
            echo json_encode(['success' => true, 'message' => 'Producto creado exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al crear el producto']);
        }
    }

    public function producto_editar() {
        // Validar permisos de escritura
        if(!$this->tiene_permiso('productos')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No tiene permisos para editar productos']);
            return;
        }

        header('Content-Type: application/json');
        $id = $this->input->post('id_producto');
        $nombre = trim($this->input->post('nombre'));
        $descripcion = trim($this->input->post('descripcion'));
        $precio = $this->input->post('precio');
        $id_categoria = $this->input->post('id_categoria');
        $stock = $this->input->post('stock') ?? 0;
        
        if(empty($id) || empty($nombre) || empty($precio) || empty($id_categoria)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        if(!is_numeric($precio) || $precio < 0) {
            echo json_encode(['success' => false, 'message' => 'El precio debe ser un número válido']);
            return;
        }

        if(!is_numeric($stock) || $stock < 0) {
            echo json_encode(['success' => false, 'message' => 'El stock debe ser un número válido']);
            return;
        }

        // SEGURIDAD: Verificar que el producto pertenece a la sucursal del usuario
        if($this->rol == 'admin_sucursal') {
            $producto = $this->Producto_model->obtener_por_id($id);
            if(!$producto || $producto->id_sucursal != $this->id_sucursal) {
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para editar este producto']);
                return;
            }
        }

        $datos = [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => $precio,
            'id_categoria' => $id_categoria,
            'stock' => (int)$stock
        ];

        if($this->Producto_model->actualizar($id, $datos)) {
            echo json_encode(['success' => true, 'message' => 'Producto actualizado exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el producto']);
        }
    }

    public function producto_eliminar() {
        // Validar permisos de escritura
        if(!$this->tiene_permiso('productos')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No tiene permisos para eliminar productos']);
            return;
        }

        header('Content-Type: application/json');
        $id = $this->input->post('id_producto');
        
        if(empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID de producto no proporcionado']);
            return;
        }

        // SEGURIDAD: Verificar que el producto pertenece a la sucursal del usuario
        if($this->rol == 'admin_sucursal') {
            $producto = $this->Producto_model->obtener_por_id($id);
            if(!$producto || $producto->id_sucursal != $this->id_sucursal) {
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para eliminar este producto']);
                return;
            }
        }

        if($this->Producto_model->eliminar($id)) {
            echo json_encode(['success' => true, 'message' => 'Producto eliminado exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar el producto']);
        }
    }

    public function producto_toggle_disponibilidad() {
        // Validar permisos de escritura
        if(!$this->tiene_permiso('productos')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No tiene permisos para modificar productos']);
            return;
        }

        header('Content-Type: application/json');
        $id = $this->input->post('id_producto');
        $disponible_post = $this->input->post('disponible');
        
        // Convertir correctamente el estado string a booleano
        $disponible = ($disponible_post === 'true' || $disponible_post === true || $disponible_post === '1' || $disponible_post === 1);
        
        if(empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID de producto no proporcionado']);
            return;
        }

        // SEGURIDAD: Verificar que el producto pertenece a la sucursal del usuario
        if($this->rol == 'admin_sucursal') {
            $producto = $this->Producto_model->obtener_por_id($id);
            if(!$producto || $producto->id_sucursal != $this->id_sucursal) {
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para modificar este producto']);
                return;
            }
        }

        if($this->Producto_model->cambiar_disponibilidad($id, $disponible)) {
            $mensaje = $disponible ? 'Producto activado correctamente' : 'Producto desactivado correctamente';
            echo json_encode(['success' => true, 'message' => $mensaje]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar la disponibilidad']);
        }
    }

    // ============================================
    // DIAGNÓSTICO DE SESIÓN (TEMPORAL)
    // ============================================
    
    public function diagnostico() {
        echo "<!DOCTYPE html>";
        echo "<html><head>";
        echo "<meta charset='utf-8'>";
        echo "<title>🔍 Diagnóstico FUDO</title>";
        echo "<style>
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
        </style>";
        echo "</head><body>";
        
        echo "<h1>🔍 Diagnóstico Completo del Sistema FUDO</h1>";
        
        // ===== 1. VERIFICAR SESIÓN =====
        echo "<div class='section'>";
        echo "<h2>1️⃣ Estado de Sesión PHP</h2>";
        echo "<p class='success'>✅ Sesión activa (estás dentro del sistema)</p>";
        echo "<table>";
        echo "<tr><th>Campo</th><th>Valor</th></tr>";
        
        $session_fields = ['logueado', 'id_usuario', 'usuario', 'nombre_completo', 'email', 'rol', 'id_sucursal', 'nombre_sucursal'];
        foreach($session_fields as $field) {
            $value = $this->session->userdata($field);
            $display = $value ?? '<span class="error">NULL</span>';
            echo "<tr><td><strong>$field</strong></td><td>$display</td></tr>";
        }
        echo "</table>";
        
        $rol = $this->session->userdata('rol');
        if($rol == 'admin') {
            echo "<p class='success'>✅ ROL = 'admin' - Los enlaces DEBERÍAN aparecer</p>";
        } else {
            echo "<p class='warning'>⚠️ ROL = '$rol' - No es super admin. Los enlaces NO aparecerán.</p>";
        }
        echo "</div>";
        
        // ===== 2. VERIFICAR BASE DE DATOS =====
        echo "<div class='section'>";
        echo "<h2>2️⃣ Datos en Base de Datos PostgreSQL</h2>";
        $query = $this->db->query("SELECT id, usuario, rol, id_sucursal, activo FROM usuarios_admin WHERE usuario = 'admin'");
        $admin = $query->row();
        
        if($admin) {
            echo "<p class='success'>✅ Usuario 'admin' encontrado en BD</p>";
            echo "<table>";
            echo "<tr><th>Campo</th><th>Valor</th></tr>";
            echo "<tr><td><strong>ID</strong></td><td>{$admin->id}</td></tr>";
            echo "<tr><td><strong>Usuario</strong></td><td>{$admin->usuario}</td></tr>";
            echo "<tr><td><strong>Rol</strong></td><td><strong style='color:".($admin->rol == 'admin' ? 'green' : 'red')."'>{$admin->rol}</strong></td></tr>";
            echo "<tr><td><strong>ID Sucursal</strong></td><td>".($admin->id_sucursal ?? 'NULL')."</td></tr>";
            echo "<tr><td><strong>Activo</strong></td><td>".($admin->activo ? 'Sí' : 'No')."</td></tr>";
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
        echo "</div>";
        
        // ===== 3. VERIFICAR ARCHIVOS =====
        echo "<div class='section'>";
        echo "<h2>3️⃣ Verificación de Archivos de Vista</h2>";
        $archivos = [
            'pedidos.php' => APPPATH.'views/admin/pedidos.php',
            'categorias.php' => APPPATH.'views/admin/categorias.php',
            'productos.php' => APPPATH.'views/admin/productos.php'
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
        echo "</div>";
        
        // ===== 4. DIAGNÓSTICO Y SOLUCIÓN =====
        echo "<div class='section'>";
        echo "<h2>4️⃣ Diagnóstico y Solución</h2>";
        
        if($rol != 'admin') {
            echo "<p class='error'>❌ <strong>PROBLEMA:</strong> El rol en sesión es '$rol', no 'admin'</p>";
            echo "<p><strong>SOLUCIÓN:</strong></p>";
            echo "<ol>";
            echo "<li>Cierra sesión</li>";
            echo "<li>Vuelve a iniciar sesión con admin/admin123</li>";
            echo "<li>La sesión se actualizará con el rol correcto de la base de datos</li>";
            echo "</ol>";
            echo "<a href='".site_url('login/salir')."' class='btn btn-red'>🚪 Cerrar Sesión</a>";
        } else {
            echo "<p class='success'>✅ <strong>TODO ESTÁ CORRECTO:</strong> Sesión activa con rol 'admin'</p>";
            echo "<p>Si los enlaces no aparecen, prueba:</p>";
            echo "<ol>";
            echo "<li>Refresca la página con <strong>Ctrl + Shift + R</strong> (limpia caché)</li>";
            echo "<li>Abre las herramientas de desarrollador (F12) y ve a la pestaña Console</li>";
            echo "<li>Busca errores JavaScript que puedan ocultar elementos</li>";
            echo "</ol>";
        }
        echo "</div>";
        
        echo "<div style='text-align:center;padding:20px;'>";
        echo "<a href='".site_url('admin')."' class='btn btn-green'>⬅️ Volver al Admin</a>";
        echo "</div>";
        
        echo "</body></html>";
    }

    // ============================================
    // DIAGNÓSTICO DE PERMISOS
    // ============================================
    
    public function debug_permisos() {
        echo "<!DOCTYPE html>";
        echo "<html><head><meta charset='utf-8'><title>🔍 Debug Permisos</title>";
        echo "<style>body{font-family:Arial,sans-serif;padding:20px;} table{border-collapse:collapse;width:100%;} th,td{border:1px solid #ddd;padding:8px;} th{background:#f2f2f2;} .success{color:green;} .error{color:red;}</style>";
        echo "</head><body>";
        
        echo "<h1>🔍 Diagnóstico de Permisos - Mi Carta</h1>";
        
        // Datos de sesión
        echo "<h2>📋 Datos de Sesión</h2>";
        echo "<table>";
        echo "<tr><th>Campo</th><th>Valor</th></tr>";
        echo "<tr><td>rol</td><td>" . ($this->rol ?? 'NULL') . "</td></tr>";
        echo "<tr><td>id_sucursal</td><td>" . ($this->id_sucursal ?? 'NULL') . "</td></tr>";
        echo "<tr><td>permisos (raw)</td><td><pre>" . print_r($this->permisos, true) . "</pre></td></tr>";
        echo "<tr><td>permisos es array?</td><td>" . (is_array($this->permisos) ? 'SÍ' : 'NO') . "</td></tr>";
        if(is_array($this->permisos)) {
            echo "<tr><td>mi_carta existe?</td><td>" . (isset($this->permisos['mi_carta']) ? 'SÍ' : 'NO') . "</td></tr>";
            if(isset($this->permisos['mi_carta'])) {
                echo "<tr><td>mi_carta valor</td><td>" . ($this->permisos['mi_carta'] === true ? 'TRUE' : ($this->permisos['mi_carta'] === false ? 'FALSE' : $this->permisos['mi_carta'])) . "</td></tr>";
            }
        }
        echo "</table>";
        
        // Test de función tiene_permiso
        echo "<h2>🧪 Test de Función tiene_permiso</h2>";
        echo "<table>";
        echo "<tr><th>Sección</th><th>Resultado</th></tr>";
        $secciones = ['mi_carta', 'categorias', 'productos', 'usuarios', 'pedidos', 'cocina', 'mesas'];
        foreach($secciones as $seccion) {
            $resultado = $this->tiene_permiso($seccion);
            $clase = $resultado ? 'success' : 'error';
            echo "<tr><td>$seccion</td><td class='$clase'>" . ($resultado ? 'PERMITIDO' : 'DENEGADO') . "</td></tr>";
        }
        echo "</table>";
        
        // Datos de BD
        echo "<h2>💾 Datos de Base de Datos</h2>";
        $usuario_actual = $this->session->userdata('usuario');
        if($usuario_actual) {
            $query = $this->db->get_where('usuarios_admin', ['usuario' => $usuario_actual]);
            $user_data = $query->row();
            if($user_data) {
                echo "<table>";
                echo "<tr><th>Campo BD</th><th>Valor</th></tr>";
                echo "<tr><td>ID</td><td>$user_data->id</td></tr>";
                echo "<tr><td>Usuario</td><td>$user_data->usuario</td></tr>";
                echo "<tr><td>Rol</td><td>$user_data->rol</td></tr>";
                echo "<tr><td>ID Sucursal</td><td>" . ($user_data->id_sucursal ?? 'NULL') . "</td></tr>";
                echo "<tr><td>Permisos (raw)</td><td><pre>" . ($user_data->permisos ?? 'NULL') . "</pre></td></tr>";
                
                if($user_data->permisos) {
                    $permisos_bd = json_decode($user_data->permisos, true);
                    echo "<tr><td>Permisos (parsed)</td><td><pre>" . print_r($permisos_bd, true) . "</pre></td></tr>";
                    if(is_array($permisos_bd)) {
                        echo "<tr><td>mi_carta en BD?</td><td>" . (isset($permisos_bd['mi_carta']) ? 'SÍ' : 'NO') . "</td></tr>";
                        if(isset($permisos_bd['mi_carta'])) {
                            echo "<tr><td>mi_carta valor BD</td><td>" . ($permisos_bd['mi_carta'] === true ? 'TRUE' : ($permisos_bd['mi_carta'] === false ? 'FALSE' : $permisos_bd['mi_carta'])) . "</td></tr>";
                        }
                    }
                }
                echo "</table>";
            }
        }
        
        echo "<br><a href='" . site_url('admin/usuarios') . "'>⬅️ Volver a Usuarios</a>";
        echo "</body></html>";
    }
    
    // ============================================
    // MI CARTA - Vista previa para admin sucursal
    // ============================================
    
    public function mi_carta() {
        // Verificar permisos
        if(!$this->tiene_permiso('mi_carta')) {
            show_error('No tienes permisos para acceder a esta sección', 403);
            return;
        }

        // Cargar modelo de sucursales para obtener todos los datos
        $this->load->model('Sucursal_model');
        
        // Obtener datos completos de la sucursal
        $sucursal = $this->Sucursal_model->obtener_por_id($this->id_sucursal);
        
        // Crear objeto mesa simulado con datos de sucursal para compatibilidad con la vista
        $mesa = new stdClass();
        $mesa->nombre_sucursal = $sucursal->nombre ?? 'Sucursal';
        $mesa->direccion = $sucursal->direccion ?? 'Dirección no disponible';
        $mesa->telefono = $sucursal->telefono ?? 'Teléfono no disponible';
        
        $data['mesa'] = $mesa;
        $data['id_mesa'] = 0; // No aplica para admin

        // Obtener categorías activas de la sucursal del admin
        $data['categorias'] = $this->Categoria_model->obtener_categorias_activas($this->id_sucursal);
        
        // Obtener productos agrupados por categoría
        $productos_por_categoria = [];
        foreach($data['categorias'] as $cat) {
            $productos_por_categoria[$cat->id_categoria] = $this->Producto_model->obtener_por_categoria($cat->id_categoria, $this->id_sucursal);
        }
        $data['productos_por_categoria'] = $productos_por_categoria;
        
        
        $this->load->view('admin/mi_carta', $data);
    }

    // ============================================
    // USUARIOS - Gestión de usuarios del sistema
    // ============================================
    
    public function usuarios() {
        // Verificar permisos - Solo admin y admin_sucursal
        if(!$this->tiene_permiso('usuarios')) {
            show_error('No tienes permisos para acceder a esta sección', 403);
            return;
        }

        $data = [];
        $data['rol_actual'] = $this->rol;
        $data['id_sucursal_actual'] = $this->id_sucursal;
        
        // Obtener usuarios según el rol
        if($this->rol == 'admin') {
            // Super admin ve todos los usuarios
            $data['usuarios'] = $this->Usuario_model->obtener_todos();
            // Obtener todas las sucursales
            $data['sucursales'] = $this->Sucursal_model->obtener_todas();
        } elseif($this->rol == 'admin_sucursal') {
            // Admin de sucursal solo ve usuarios de su sucursal
            $data['usuarios'] = $this->Usuario_model->obtener_por_sucursal($this->id_sucursal);
            // Obtener solo su sucursal
            $sucursal = $this->Sucursal_model->obtener_por_id($this->id_sucursal);
            $data['sucursales'] = $sucursal ? [$sucursal] : [];
        }
        
        $this->load->view('admin/usuarios', $data);
    }

    public function usuario_crear() {
        header('Content-Type: application/json');
        
        // Verificar permisos
        if(!$this->tiene_permiso('usuarios')) {
            echo json_encode(['success' => false, 'message' => 'No tienes permisos']);
            return;
        }
        
        // Validar datos requeridos
        $nombre_completo = $this->input->post('nombre_completo');
        $usuario = $this->input->post('usuario');
        $email = $this->input->post('email');
        $contrasena = $this->input->post('contrasena');
        $rol = $this->input->post('rol');
        $id_sucursal = $this->input->post('id_sucursal');
        
        if(empty($nombre_completo) || empty($usuario) || empty($email) || empty($contrasena) || empty($rol)) {
            echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
            return;
        }
        
        // Verificar que no exista el usuario
        if($this->Usuario_model->existe_usuario($usuario)) {
            echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya existe']);
            return;
        }
        
        // Verificar que no exista el email
        if($this->Usuario_model->existe_email($email)) {
            echo json_encode(['success' => false, 'message' => 'El email ya está registrado']);
            return;
        }
        
        // Si es admin_sucursal, usar su propia sucursal
        if($this->rol == 'admin_sucursal') {
            $id_sucursal = $this->id_sucursal;
        }
        
        // Preparar datos
        $datos = [
            'nombre_completo' => $nombre_completo,
            'usuario' => $usuario,
            'email' => $email,
            'contrasena' => $contrasena,
            'rol' => $rol,
            'id_sucursal' => $id_sucursal,
            'activo' => true
        ];
        
        // Agregar permisos si es usuario
        if($rol == 'usuario') {
            $permisos = [];
            $secciones = ['pedidos', 'categorias', 'productos', 'micarta', 'mesas', 'cocina'];
            foreach($secciones as $seccion) {
                $permisos[$seccion] = $this->input->post('permiso_' . $seccion) == '1';
            }
            $datos['permisos'] = json_encode($permisos);
        }
        
        // Crear usuario
        if($this->Usuario_model->crear($datos)) {
            echo json_encode(['success' => true, 'message' => 'Usuario creado exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al crear el usuario']);
        }
    }

    public function usuario_editar() {
        header('Content-Type: application/json');
        
        // Verificar permisos
        if(!$this->tiene_permiso('usuarios')) {
            echo json_encode(['success' => false, 'message' => 'No tienes permisos']);
            return;
        }
        
        $id_usuario = $this->input->post('id_usuario');
        $nombre_completo = $this->input->post('nombre_completo');
        $usuario = $this->input->post('usuario');
        $email = $this->input->post('email');
        $contrasena = $this->input->post('contrasena');
        $rol = $this->input->post('rol');
        $id_sucursal = $this->input->post('id_sucursal');
        
        if(empty($id_usuario) || empty($nombre_completo) || empty($usuario) || empty($email) || empty($rol)) {
            echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
            return;
        }
        
        // Verificar que no exista el usuario (excepto el actual)
        if($this->Usuario_model->existe_usuario($usuario, $id_usuario)) {
            echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya existe']);
            return;
        }
        
        // Verificar que no exista el email (excepto el actual)
        if($this->Usuario_model->existe_email($email, $id_usuario)) {
            echo json_encode(['success' => false, 'message' => 'El email ya está registrado']);
            return;
        }
        
        // Si es admin_sucursal, usar su propia sucursal
        if($this->rol == 'admin_sucursal') {
            $id_sucursal = $this->id_sucursal;
        }
        
        // Preparar datos
        $datos = [
            'nombre_completo' => $nombre_completo,
            'usuario' => $usuario,
            'email' => $email,
            'rol' => $rol,
            'id_sucursal' => $id_sucursal
        ];
        
        // Solo actualizar contraseña si se proporciona
        if(!empty($contrasena)) {
            $datos['contrasena'] = $contrasena;
        }
        
        // Agregar permisos si es usuario
        if($rol == 'usuario') {
            $permisos = [];
            $secciones = ['pedidos', 'categorias', 'productos', 'micarta', 'mesas', 'cocina'];
            foreach($secciones as $seccion) {
                $permisos[$seccion] = $this->input->post('permiso_' . $seccion) == '1';
            }
            $datos['permisos'] = json_encode($permisos);
        }
        
        // Actualizar usuario
        if($this->Usuario_model->actualizar($id_usuario, $datos)) {
            echo json_encode(['success' => true, 'message' => 'Usuario actualizado exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el usuario']);
        }
    }

    public function usuario_eliminar() {
        header('Content-Type: application/json');
        
        // Verificar permisos
        if(!$this->tiene_permiso('usuarios')) {
            echo json_encode(['success' => false, 'message' => 'No tienes permisos']);
            return;
        }
        
        $id_usuario = $this->input->post('id_usuario');
        
        if(empty($id_usuario)) {
            echo json_encode(['success' => false, 'message' => 'ID de usuario no proporcionado']);
            return;
        }
        
        // Eliminar usuario
        if($this->Usuario_model->eliminar($id_usuario)) {
            echo json_encode(['success' => true, 'message' => 'Usuario eliminado exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar el usuario']);
        }
    }

    public function usuario_toggle_estado() {
        header('Content-Type: application/json');
        
        // Verificar permisos
        if(!$this->tiene_permiso('usuarios')) {
            echo json_encode(['success' => false, 'message' => 'No tienes permisos']);
            return;
        }
        
        $id_usuario = $this->input->post('id_usuario');
        $nuevo_estado = $this->input->post('nuevo_estado') === 'true';
        
        if(empty($id_usuario)) {
            echo json_encode(['success' => false, 'message' => 'ID de usuario no proporcionado']);
            return;
        }
        
        // Cambiar estado
        if($this->Usuario_model->cambiar_estado($id_usuario, $nuevo_estado)) {
            echo json_encode(['success' => true, 'message' => 'Estado actualizado exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado']);
        }
    }
    
    /**
     * Verificar si la sucursal del usuario sigue activa
     */
    private function verificar_sucursal_activa() {
        // Solo verificar para usuarios no admin
        if($this->rol == 'admin') {
            return;
        }
        
        // Verificar si la sucursal sigue activa
        if($this->id_sucursal) {
            $sucursal = $this->Sucursal_model->obtener_por_id($this->id_sucursal);
            if(!$sucursal || $sucursal->activo === 'f' || $sucursal->activo === false) {
                // Cerrar sesión y redirigir al login
                $this->session->sess_destroy();
                $this->session->set_flashdata('error', 'Su sucursal ha sido desactivada. Contacte al administrador.');
                redirect('login');
            }
        }
    }
}
