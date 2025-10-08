<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Carta extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['Categoria_model','Producto_model','Mesa_model']);
        $this->load->database();
        $this->load->helper('url');
        $this->load->library('session');
    }

    public function index() {
        // aceptar id_mesa como segmento (ruta /mesa/1) o como query string ?id_mesa=1
        $id_mesa = $this->uri->segment(2) ?: $this->input->get('id_mesa');
        if (!$id_mesa) {
            show_error('Mesa no especificada', 400);
            return;
        }
        
        // Obtener información de la mesa con su sucursal
        $mesa = $this->Mesa_model->obtener_por_id($id_mesa);
        if (!$mesa) {
            show_error('Mesa no encontrada', 404);
            return;
        }
        
        // Verificar que la mesa tenga sucursal asignada
        if (!$mesa->id_sucursal) {
            show_error('Mesa sin sucursal asignada', 500);
            return;
        }
        
        // Guardar en sesión para que el carrito/checkout lo use
        $this->session->set_userdata('id_mesa', $id_mesa);
        $this->session->set_userdata('id_sucursal', $mesa->id_sucursal);
        $this->session->set_userdata('nombre_sucursal', $mesa->nombre_sucursal);

        // Obtener solo las categorías activas de la sucursal de la mesa
        $data['categorias'] = $this->Categoria_model->obtener_categorias_activas($mesa->id_sucursal);
        $data['mesa'] = $mesa;
        $data['id_mesa'] = $id_mesa;
        
        // Cargar productos por cada categoría (solo de esta sucursal)
        $productos_por_categoria = [];
        foreach ($data['categorias'] as $cat) {
            $productos_por_categoria[$cat->id_categoria] = $this->Producto_model->obtener_por_categoria($cat->id_categoria, $mesa->id_sucursal);
        }
        $data['productos_por_categoria'] = $productos_por_categoria;
        
        // Cargar vista interactiva de menú
        $this->load->view('carta/index', $data);
    }

    // endpoint AJAX para devolver el partial de productos de una categoría
    public function productos_ajax($id_categoria) {
        // Obtener id_sucursal de la sesión
        $id_sucursal = $this->session->userdata('id_sucursal');
        $productos = $this->Producto_model->obtener_por_categoria($id_categoria, $id_sucursal);
        $this->load->view('carta/_productos_list', ['productos' => $productos]);
    }

    public function productos($id_categoria) {
        // Obtener id_sucursal de la sesión
        $id_sucursal = $this->session->userdata('id_sucursal');
        $data['productos'] = $this->Producto_model->obtener_por_categoria($id_categoria, $id_sucursal);
        $this->load->view('carta/productos', $data);
    }
}
