<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categoria_model extends CI_Model {

    public function obtener_categorias_activas($id_sucursal = null) {
        $this->db->where('estado', TRUE);
        if ($id_sucursal !== null) {
            $this->db->where('id_sucursal', $id_sucursal);
        }
        return $this->db->order_by('nombre', 'ASC')
                        ->get('categorias')
                        ->result();
    }

    public function obtener_todas($id_sucursal = null) {
        $this->db->select('c.*, s.nombre as nombre_sucursal');
        $this->db->from('categorias c');
        $this->db->join('sucursales s', 's.id_sucursal = c.id_sucursal', 'left');
        
        if ($id_sucursal !== null) {
            $this->db->where('c.id_sucursal', $id_sucursal);
        }
        
        $this->db->order_by('c.nombre', 'ASC');
        return $this->db->get()->result();
    }

    public function obtener_por_id($id_categoria) {
        return $this->db->where('id_categoria', $id_categoria)
                        ->get('categorias')
                        ->row();
    }

    public function crear($datos) {
        return $this->db->insert('categorias', $datos);
    }

    public function actualizar($id_categoria, $datos) {
        return $this->db->where('id_categoria', $id_categoria)
                        ->update('categorias', $datos);
    }

    public function eliminar($id_categoria) {
        return $this->db->where('id_categoria', $id_categoria)
                        ->delete('categorias');
    }

    public function cambiar_estado($id_categoria, $estado) {
        return $this->db->where('id_categoria', $id_categoria)
                        ->update('categorias', ['estado' => $estado]);
    }

    public function tiene_productos($id_categoria) {
        $count = $this->db->where('id_categoria', $id_categoria)
                          ->count_all_results('productos');
        return $count > 0;
    }
}

