<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mesas extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->helper(['url']);
        $this->load->database();
        $this->load->library('session');
        $this->load->library('ciqrcode');
        $this->load->model('Mesa_model');
        
        // Verificar que esté logueado
        if(!$this->session->userdata('logueado')) {
            redirect('login');
        }
        
        $this->rol = $this->session->userdata('rol');
        $this->id_sucursal = $this->session->userdata('id_sucursal');
        $this->permisos = $this->session->userdata('permisos');
    }
    
    /**
     * Verificar si el usuario tiene permiso para mesas
     */
    private function tiene_permiso_mesas() {
        if($this->rol == 'admin' || $this->rol == 'admin_sucursal') {
            return true;
        }
        if($this->rol == 'usuario' && is_array($this->permisos)) {
            return isset($this->permisos['mesas']) && $this->permisos['mesas'] === true;
        }
        return false;
    }

    public function index() {
        // Verificar permisos
        if(!$this->tiene_permiso_mesas()) {
            show_error('No tienes permisos para acceder a esta sección', 403);
            return;
        }
        
        // Obtener mesas según el rol
        if($this->rol == 'admin_sucursal' || $this->rol == 'usuario') {
            $data['mesas'] = $this->Mesa_model->obtener_por_sucursal($this->id_sucursal);
        } else {
            $data['mesas'] = $this->Mesa_model->obtener_todas();
        }
        
        // Verificar estado de cada mesa (si tiene pedidos activos)
        foreach($data['mesas'] as $mesa) {
            $mesa->ocupada = $this->verificar_mesa_ocupada($mesa->id_mesa);
        }
        
        // Ordenar las mesas solo por número (independiente del estado)
        usort($data['mesas'], function($a, $b) {
            // Extraer el número del nombre de la mesa (ej: "Mesa 5" -> 5)
            preg_match('/\d+/', $a->nombre, $numA);
            preg_match('/\d+/', $b->nombre, $numB);
            
            if(!empty($numA) && !empty($numB)) {
                return (int)$numA[0] - (int)$numB[0];
            }
            
            // Si no tienen números, ordenar alfabéticamente
            return strcasecmp($a->nombre, $b->nombre);
        });
        
        $data['rol'] = $this->rol;
        $data['id_sucursal'] = $this->id_sucursal;
        
        $this->load->view('admin/mesas', $data);
    }

    public function crear() {
        header('Content-Type: application/json');
        
        // Validar que no sea rol usuario (solo lectura)
        if($this->rol == 'usuario') {
            echo json_encode(['success' => false, 'message' => 'No tienes permisos para crear mesas (solo lectura)']);
            exit;
        }
        
        $id_sucursal = $this->input->post('id_sucursal');
        
        if(empty($id_sucursal)) {
            echo json_encode(['success' => false, 'message' => 'Sucursal no especificada']);
            return;
        }
        
        // Verificar permisos
        if($this->rol == 'admin_sucursal' && $id_sucursal != $this->id_sucursal) {
            echo json_encode(['success' => false, 'message' => 'No tiene permisos para crear mesas en esta sucursal']);
            return;
        }
        
        // Obtener el siguiente número de mesa para esta sucursal
        $this->db->select('nombre');
        $this->db->where('id_sucursal', $id_sucursal);
        $this->db->like('nombre', 'Mesa ', 'after');
        $this->db->order_by('id_mesa', 'DESC');
        $ultima_mesa = $this->db->get('mesas', 1)->row();
        
        $siguiente_numero = 1;
        if($ultima_mesa) {
            // Extraer el número de la última mesa
            preg_match('/\d+/', $ultima_mesa->nombre, $matches);
            if(!empty($matches)) {
                $siguiente_numero = (int)$matches[0] + 1;
            }
        }
        
        $nombre = 'Mesa ' . $siguiente_numero;
        
        // Verificar que no exista una mesa con ese nombre en la sucursal
        $existe = $this->db->where('nombre', $nombre)
                           ->where('id_sucursal', $id_sucursal)
                           ->get('mesas')
                           ->num_rows();
        
        if($existe > 0) {
            // Si existe, buscar el siguiente número disponible
            $num = $siguiente_numero;
            do {
                $num++;
                $nombre = 'Mesa ' . $num;
                $existe = $this->db->where('nombre', $nombre)
                                   ->where('id_sucursal', $id_sucursal)
                                   ->get('mesas')
                                   ->num_rows();
            } while($existe > 0);
        }
        
        $data = [
            'nombre' => $nombre,
            'id_sucursal' => $id_sucursal
        ];
        
        if($this->Mesa_model->crear($data)) {
            echo json_encode(['success' => true, 'message' => 'Mesa "' . $nombre . '" creada exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al crear la mesa']);
        }
    }

    public function eliminar($id_mesa) {
        header('Content-Type: application/json');
        
        // Validar que no sea rol usuario (solo lectura)
        if($this->rol == 'usuario') {
            echo json_encode(['success' => false, 'message' => 'No tienes permisos para eliminar mesas (solo lectura)']);
            exit;
        }
        
        // Verificar que la mesa exista
        $mesa = $this->Mesa_model->obtener_por_id($id_mesa);
        if(!$mesa) {
            echo json_encode(['success' => false, 'message' => 'Mesa no encontrada']);
            return;
        }
        
        // Verificar permisos
        if($this->rol == 'admin_sucursal' && $mesa->id_sucursal != $this->id_sucursal) {
            echo json_encode(['success' => false, 'message' => 'No tiene permisos para eliminar esta mesa']);
            return;
        }
        
        // Verificar que no tenga pedidos activos
        if($this->verificar_mesa_ocupada($id_mesa)) {
            echo json_encode(['success' => false, 'message' => 'No se puede eliminar una mesa con pedidos activos']);
            return;
        }
        
        if($this->Mesa_model->eliminar($id_mesa)) {
            echo json_encode(['success' => true, 'message' => 'Mesa eliminada exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar la mesa']);
        }
    }

    public function liberar($id_mesa) {
        header('Content-Type: application/json');
        
        // Validar que no sea rol usuario (solo lectura)
        if($this->rol == 'usuario') {
            echo json_encode(['success' => false, 'message' => 'No tienes permisos para liberar mesas (solo lectura)']);
            exit;
        }
        
        // Verificar que la mesa existe
        $mesa = $this->Mesa_model->obtener_por_id($id_mesa);
        if(!$mesa) {
            echo json_encode(['success' => false, 'message' => 'Mesa no encontrada']);
            return;
        }
        
        // Verificar permisos
        if($this->rol == 'admin_sucursal' && $mesa->id_sucursal != $this->id_sucursal) {
            echo json_encode(['success' => false, 'message' => 'No tiene permisos para liberar esta mesa']);
            return;
        }
        
        // Cambiar todos los pedidos activos de la mesa a estado "Completado"
        $this->db->where('id_mesa', $id_mesa);
        $this->db->where_in('estado', ['Pendiente', 'En preparación', 'Lista']);
        $resultado = $this->db->update('pedidos', ['estado' => 'Completado']);
        
        if($resultado) {
            echo json_encode(['success' => true, 'message' => 'Mesa liberada exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al liberar la mesa']);
        }
    }

    private function verificar_mesa_ocupada($id_mesa) {
        // Una mesa está ocupada si tiene pedidos en estado Pendiente o En preparación
        $this->db->where('id_mesa', $id_mesa);
        $this->db->where_in('estado', ['Pendiente', 'En preparación']);
        $pedidos = $this->db->get('pedidos')->num_rows();
        return $pedidos > 0;
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

        $url = site_url('mesa/'.$id_mesa);
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
