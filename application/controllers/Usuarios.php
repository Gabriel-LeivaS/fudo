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

        // SOLO super admin puede acceder
        if($this->session->userdata('rol') != 'admin') {
            show_error('No tienes permisos para acceder a esta sección', 403);
        }
    }

    public function index() {
        $data['usuarios'] = $this->Usuario_model->obtener_todos();
        $data['sucursales'] = $this->Sucursal_model->obtener_activas();
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

        // Validaciones
        if(empty($usuario) || empty($contrasena) || empty($nombre_completo) || empty($rol)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        if($rol == 'admin_sucursal' && empty($id_sucursal)) {
            echo json_encode(['success' => false, 'message' => 'Debe seleccionar una sucursal para admin de sucursal']);
            return;
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

        $datos = [
            'usuario' => $usuario,
            'contrasena' => $contrasena, // El modelo lo encriptará
            'nombre_completo' => $nombre_completo,
            'email' => $email,
            'rol' => $rol,
            'id_sucursal' => ($rol == 'admin_sucursal') ? $id_sucursal : null,
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
        
        // Obtener ID de parámetro URL o POST
        if(empty($id)) {
            $id = $this->input->post('id_usuario');
        }
        
        $usuario = trim($this->input->post('usuario'));
        $contrasena = $this->input->post('contrasena'); // Puede ser vacío si no se cambia
        $nombre_completo = trim($this->input->post('nombre_completo'));
        $email = trim($this->input->post('email'));
        $rol = $this->input->post('rol');
        $id_sucursal = $this->input->post('id_sucursal');

        // Validaciones
        if(empty($id) || empty($usuario) || empty($nombre_completo) || empty($rol)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        if($rol == 'admin_sucursal' && empty($id_sucursal)) {
            echo json_encode(['success' => false, 'message' => 'Debe seleccionar una sucursal para admin de sucursal']);
            return;
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

        $datos = [
            'usuario' => $usuario,
            'nombre_completo' => $nombre_completo,
            'email' => $email,
            'rol' => $rol,
            'id_sucursal' => ($rol == 'admin_sucursal') ? $id_sucursal : null
        ];

        // Solo agregar contraseña si se proporciona una nueva
        if(!empty($contrasena)) {
            $datos['contrasena'] = $contrasena;
        }

        if($this->Usuario_model->actualizar($id, $datos)) {
            echo json_encode(['success' => true, 'message' => 'Usuario actualizado exitosamente']);
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
