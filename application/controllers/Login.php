<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Usuario_model');
        $this->load->library('session');
        $this->load->helper(['url','form']);
    }

    public function index() {
        $this->load->view('admin/login');
    }

    public function acceder() {
        $usuario = $this->input->post('usuario');
        $contrasena = $this->input->post('contrasena');

        $u = $this->Usuario_model->verificar_usuario($usuario,$contrasena);
        if($u){
            // Verificar si la sucursal está activa (solo para usuarios no admin)
            if($u->rol != 'admin' && isset($u->sucursal_activa) && $u->sucursal_activa === 'f') {
                $this->session->set_flashdata('error','Su sucursal está desactivada. Contacte al administrador.');
                redirect('login');
                return;
            }
            
            // Parsear permisos si es rol usuario
            $permisos = null;
            if($u->rol == 'usuario' && !empty($u->permisos)) {
                $permisos = json_decode($u->permisos, true);
            }
            
            // Guardar datos completos en sesión incluyendo rol, sucursal y permisos
            $session_data = [
                'logueado' => TRUE,
                'id_usuario' => $u->id,
                'usuario' => $u->usuario,
                'nombre_completo' => $u->nombre_completo,
                'email' => $u->email,
                'rol' => $u->rol,
                'id_sucursal' => $u->id_sucursal,
                'nombre_sucursal' => $u->nombre_sucursal,
                'permisos' => $permisos
            ];
            $this->session->set_userdata($session_data);
            redirect('admin');
        }else{
            $this->session->set_flashdata('error','Usuario o contraseña incorrecta');
            redirect('login');
        }
    }

    public function salir() {
        $this->session->sess_destroy();
        redirect('login');
    }
}
