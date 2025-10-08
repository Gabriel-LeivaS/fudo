<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pedidos extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Pedido_model');
        $this->load->helper('url');
        $this->load->library('session');
    }

    public function crear() {
        $data = json_decode($this->input->raw_input_stream, TRUE);

        if (!isset($data['detalle']) || !is_array($data['detalle']) || count($data['detalle'])==0) {
            echo json_encode(['error' => 'Detalle de pedido vacío o inválido']);
            return;
        }

        // id_cliente opcional: crear cliente anónimo si no existe
        $id_cliente = $data['id_cliente'] ?? null;
        if (empty($id_cliente)) {
            // crear cliente genérico en tabla clientes
            $this->load->database();
            $this->db->insert('clientes', ['nombre' => 'Cliente Anónimo']);
            $id_cliente = $this->db->insert_id();
        }
+
        $id_mesa = $this->session->userdata('id_mesa') ?? ($data['id_mesa'] ?? null);

        $id = $this->Pedido_model->crear_pedido($id_cliente, $data['detalle'], $id_mesa);
+
        if ($id) {
            echo json_encode(['ok' => true, 'id_pedido' => $id]);
        } else {
            echo json_encode(['error' => 'No se pudo crear el pedido']);
        }
    }
}
