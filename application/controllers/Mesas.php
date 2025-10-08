<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mesas extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->helper(['url']);
        $this->load->database();
        $this->load->library('session');
        $this->load->library('ciqrcode');
    }

    public function generar_qr($id_mesa){
        // comprobar existencia
        $mesa = $this->db->where('id_mesa', $id_mesa)->get('mesas')->row();
        if (!$mesa) {
            // si es AJAX, devolver JSON
            if ($this->input->is_ajax_request()) {
                return $this->output->set_content_type('application/json')->set_output(json_encode(['success'=>false,'message'=>'Mesa no encontrada']));
            }
            show_error('Mesa no encontrada', 404);
            return;
        }

        $url = site_url('carta/index?id_mesa='.$id_mesa);
        $path = 'assets/qr/mesa_'.$id_mesa.'.png';

        $params['data'] = $url;
        $params['level'] = 'H';
        $params['size'] = 8;
        $params['savename'] = FCPATH.$path;

        try {
            $this->ciqrcode->generate($params);
            // actualizar BD
            $this->db->where('id_mesa',$id_mesa);
            $this->db->update('mesas',['codigo_qr'=>$path]);

            if ($this->input->is_ajax_request()) {
                return $this->output->set_content_type('application/json')->set_output(json_encode(['success'=>true,'path'=>$path,'message'=>'QR generado']));
            }

            echo "QR generado en: ".$path;
        } catch (Exception $e) {
            if ($this->input->is_ajax_request()) {
                return $this->output->set_content_type('application/json')->set_output(json_encode(['success'=>false,'message'=>$e->getMessage()]));
            }
            show_error('Error generando QR: '.$e->getMessage(), 500);
        }
    }

    public function listar(){
        $mesas = $this->db->get('mesas')->result();
        $this->load->view('admin/mesas', ['mesas'=>$mesas]);
    }
}
