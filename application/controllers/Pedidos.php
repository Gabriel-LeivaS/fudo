<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pedidos extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model(['Pedido_model', 'Producto_model']);
        $this->load->helper('url');
        $this->load->library('session');
    }

    public function crear() {
        // Asegurar que solo se envíe JSON
        header('Content-Type: application/json; charset=utf-8');
        
        $raw_input = $this->input->raw_input_stream;
        $data = json_decode($raw_input, TRUE);

        if (!isset($data['detalle']) || !is_array($data['detalle']) || count($data['detalle'])==0) {
            echo json_encode(['error' => 'Detalle de pedido vacío o inválido']);
            exit;
        }

        // Verificar disponibilidad de todos los productos antes de crear el pedido
        $productos_no_disponibles = [];
        $productos_sin_stock = [];
        
        foreach ($data['detalle'] as $item) {
            if (!$this->Producto_model->esta_disponible($item['id_producto'])) {
                $producto = $this->Producto_model->obtener_por_id($item['id_producto']);
                $productos_no_disponibles[] = $producto ? $producto->nombre : 'Producto #' . $item['id_producto'];
            } else {
                // Verificar stock suficiente
                $producto = $this->Producto_model->obtener_por_id($item['id_producto']);
                $stock_actual = isset($producto->stock) ? (int)$producto->stock : 0;
                $cantidad_solicitada = (int)$item['cantidad'];
                
                if ($stock_actual < $cantidad_solicitada) {
                    $productos_sin_stock[] = [
                        'nombre' => $producto->nombre,
                        'solicitado' => $cantidad_solicitada,
                        'disponible' => $stock_actual
                    ];
                }
            }
        }

        // Si hay productos no disponibles, rechazar el pedido
        if (!empty($productos_no_disponibles)) {
            echo json_encode([
                'error' => 'Algunos productos ya no estÃ¡n disponibles: ' . implode(', ', $productos_no_disponibles),
                'productos_no_disponibles' => $productos_no_disponibles
            ]);
            exit;
        }

        // Si hay productos sin stock suficiente, rechazar el pedido
        if (!empty($productos_sin_stock)) {
            $mensaje = 'Stock insuficiente para: ';
            $detalles = [];
            foreach ($productos_sin_stock as $ps) {
                $detalles[] = "{$ps['nombre']} (solicitado: {$ps['solicitado']}, disponible: {$ps['disponible']})";
            }
            echo json_encode([
                'error' => $mensaje . implode(', ', $detalles),
                'productos_sin_stock' => $productos_sin_stock
            ]);
            exit;
        }

        // Obtener datos del cliente desde el payload
        $cliente_nombre = $data['cliente_nombre'] ?? 'Cliente AnÃ³nimo';
        $cliente_rut = $data['cliente_rut'] ?? '';
        $cliente_telefono = $data['cliente_telefono'] ?? '';
        $cliente_email = $data['cliente_email'] ?? '';
        $cliente_notas = $data['cliente_notas'] ?? '';

        // Crear o buscar cliente
        $id_cliente = null;
        if (!empty($cliente_rut)) {
            // Buscar cliente por RUT
            $cliente_existente = $this->db->where('rut', $cliente_rut)->get('clientes')->row();
            if ($cliente_existente) {
                $id_cliente = $cliente_existente->id_cliente;
                // Actualizar datos del cliente
                $this->db->where('id_cliente', $id_cliente)->update('clientes', [
                    'nombre' => $cliente_nombre,
                    'telefono' => $cliente_telefono,
                    'email' => $cliente_email
                ]);
            }
        }
        
        if (empty($id_cliente)) {
            // Crear nuevo cliente
            $this->db->insert('clientes', [
                'nombre' => $cliente_nombre,
                'rut' => $cliente_rut,
                'telefono' => $cliente_telefono,
                'email' => $cliente_email
            ]);
            $id_cliente = $this->db->insert_id();
        }

        $id_mesa = $this->session->userdata('id_mesa') ?? ($data['id_mesa'] ?? null);
        $id_sucursal = $this->session->userdata('id_sucursal') ?? ($data['id_sucursal'] ?? null);

        $id = $this->Pedido_model->crear_pedido($id_cliente, $data['detalle'], $id_mesa, $id_sucursal, $cliente_notas);

        if ($id) {
            echo json_encode(['ok' => true, 'id_pedido' => $id]);
            exit;
        } else {
            echo json_encode(['error' => 'No se pudo crear el pedido']);
            exit;
        }
    }
}
