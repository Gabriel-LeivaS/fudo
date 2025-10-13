<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Usuario_model');
        $this->load->model('Sucursal_model');
        $this->load->library('session');
        $this->load->helper('url');

        // Verificar sesión
        if(!$this->session->userdata('logueado')) {
            redirect('login');
        }

        // Super admin y admin_sucursal pueden acceder
        $rol_actual = $this->session->userdata('rol');
        if($rol_actual != 'admin' && $rol_actual != 'admin_sucursal') {
            show_error('No tienes permisos para acceder a esta sección', 403);
        }
    }

    public function index() {
        $rol_actual = $this->session->userdata('rol');
        $id_sucursal_actual = $this->session->userdata('id_sucursal');
        
        // Si es admin_sucursal, solo ver usuarios de su sucursal
        if($rol_actual == 'admin_sucursal') {
            $data['usuarios'] = $this->Usuario_model->obtener_por_sucursal($id_sucursal_actual);
            $data['sucursales'] = [$this->Sucursal_model->obtener_por_id($id_sucursal_actual)];
        } else {
            $data['usuarios'] = $this->Usuario_model->obtener_todos();
            $data['sucursales'] = $this->Sucursal_model->obtener_activas();
        }
        
        $this->load->view('admin/usuarios', $data);
    }

    public function crear() {
        header('Content-Type: application/json');
        
        $usuario = trim($this->input->post('usuario'));
        $contrasena = $this->input->post('contrasena');
        $nombre_completo = trim($this->input->post('nombre_completo'));
        $email = trim($this->input->post('email'));
        $rol = $this->input->post('rol');
        $id_sucursal = $this->input->post('id_sucursal');
        $permisos_array = $this->input->post('permisos');
        
        $rol_actual = $this->session->userdata('rol');
        $id_sucursal_actual = $this->session->userdata('id_sucursal');

        // Validaciones
        if(empty($usuario) || empty($contrasena) || empty($nombre_completo) || empty($rol)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit;
        }

        // Admin_sucursal solo puede crear usuarios de su sucursal y solo rol 'usuario'
        if($rol_actual == 'admin_sucursal') {
            if($rol != 'usuario') {
                echo json_encode(['success' => false, 'message' => 'Solo puedes crear usuarios con rol Usuario']);
                exit;
            }
            if($id_sucursal != $id_sucursal_actual) {
                echo json_encode(['success' => false, 'message' => 'Solo puedes crear usuarios para tu sucursal']);
                exit;
            }
        }

        // Validar que admin_sucursal y usuario requieran sucursal
        if(($rol == 'admin_sucursal' || $rol == 'usuario') && empty($id_sucursal)) {
            echo json_encode(['success' => false, 'message' => 'Debe seleccionar una sucursal para este rol']);
            exit;
        }

        // Verificar si el usuario ya existe
        if($this->Usuario_model->existe_usuario($usuario)) {
            echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya existe']);
            return;
        }

        // Verificar si el email ya existe
        if(!empty($email) && $this->Usuario_model->existe_email($email)) {
            echo json_encode(['success' => false, 'message' => 'El email ya está registrado']);
            return;
        }

        // Procesar permisos para rol usuario
        $permisos_json = null;
        if($rol == 'usuario') {
            // NUEVO: Admin DEBE seleccionar permisos explícitamente
            // No hay valores por defecto, si no marca nada = no tiene acceso
            
            if(!is_array($permisos_array)) {
                // Si no se envió array de permisos, establecer todos en false
                $permisos_array = [];
            }
            
            $permisos = [
                'pedidos' => isset($permisos_array['pedidos']),
                'mesas' => isset($permisos_array['mesas']),
                'cocina' => isset($permisos_array['cocina']),
                'mi_carta' => isset($permisos_array['mi_carta']),
                'categorias' => isset($permisos_array['categorias']),
                'productos' => isset($permisos_array['productos'])
            ];
            
            $permisos_json = json_encode($permisos);
        }

        $datos = [
            'usuario' => $usuario,
            'contrasena' => $contrasena, // El modelo lo encriptará
            'nombre_completo' => $nombre_completo,
            'email' => $email,
            'rol' => $rol,
            'id_sucursal' => ($rol == 'admin_sucursal' || $rol == 'usuario') ? $id_sucursal : null,
            'permisos' => $permisos_json,
            'activo' => true
        ];

        if($this->Usuario_model->crear($datos)) {
            echo json_encode(['success' => true, 'message' => 'Usuario creado exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al crear el usuario']);
        }
    }

    public function editar($id = null) {
        header('Content-Type: application/json');
        
        // DEBUG CRÍTICO: Ver qué IDs estamos recibiendo
        $log_file = FCPATH . 'debug_log.txt';
        $timestamp = date('Y-m-d H:i:s');
        $log_msg = "\n\n========================================\n";
        $log_msg .= "EDITAR USUARIO - $timestamp\n";
        $log_msg .= "========================================\n";
        $log_msg .= "ID desde URL (\$id parametro): " . var_export($id, true) . "\n";
        $log_msg .= "ID desde POST (id_usuario): " . var_export($this->input->post('id_usuario'), true) . "\n";
        $log_msg .= "Usuario desde POST: " . var_export($this->input->post('usuario'), true) . "\n";
        file_put_contents($log_file, $log_msg, FILE_APPEND);
        
        // Obtener ID de parámetro URL o POST
        if(empty($id)) {
            $id = $this->input->post('id_usuario');
        }
        
        $log_msg = "ID FINAL que se usará: " . var_export($id, true) . "\n";
        file_put_contents($log_file, $log_msg, FILE_APPEND);
        
        $usuario = trim($this->input->post('usuario'));
        $contrasena = $this->input->post('contrasena'); // Puede ser vacío si no se cambia
        $nombre_completo = trim($this->input->post('nombre_completo'));
        $email = trim($this->input->post('email'));
        $rol = $this->input->post('rol');
        $id_sucursal = $this->input->post('id_sucursal');
        $permisos_array = $this->input->post('permisos');
        
        // DEBUG CRÍTICO: Ver TODO lo que llega en POST
        $log_msg = "========================================\n";
        $log_msg .= "POST COMPLETO RECIBIDO:\n";
        $log_msg .= "========================================\n";
        $log_msg .= "usuario: " . var_export($usuario, true) . "\n";
        $log_msg .= "rol: " . var_export($rol, true) . "\n";
        $log_msg .= "permisos_array: " . var_export($permisos_array, true) . "\n";
        $log_msg .= "permisos_array es array? " . (is_array($permisos_array) ? 'SI' : 'NO') . "\n";
        if(is_array($permisos_array)) {
            $log_msg .= "Contenido de permisos_array:\n";
            foreach($permisos_array as $key => $value) {
                $log_msg .= "  - permisos[$key] = " . var_export($value, true) . "\n";
            }
        }
        $log_msg .= "========================================\n";
        file_put_contents($log_file, $log_msg, FILE_APPEND);
        
        $rol_actual = $this->session->userdata('rol');
        $id_sucursal_actual = $this->session->userdata('id_sucursal');

        // Validaciones
        if(empty($id) || empty($usuario) || empty($nombre_completo) || empty($rol)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit;
        }

        // Admin_sucursal solo puede editar usuarios de su sucursal
        if($rol_actual == 'admin_sucursal') {
            $usuario_existente = $this->Usuario_model->obtener_por_id($id);
            if($usuario_existente->id_sucursal != $id_sucursal_actual) {
                echo json_encode(['success' => false, 'message' => 'Solo puedes editar usuarios de tu sucursal']);
                exit;
            }
            if($rol != 'usuario') {
                echo json_encode(['success' => false, 'message' => 'Solo puedes gestionar usuarios con rol Usuario']);
                exit;
            }
        }

        // Validar que admin_sucursal y usuario requieran sucursal
        if(($rol == 'admin_sucursal' || $rol == 'usuario') && empty($id_sucursal)) {
            echo json_encode(['success' => false, 'message' => 'Debe seleccionar una sucursal para este rol']);
            exit;
        }

        // Verificar si el usuario ya existe (excepto el actual)
        if($this->Usuario_model->existe_usuario($usuario, $id)) {
            echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya existe']);
            return;
        }

        // Verificar si el email ya existe (excepto el actual)
        if(!empty($email) && $this->Usuario_model->existe_email($email, $id)) {
            echo json_encode(['success' => false, 'message' => 'El email ya está registrado']);
            return;
        }

        // Procesar permisos para rol usuario
        $permisos_json = null;
        if($rol == 'usuario') {
            // IMPORTANTE: Los checkboxes NO marcados NO se envían en POST
            // Por eso usamos isset() - si existe = true, si no existe = false
            
            // Si no se envió array de permisos, establecer todos en false
            if(!is_array($permisos_array)) {
                $permisos_array = [];
            }
            
            $permisos = [
                'pedidos' => isset($permisos_array['pedidos']),
                'mesas' => isset($permisos_array['mesas']),
                'cocina' => isset($permisos_array['cocina']),
                'mi_carta' => isset($permisos_array['mi_carta']),
                'categorias' => isset($permisos_array['categorias']),
                'productos' => isset($permisos_array['productos'])
            ];
            $permisos_json = json_encode($permisos);
            
            // DEBUG: Registrar en log personalizado
            $log_file = FCPATH . 'debug_log.txt';
            $timestamp = date('Y-m-d H:i:s');
            $log_msg = "\n=== EDITAR USUARIO - $timestamp ===\n";
            $log_msg .= "ID Usuario a editar: " . $id . "\n";
            $log_msg .= "Usuario editado: " . $usuario . "\n";
            $log_msg .= "Rol: " . $rol . "\n";
            $log_msg .= "Permisos POST (raw): " . print_r($permisos_array, true) . "\n";
            $log_msg .= "Permisos procesados: " . print_r($permisos, true) . "\n";
            $log_msg .= "JSON generado: " . $permisos_json . "\n";
            file_put_contents($log_file, $log_msg, FILE_APPEND);
        }

        $datos = [
            'usuario' => $usuario,
            'nombre_completo' => $nombre_completo,
            'email' => $email,
            'rol' => $rol,
            'id_sucursal' => ($rol == 'admin_sucursal' || $rol == 'usuario') ? $id_sucursal : null,
            'permisos' => $permisos_json
        ];

        // Solo agregar contraseña si se proporciona una nueva
        if(!empty($contrasena)) {
            $datos['contrasena'] = $contrasena;
        }

        // DEBUG: Log antes de llamar al modelo
        $log_file = FCPATH . 'debug_log.txt';
        $log_msg = ">>> Llamando a Usuario_model->actualizar()\n";
        $log_msg .= ">>> ID a actualizar: " . $id . "\n";
        $log_msg .= ">>> Datos a enviar: " . print_r($datos, true) . "\n";
        file_put_contents($log_file, $log_msg, FILE_APPEND);

        $resultado = $this->Usuario_model->actualizar($id, $datos);
        
        // DEBUG: Log después de llamar al modelo
        $log_msg = ">>> Resultado del modelo->actualizar(): " . ($resultado ? 'TRUE' : 'FALSE') . "\n";
        file_put_contents($log_file, $log_msg, FILE_APPEND);

        if($resultado) {
            $mensaje = 'Usuario actualizado exitosamente';
            // Agregar nota si se editaron permisos
            if($rol == 'usuario' && !empty($permisos_json)) {
                $mensaje .= '. Los cambios de permisos se aplicarán cuando el usuario vuelva a iniciar sesión';
            }
            echo json_encode(['success' => true, 'message' => $mensaje]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el usuario']);
        }
    }

    public function eliminar($id = null) {
        header('Content-Type: application/json');
        
        // Obtener ID de parámetro URL o POST
        if(empty($id)) {
            $id = $this->input->post('id');
        }
        
        if(empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID de usuario no proporcionado']);
            return;
        }

        // Evitar que se elimine a sí mismo
        if($id == $this->session->userdata('id_usuario')) {
            echo json_encode(['success' => false, 'message' => 'No puedes eliminar tu propio usuario']);
            return;
        }

        if($this->Usuario_model->eliminar($id)) {
            echo json_encode(['success' => true, 'message' => 'Usuario eliminado exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar el usuario']);
        }
    }

    public function cambiar_estado($id = null, $estado = null) {
        header('Content-Type: application/json');
        
        // Obtener parámetros de URL o POST
        if(empty($id)) {
            $id = $this->input->post('id');
        }
        if($estado === null) {
            $estado = $this->input->post('estado');
        }
        
        // Convertir estado a booleano
        $estado = $estado === 'true' || $estado === '1' || $estado === 1 || $estado === true;
        
        if(empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID de usuario no proporcionado']);
            return;
        }

        // Evitar que se desactive a sí mismo
        if($id == $this->session->userdata('id_usuario') && !$estado) {
            echo json_encode(['success' => false, 'message' => 'No puedes desactivar tu propio usuario']);
            return;
        }

        if($this->Usuario_model->cambiar_estado($id, $estado)) {
            echo json_encode(['success' => true, 'message' => 'Estado actualizado exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado']);
        }
    }
}
