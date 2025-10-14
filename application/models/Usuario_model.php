<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Verificar usuario y retornar datos completos incluyendo rol y sucursal
     */
    public function verificar_usuario($usuario, $contrasena) {
        $query = $this->db->query(
            "SELECT u.*, s.nombre as nombre_sucursal 
             FROM usuarios_admin u 
             LEFT JOIN sucursales s ON u.id_sucursal = s.id_sucursal
             WHERE u.usuario = ? AND u.activo = TRUE AND u.contrasena = crypt(?, u.contrasena)",
            array($usuario, $contrasena)
        );
        return $query->row();
    }

    /**
     * Obtener todos los usuarios
     */
    public function obtener_todos() {
        return $this->db->select('u.*, u.id as id_usuario, s.nombre as nombre_sucursal')
                        ->from('usuarios_admin u')
                        ->join('sucursales s', 'u.id_sucursal = s.id_sucursal', 'left')
                        ->order_by('u.nombre_completo', 'ASC')
                        ->get()
                        ->result();
    }

    /**
     * Obtener usuarios por rol
     */
    public function obtener_por_rol($rol) {
        return $this->db->select('u.*, u.id as id_usuario, s.nombre as nombre_sucursal')
                        ->from('usuarios_admin u')
                        ->join('sucursales s', 'u.id_sucursal = s.id_sucursal', 'left')
                        ->where('u.rol', $rol)
                        ->order_by('u.nombre_completo', 'ASC')
                        ->get()
                        ->result();
    }

    /**
     * Obtener usuarios de una sucursal específica
     */
    public function obtener_por_sucursal($id_sucursal) {
        return $this->db->select('u.*, u.id as id_usuario, s.nombre as nombre_sucursal')
                        ->from('usuarios_admin u')
                        ->join('sucursales s', 'u.id_sucursal = s.id_sucursal', 'left')
                        ->where('u.id_sucursal', $id_sucursal)
                        ->order_by('u.nombre_completo', 'ASC')
                        ->get()
                        ->result();
    }

    /**
     * Obtener un usuario por ID
     */
    public function obtener_por_id($id) {
        return $this->db->select('u.*, u.id as id_usuario, s.nombre as nombre_sucursal')
                        ->from('usuarios_admin u')
                        ->join('sucursales s', 'u.id_sucursal = s.id_sucursal', 'left')
                        ->where('u.id', $id)
                        ->get()
                        ->row();
    }

    /**
     * Crear un nuevo usuario admin
     */
    public function crear($datos) {
        // Encriptar contraseña si viene en texto plano
        if (isset($datos['contrasena'])) {
            $this->db->query("SET search_path TO public");
            $hash_result = $this->db->query("SELECT crypt(?, gen_salt('bf')) as hash", array($datos['contrasena']))->row();
            $datos['contrasena'] = $hash_result->hash;
        }
        
        return $this->db->insert('usuarios_admin', $datos);
    }

    /**
     * Actualizar un usuario existente
     */
    public function actualizar($id, $datos) {
        
        // Si viene contraseña nueva, encriptarla
        if (isset($datos['contrasena']) && !empty($datos['contrasena'])) {
            $this->db->query("SET search_path TO public");
            $hash_result = $this->db->query("SELECT crypt(?, gen_salt('bf')) as hash", array($datos['contrasena']))->row();
            $datos['contrasena'] = $hash_result->hash;
        } else {
            // Si no hay contraseña nueva, no actualizar ese campo
            unset($datos['contrasena']);
        }
        
        $result = $this->db->where('id', $id)
                           ->update('usuarios_admin', $datos);
        
        return $result;
    }

    /**
     * Eliminar un usuario
     */
    public function eliminar($id) {
        return $this->db->where('id', $id)
                        ->delete('usuarios_admin');
    }

    /**
     * Cambiar estado de un usuario
     */
    public function cambiar_estado($id, $estado) {
        return $this->db->where('id', $id)
                        ->update('usuarios_admin', ['activo' => $estado]);
    }

    /**
     * Verificar si existe un usuario con ese username
     */
    public function existe_usuario($usuario, $excluir_id = null) {
        $this->db->where('usuario', $usuario);
        if ($excluir_id) {
            $this->db->where('id !=', $excluir_id);
        }
        return $this->db->count_all_results('usuarios_admin') > 0;
    }

    /**
     * Verificar si existe un email
     */
    public function existe_email($email, $excluir_id = null) {
        $this->db->where('email', $email);
        if ($excluir_id) {
            $this->db->where('id !=', $excluir_id);
        }
        return $this->db->count_all_results('usuarios_admin') > 0;
    }
}

