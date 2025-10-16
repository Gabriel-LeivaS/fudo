<?php
$rol = $this->session->userdata('rol');
$permisos = $this->session->userdata('permisos');

// Función helper para verificar permisos
$tiene_permiso = function($seccion) use ($rol, $permisos) {
    // Super admin: Solo acceso a secciones administrativas
    if($rol == 'admin') {
        return in_array($seccion, ['categorias', 'productos', 'usuarios', 'sucursales']);
    }
    // Pedidos: Solo admin_sucursal y usuarios con permiso
    if($seccion == 'pedidos') {
        return $rol == 'admin_sucursal' || ($rol == 'usuario' && is_array($permisos) && isset($permisos['pedidos']) && $permisos['pedidos'] === true);
    }
    // Admin sucursal: acceso completo
    if($rol == 'admin_sucursal') return true;
    // Usuarios: permisos granulares
    if($rol == 'usuario' && is_array($permisos)) {
        return isset($permisos[$seccion]) && $permisos[$seccion] === true;
    }
    return false;
};

// Determinar página activa
$current_page = isset($active_page) ? $active_page : '';
?>

<!-- Navbar Superior -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        <!-- Logo -->
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?= site_url('admin') ?>">
            <span class="navbar-brand">🍽️ FUDO</span>
        </a>

        <!-- Usuario logeado + Menú -->
        <div class="d-flex align-items-center gap-3">
            
            <!-- Usuario visible -->
            <div class="user-info-navbar d-flex align-items-center gap-2">
                <i class="bi bi-person-circle"></i>
                <div class="user-details d-flex flex-column align-items-start">
                    <span class="username"><?= $this->session->userdata('nombre_completo') ?: $this->session->userdata('nombre') ?></span>
                    <?php if($this->session->userdata('nombre_sucursal')): ?>
                        <span class="user-sucursal"><?= $this->session->userdata('nombre_sucursal') ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Menú desplegable -->
            <div class="dropdown">
                <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-list me-1"></i> Menú
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg p-3" style="min-width: 250px;">

                    <!-- Enlaces del panel -->
                    <?php if($tiene_permiso('pedidos')): ?>
                        <li><a class="dropdown-item <?= $current_page == 'pedidos' ? 'active' : '' ?>" href="<?= site_url('admin') ?>">
                            <span class="dropdown-icon">📦</span>Pedidos
                        </a></li>
                    <?php endif; ?>
                    
                    <?php if($tiene_permiso('categorias')): ?>
                        <li><a class="dropdown-item <?= $current_page == 'categorias' ? 'active' : '' ?>" href="<?= site_url('admin/categorias') ?>">
                            <span class="dropdown-icon">🏷️</span>Categorías
                        </a></li>
                    <?php endif; ?>
                    
                    <?php if($tiene_permiso('productos')): ?>
                        <li><a class="dropdown-item <?= $current_page == 'productos' ? 'active' : '' ?>" href="<?= site_url('admin/productos') ?>">
                            <span class="dropdown-icon">🛍️</span>Productos
                        </a></li>
                    <?php endif; ?>
                    
                    <?php if($tiene_permiso('mi_carta')): ?>
                        <li><a class="dropdown-item <?= $current_page == 'mi_carta' ? 'active' : '' ?>" href="<?= site_url('admin/mi_carta') ?>">
                            <span class="dropdown-icon">📋</span>Mi Carta
                        </a></li>
                    <?php endif; ?>
                    
                    <?php if($tiene_permiso('mesas')): ?>
                        <li><a class="dropdown-item <?= $current_page == 'mesas' ? 'active' : '' ?>" href="<?= site_url('mesas') ?>">
                            <span class="dropdown-icon">🪑</span>Mesas
                        </a></li>
                    <?php endif; ?>
                    
                    <?php if($tiene_permiso('cocina')): ?>
                        <li><a class="dropdown-item <?= $current_page == 'cocina' ? 'active' : '' ?>" href="<?= site_url('cocina') ?>">
                            <span class="dropdown-icon">🔥</span>Cocina
                        </a></li>
                    <?php endif; ?>
                    
                    <?php if($rol == 'admin' || $rol == 'admin_sucursal'): ?>
                        <li><a class="dropdown-item <?= $current_page == 'usuarios' ? 'active' : '' ?>" href="<?= site_url('admin/usuarios') ?>">
                            <span class="dropdown-icon">👥</span>Usuarios
                        </a></li>
                    <?php endif; ?>
                    
                    <?php if($rol == 'admin'): ?>
                        <li><a class="dropdown-item <?= $current_page == 'sucursales' ? 'active' : '' ?>" href="<?= site_url('admin/sucursales') ?>">
                            <span class="dropdown-icon">🏢</span>Sucursales
                        </a></li>
                    <?php endif; ?>

                    <li><hr class="dropdown-divider"></li>

                    <!-- Perfil y botón salir -->
                    <li class="text-center py-2">
                        <div class="user-info-dropdown text-center mb-2">
                            <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                                <i class="bi bi-person-circle"></i>
                                <span class="username"><?= $this->session->userdata('nombre_completo') ?: $this->session->userdata('nombre') ?></span>
                            </div>
                            <?php if($this->session->userdata('nombre_sucursal')): ?>
                                <div class="user-sucursal-dropdown"><?= $this->session->userdata('nombre_sucursal') ?></div>
                            <?php endif; ?>
                        </div>
                        <a href="<?= site_url('login/salir') ?>" class="btn btn-danger btn-action w-100">
                            🚪 Salir
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
