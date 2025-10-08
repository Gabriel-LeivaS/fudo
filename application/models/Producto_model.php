<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Producto_model extends CI_Model {

    public function obtener_por_categoria($id_categoria, $id_sucursal = null) {
        $this->db->where('id_categoria', $id_categoria)
                 ->where('disponible', TRUE);
        if ($id_sucursal !== null) {
            $this->db->where('id_sucursal', $id_sucursal);
        }
        return $this->db->order_by('nombre', 'ASC')
                        ->get('productos')
                        ->result();
    }

    public function obtener_detalle($id_producto) {
        return $this->db->where('id_producto', $id_producto)
                        ->get('productos')
                        ->row();
    }

    public function obtener_todos($id_sucursal = null) {
        $this->db->select('p.*, c.nombre as nombre_categoria, s.nombre as nombre_sucursal');
        $this->db->from('productos p');
        $this->db->join('categorias c', 'c.id_categoria = p.id_categoria', 'left');
        $this->db->join('sucursales s', 's.id_sucursal = p.id_sucursal', 'left');
        
        if ($id_sucursal !== null) {
            $this->db->where('p.id_sucursal', $id_sucursal);
        }
        
        $this->db->order_by('p.nombre', 'ASC');
        return $this->db->get()->result();
    }

    public function obtener_por_id($id_producto) {
        return $this->db->where('id_producto', $id_producto)
                        ->get('productos')
                        ->row();
    }

    public function crear($datos) {
        return $this->db->insert('productos', $datos);
    }

    public function actualizar($id_producto, $datos) {
        return $this->db->where('id_producto', $id_producto)
                        ->update('productos', $datos);
    }

    public function eliminar($id_producto) {
        return $this->db->where('id_producto', $id_producto)
                        ->delete('productos');
    }

    public function cambiar_disponibilidad($id_producto, $disponible) {
        return $this->db->where('id_producto', $id_producto)
                        ->update('productos', ['disponible' => $disponible]);
    }
}

