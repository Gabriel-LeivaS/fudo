<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dev_qr extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Cargar la librería wrapper
        $this->load->library('Ciqrcode');
        // Cargar helper URL para site_url()/base_url()
        $this->load->helper('url');
    }

    /**
     * Genera un QR de prueba y devuelve la ruta del archivo.
     * Acceder vía: /index.php/dev_qr/generar
     */
    public function generar() {
        $data = site_url('carta');
        $outfile = FCPATH . 'assets/qr/mesa_test.png';
        try {
            $this->ciqrcode->generate(['data' => $data, 'savename' => $outfile, 'level' => 'H', 'size' => 6]);
            echo "QR generado: assets/qr/mesa_test.png";
        } catch (Exception $e) {
            echo "Error generando QR: " . $e->getMessage();
        }
    }

}
