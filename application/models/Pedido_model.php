<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pedido_model extends CI_Model {

    /**
     * Crear pedido
     * @param int $id_cliente
     * @param array $detalle
     * @param int|null $id_mesa
     * @param int|null $id_sucursal
     * @return int|false id_pedido o FALSE
     */
    public function crear_pedido($id_cliente, $detalle, $id_mesa = NULL, $id_sucursal = NULL, $notas = NULL) {
        $this->db->trans_start();

        $total = 0;
        foreach ($detalle as $item) {
            $total += $item['subtotal'];
        }

        $insert = [
            'id_cliente' => $id_cliente,
            'estado' => 'Pendiente',
            'total' => $total
        ];

        // incluir id_mesa si se proporciona
        if (!is_null($id_mesa)) {
            $insert['id_mesa'] = $id_mesa;
        }

        // incluir id_sucursal si se proporciona
        if (!is_null($id_sucursal)) {
            $insert['id_sucursal'] = $id_sucursal;
        }
        
        // incluir notas si se proporciona
        if (!is_null($notas) && !empty($notas)) {
            $insert['notas'] = $notas;
        }

        $this->db->insert('pedidos', $insert);

        $id_pedido = $this->db->insert_id();

        foreach ($detalle as $item) {
            $this->db->insert('detalle_pedido', [
                'id_pedido' => $id_pedido,
                'id_producto' => $item['id_producto'],
                'cantidad' => $item['cantidad'],
                'subtotal' => $item['subtotal']
            ]);
            
            // Reducir stock del producto
            $this->load->model('Producto_model');
            $this->Producto_model->reducir_stock($item['id_producto'], $item['cantidad']);
        }

        $this->db->trans_complete();

        return $this->db->trans_status() ? $id_pedido : FALSE;
    }

    public function obtener_pedidos_pendientes($id_sucursal = null) {
        // Mostrar pedidos Pendientes y En preparación (no los que están Listos)
        $this->db->where_in('estado', ['Pendiente', 'En preparación']);
        if ($id_sucursal !== null) {
            $this->db->where('id_sucursal', $id_sucursal);
        }
        return $this->db->order_by('fecha', 'DESC')
                        ->get('pedidos')
                        ->result();
    }

    public function obtener_detalle_pedido($id_pedido) {
        $this->db->select('p.nombre, p.precio, d.cantidad, d.subtotal');
        $this->db->from('detalle_pedido d');
        $this->db->join('productos p', 'p.id_producto = d.id_producto');
        $this->db->where('d.id_pedido', $id_pedido);
        return $this->db->get()->result();
    }

    public function actualizar_estado($id_pedido, $nuevo_estado) {
        $this->db->where('id_pedido', $id_pedido);
        return $this->db->update('pedidos', ['estado' => $nuevo_estado]);
    }

    /**
     * Obtener pedidos activos de una mesa (no completados)
     * @param int $id_mesa
     * @return array
     */
    public function obtener_pedidos_activos_mesa($id_mesa) {
        $this->db->select('p.*, c.nombre as cliente_nombre');
        $this->db->from('pedidos p');
        $this->db->join('clientes c', 'c.id_cliente = p.id_cliente', 'left');
        $this->db->where('p.id_mesa', $id_mesa);
        $this->db->where_in('p.estado', ['Pendiente', 'En preparación', 'Lista']);
        $this->db->order_by('p.fecha', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Obtener total acumulado de pedidos activos de una mesa
     * @param int $id_mesa
     * @return float
     */
    public function obtener_total_mesa($id_mesa) {
        $this->db->select_sum('total');
        $this->db->where('id_mesa', $id_mesa);
        $this->db->where_in('estado', ['Pendiente', 'En preparación', 'Lista']);
        $result = $this->db->get('pedidos')->row();
        return $result->total ?? 0;
    }

    /**
     * Marcar todos los pedidos de una mesa como completados (para cobro)
     * @param int $id_mesa
     * @return bool
     */
    public function completar_pedidos_mesa($id_mesa) {
        $this->db->where('id_mesa', $id_mesa);
        $this->db->where_in('estado', ['Pendiente', 'En preparación', 'Lista']);
        return $this->db->update('pedidos', ['estado' => 'Completado']);
    }
}
