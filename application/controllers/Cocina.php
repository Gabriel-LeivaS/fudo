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
    }

    public function index() {
        $this->load->view('admin/panel_cocina');
    }

    public function pendientes_json() {
        $pedidos = $this->Pedido_model->obtener_pedidos_pendientes();
        echo json_encode($pedidos);
    }

    public function detalle_json($id_pedido) {
        $detalle = $this->Pedido_model->obtener_detalle_pedido($id_pedido);
        echo json_encode($detalle);
    }
}

