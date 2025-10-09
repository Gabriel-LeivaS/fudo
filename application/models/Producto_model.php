<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Producto_model extends CI_Model {

    public function obtener_por_categoria($id_categoria, $id_sucursal = null) {
        $this->db->select('p.*')
                 ->from('productos p')
                 ->join('categorias c', 'c.id_categoria = p.id_categoria', 'inner')
                 ->where('p.id_categoria', $id_categoria)
                 ->where('p.disponible', TRUE)
                 ->where('c.estado', TRUE); // Solo productos de categorías activas
        
        if ($id_sucursal !== null) {
            $this->db->where('p.id_sucursal', $id_sucursal);
        }
        
        return $this->db->order_by('p.nombre', 'ASC')
                        ->get()
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
        $result = $this->db->get()->result();
        
        // Convertir explícitamente el campo disponible a booleano
        foreach ($result as $prod) {
            $prod->disponible = ($prod->disponible === 't' || $prod->disponible === 'true' || $prod->disponible === true || $prod->disponible === 1 || $prod->disponible === '1');
        }
        
        return $result;
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
        // Convertir explícitamente a booleano para PostgreSQL
        $disponible_bool = ($disponible === true || $disponible === 'true' || $disponible === 1 || $disponible === '1') ? true : false;
        
        return $this->db->where('id_producto', $id_producto)
                        ->update('productos', ['disponible' => $disponible_bool]);
    }

    /**
     * Verificar si un producto está disponible para compra
     * (producto disponible Y categoría activa)
     */
    public function esta_disponible($id_producto) {
        $this->db->select('p.disponible, c.estado as categoria_activa')
                 ->from('productos p')
                 ->join('categorias c', 'c.id_categoria = p.id_categoria', 'inner')
                 ->where('p.id_producto', $id_producto);
        
        $producto = $this->db->get()->row();
        
        if (!$producto) {
            return false;
        }
        
        // El producto está disponible si está marcado como disponible Y su categoría está activa
        $disponible = ($producto->disponible === 't' || $producto->disponible === 'true' || $producto->disponible === true || $producto->disponible === 1 || $producto->disponible === '1');
        $categoria_activa = ($producto->categoria_activa === 't' || $producto->categoria_activa === 'true' || $producto->categoria_activa === true || $producto->categoria_activa === 1 || $producto->categoria_activa === '1');
        
        return $disponible && $categoria_activa;
    }

    /**
     * Reducir stock de un producto
     * @param int $id_producto ID del producto
     * @param int $cantidad Cantidad a reducir
     * @return bool True si se redujo correctamente, False si no hay stock suficiente
     */
    public function reducir_stock($id_producto, $cantidad) {
        $producto = $this->obtener_por_id($id_producto);
        
        if (!$producto) {
            return false;
        }
        
        $stock_actual = isset($producto->stock) ? (int)$producto->stock : 0;
        $nuevo_stock = $stock_actual - $cantidad;
        
        if ($nuevo_stock < 0) {
            return false; // No hay stock suficiente
        }
        
        return $this->db->where('id_producto', $id_producto)
                        ->update('productos', ['stock' => $nuevo_stock]);
    }

    /**
     * Aumentar stock de un producto
     * @param int $id_producto ID del producto
     * @param int $cantidad Cantidad a aumentar
     * @return bool True si se aumentó correctamente
     */
    public function aumentar_stock($id_producto, $cantidad) {
        $producto = $this->obtener_por_id($id_producto);
        
        if (!$producto) {
            return false;
        }
        
        $stock_actual = isset($producto->stock) ? (int)$producto->stock : 0;
        $nuevo_stock = $stock_actual + $cantidad;
        
        return $this->db->where('id_producto', $id_producto)
                        ->update('productos', ['stock' => $nuevo_stock]);
    }
}
