<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mesa_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Obtener mesa por ID con información de sucursal
     */
    public function obtener_por_id($id_mesa) {
        $this->db->select('m.*, s.nombre as nombre_sucursal, s.direccion, s.telefono');
        $this->db->from('mesas m');
        $this->db->join('sucursales s', 's.id_sucursal = m.id_sucursal', 'left');
        $this->db->where('m.id_mesa', $id_mesa);
        return $this->db->get()->row();
    }

    /**
     * Obtener todas las mesas de una sucursal
     */
    public function obtener_por_sucursal($id_sucursal) {
        $this->db->where('id_sucursal', $id_sucursal);
        $this->db->order_by('nombre', 'ASC');
        return $this->db->get('mesas')->result();
    }

    /**
     * Obtener todas las mesas
     */
    public function obtener_todas() {
        $this->db->select('m.*, s.nombre as nombre_sucursal');
        $this->db->from('mesas m');
        $this->db->join('sucursales s', 's.id_sucursal = m.id_sucursal', 'left');
        $this->db->order_by('m.id_sucursal, m.nombre', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Crear nueva mesa
     */
    public function crear($datos) {
        return $this->db->insert('mesas', $datos);
    }

    /**
     * Actualizar mesa
     */
    public function actualizar($id_mesa, $datos) {
        $this->db->where('id_mesa', $id_mesa);
        return $this->db->update('mesas', $datos);
    }

    /**
     * Eliminar mesa
     */
    public function eliminar($id_mesa) {
        $this->db->where('id_mesa', $id_mesa);
        return $this->db->delete('mesas');
    }
}
