<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sucursales extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
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
        $data['sucursales'] = $this->Sucursal_model->obtener_todas();
        $this->load->view('admin/sucursales', $data);
    }

    public function crear() {
        header('Content-Type: application/json');
        
        $nombre = trim($this->input->post('nombre'));
        $direccion = trim($this->input->post('direccion'));
        $telefono = trim($this->input->post('telefono'));
        $email = trim($this->input->post('email'));
        $whatsapp = trim($this->input->post('whatsapp'));
        $instagram = trim($this->input->post('instagram'));

        // Validaciones
        if(empty($nombre)) {
            echo json_encode(['success' => false, 'message' => 'El nombre es obligatorio']);
            return;
        }

        $datos = [
            'nombre' => $nombre,
            'direccion' => $direccion,
            'telefono' => $telefono,
            'email' => $email,
            'whatsapp' => $whatsapp,
            'instagram' => $instagram,
            'activo' => true
        ];

        if($this->Sucursal_model->crear($datos)) {
            echo json_encode(['success' => true, 'message' => 'Sucursal creada exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al crear la sucursal']);
        }
    }

    public function editar() {
        header('Content-Type: application/json');
        
        $id_sucursal = $this->input->post('id_sucursal');
        $nombre = $this->input->post('nombre');
        $direccion = $this->input->post('direccion');
        $telefono = $this->input->post('telefono');
        $email = $this->input->post('email');
        $whatsapp = $this->input->post('whatsapp');
        $instagram = $this->input->post('instagram');

        // Validaciones básicas
        if(empty($id_sucursal) || empty($nombre)) {
            echo json_encode(['success' => false, 'message' => 'ID y nombre son obligatorios']);
            return;
        }

        // Incluir todos los campos, permitiendo valores vacíos para campos opcionales
        $datos = array(
            'nombre' => $nombre,
            'direccion' => !empty($direccion) ? $direccion : null,
            'telefono' => !empty($telefono) ? $telefono : null,
            'email' => !empty($email) ? $email : null,
            'whatsapp' => !empty($whatsapp) ? $whatsapp : null,
            'instagram' => !empty($instagram) ? $instagram : null
        );

        try {
            // Actualización directa con query builder
            $this->db->where('id_sucursal', $id_sucursal);
            $resultado = $this->db->update('sucursales', $datos);
            
            if($resultado) {
                echo json_encode(['success' => true, 'message' => 'Sucursal actualizada exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar la sucursal']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    public function eliminar() {
        header('Content-Type: application/json');
        
        $id_sucursal = $this->input->post('id_sucursal');
        
        if(empty($id_sucursal)) {
            echo json_encode(['success' => false, 'message' => 'ID de sucursal no proporcionado']);
            return;
        }

        // Verificar si tiene usuarios activos
        if($this->Sucursal_model->tiene_usuarios($id_sucursal)) {
            echo json_encode(['success' => false, 'message' => 'No se puede eliminar: la sucursal tiene usuarios activos asignados']);
            return;
        }

        // Verificar si tiene mesas
        if($this->Sucursal_model->tiene_mesas($id_sucursal)) {
            echo json_encode(['success' => false, 'message' => 'No se puede eliminar: la sucursal tiene mesas asignadas. Elimínelas primero.']);
            return;
        }

        if($this->Sucursal_model->eliminar($id_sucursal)) {
            echo json_encode(['success' => true, 'message' => 'Sucursal eliminada exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar la sucursal']);
        }
    }

    public function cambiar_estado() {
        header('Content-Type: application/json');
        
        $id_sucursal = $this->input->post('id_sucursal');
        $estado = $this->input->post('estado') === 'true' || $this->input->post('estado') === '1';
        
        if(empty($id_sucursal)) {
            echo json_encode(['success' => false, 'message' => 'ID de sucursal no proporcionado']);
            return;
        }

        $resultado = $this->Sucursal_model->cambiar_estado($id_sucursal, $estado);
        
        if($resultado) {
            echo json_encode(['success' => true, 'message' => 'Estado actualizado']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado']);
        }
    }

    public function estadisticas($id_sucursal) {
        header('Content-Type: application/json');
        $stats = $this->Sucursal_model->obtener_estadisticas($id_sucursal);
        echo json_encode($stats);
    }

    public function obtener($id_sucursal) {
        header('Content-Type: application/json');
        
        if(empty($id_sucursal)) {
            echo json_encode(['success' => false, 'message' => 'ID de sucursal no proporcionado']);
            return;
        }

        $sucursal = $this->Sucursal_model->obtener_por_id($id_sucursal);
        
        if($sucursal) {
            echo json_encode(['success' => true, 'data' => $sucursal]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Sucursal no encontrada']);
        }
    }
}
