<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cocina extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Pedido_model');
        $this->load->library('session');
        $this->load->helper('url');

        // acceso restringido: se puede adaptar según reglas
        if(!$this->session->userdata('logueado')) {
            redirect('login');
        }
        
        // Cargar información del usuario si está logueado
        $this->rol = $this->session->userdata('rol');
        $this->id_sucursal = $this->session->userdata('id_sucursal');
        $this->permisos = $this->session->userdata('permisos');
    }
    
    /**
     * Verificar si el usuario tiene permiso para cocina
     */
    private function tiene_permiso_cocina() {
        if($this->rol == 'admin' || $this->rol == 'admin_sucursal') {
            return true;
        }
        if($this->rol == 'usuario' && is_array($this->permisos)) {
            return isset($this->permisos['cocina']) && $this->permisos['cocina'] === true;
        }
        return false;
    }

    public function index($id_pedido = null) {
        // Verificar permisos
        if(!$this->tiene_permiso_cocina()) {
            show_error('No tienes permisos para acceder a esta sección', 403);
            return;
        }
        
        $data = [];
        if($id_pedido) {
            $data['id_pedido_inicial'] = $id_pedido;
        }
        $this->load->view('admin/panel_cocina', $data);
    }

    public function pendientes_json() {
        // Si es admin_sucursal o usuario, filtrar por su sucursal
        $id_sucursal = ($this->rol == 'admin_sucursal' || $this->rol == 'usuario') ? $this->id_sucursal : null;
        $pedidos = $this->Pedido_model->obtener_pedidos_pendientes($id_sucursal);
        echo json_encode($pedidos);
    }

    public function detalle_json($id_pedido) {
        $detalle = $this->Pedido_model->obtener_detalle_pedido($id_pedido);
        echo json_encode($detalle);
    }
}

