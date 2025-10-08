<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sucursal_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Obtener todas las sucursales
     */
    public function obtener_todas() {
        return $this->db->order_by('nombre', 'ASC')
                        ->get('sucursales')
                        ->result();
    }

    /**
     * Obtener solo sucursales activas
     */
    public function obtener_activas() {
        return $this->db->where('activo', true)
                        ->order_by('nombre', 'ASC')
                        ->get('sucursales')
                        ->result();
    }

    /**
     * Obtener una sucursal por ID
     */
    public function obtener_por_id($id_sucursal) {
        return $this->db->where('id_sucursal', $id_sucursal)
                        ->get('sucursales')
                        ->row();
    }

    /**
     * Crear una nueva sucursal
     */
    public function crear($datos) {
        return $this->db->insert('sucursales', $datos);
    }

    /**
     * Actualizar una sucursal existente
     */
    public function actualizar($id_sucursal, $datos) {
        return $this->db->where('id_sucursal', $id_sucursal)
                        ->update('sucursales', $datos);
    }

    /**
     * Eliminar una sucursal
     */
    public function eliminar($id_sucursal) {
        return $this->db->where('id_sucursal', $id_sucursal)
                        ->delete('sucursales');
    }

    /**
     * Cambiar estado de una sucursal (activo/inactivo)
     */
    public function cambiar_estado($id_sucursal, $estado) {
        return $this->db->where('id_sucursal', $id_sucursal)
                        ->update('sucursales', ['activo' => $estado]);
    }

    /**
     * Verificar si una sucursal tiene usuarios asignados
     */
    public function tiene_usuarios($id_sucursal) {
        $count = $this->db->where('id_sucursal', $id_sucursal)
                          ->where('activo', true)
                          ->count_all_results('usuarios_admin');
        return $count > 0;
    }

    /**
     * Verificar si una sucursal tiene mesas asignadas
     */
    public function tiene_mesas($id_sucursal) {
        $count = $this->db->where('id_sucursal', $id_sucursal)
                          ->count_all_results('mesas');
        return $count > 0;
    }

    /**
     * Obtener estadísticas de una sucursal
     */
    public function obtener_estadisticas($id_sucursal) {
        $stats = [];
        
        // Total de categorías
        $stats['total_categorias'] = $this->db->where('id_sucursal', $id_sucursal)
                                               ->where('estado', true)
                                               ->count_all_results('categorias');
        
        // Total de productos
        $stats['total_productos'] = $this->db->where('id_sucursal', $id_sucursal)
                                              ->where('disponible', true)
                                              ->count_all_results('productos');
        
        // Total de mesas
        $stats['total_mesas'] = $this->db->where('id_sucursal', $id_sucursal)
                                          ->count_all_results('mesas');
        
        // Total de pedidos hoy
        $stats['pedidos_hoy'] = $this->db->where('id_sucursal', $id_sucursal)
                                          ->where('DATE(fecha)', date('Y-m-d'))
                                          ->count_all_results('pedidos');
        
        return $stats;
    }
}
