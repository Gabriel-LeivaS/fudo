<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>👥 Gestión de Usuarios - Panel FUDO</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent: #b08c6a;
            --accent-2: #a3c06b;
            --muted: #6c6c6c;
            --bg-light: #fbf8f6;
        }

        * {
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background: var(--bg-light);
            padding: 20px;
        }

        .admin-header {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            color: white;
            padding: 30px;
            border-radius: 14px;
            margin-bottom: 30px;
            box-shadow: 0 14px 36px rgba(11,11,11,0.06);
        }

        .admin-header h1 {
            font-size: 28px;
            font-weight: 800;
            margin: 0 0 8px 0;
        }

        .admin-header p {
            margin: 0;
            opacity: 0.95;
            font-size: 15px;
        }

        .card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 14px 36px rgba(11,11,11,0.06);
        }

        .card-body {
            padding: 25px;
        }

        .btn-action {
            padding: 0.5rem 0.9rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(176, 140, 106, 0.3);
        }

        .btn-success {
            background: #28a745;
            border: none;
        }

        .btn-danger {
            background: #dc3545;
            border: none;
        }

        .btn-warning {
            background: #ffc107;
            border: none;
            color: #333;
        }

        .table {
            margin-top: 20px;
        }

        .table th {
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            color: var(--muted);
            border-bottom: 2px solid #dee2e6;
        }

        .table td {
            vertical-align: middle;
            font-size: 14px;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
        }

        .badge.bg-success {
            background: #d4edda !important;
            color: #155724;
        }

        .badge.bg-danger {
            background: #f8d7da !important;
            color: #721c24;
        }

        .badge.bg-primary {
            background: #d1ecf1 !important;
            color: #0c5460;
        }

        .badge.bg-warning {
            background: #fff3cd !important;
            color: #856404;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .modal-content {
            border-radius: 14px;
            border: none;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            color: white;
            border-radius: 14px 14px 0 0;
            padding: 20px 25px;
        }

        .modal-header h5 {
            font-weight: 700;
            margin: 0;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .modal-body {
            padding: 25px;
        }

        .form-label {
            font-weight: 600;
            font-size: 14px;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent-2);
            box-shadow: 0 0 0 0.2rem rgba(163, 192, 107, 0.15);
        }

        .alert {
            border-radius: 10px;
            border: none;
            font-weight: 600;
        }

        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 15px 20px;
            border-radius: 14px;
            margin-bottom: 30px;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 20px;
            color: var(--accent);
        }

        .nav-link {
            font-weight: 600;
            color: var(--muted);
            margin: 0 10px;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
            white-space: nowrap; 
        }

        .nav-link:hover {
            background: var(--bg-light);
            color: var(--accent);
        }

        .nav-link.active {
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
        color: white;
        }

        .filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .sucursal-field {
            display: none;
        }

        .sucursal-field.show {
            display: block;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container-fluid">
            <span class="navbar-brand">🍽️ FUDO</span>
            <div class="d-flex align-items-center gap-3">
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
                ?>
                
                <?php if($tiene_permiso('pedidos')): ?>
                    <a href="<?= site_url('admin') ?>" class="nav-link">� Pedidos</a>
                <?php endif; ?>
                
                <?php if($tiene_permiso('categorias')): ?>
                    <a href="<?= site_url('admin/categorias') ?>" class="nav-link">🏷️ Categorías</a>
                <?php endif; ?>
                
                <?php if($tiene_permiso('productos')): ?>
                    <a href="<?= site_url('admin/productos') ?>" class="nav-link">🛍️ Productos</a>
                <?php endif; ?>
                
                <?php if($tiene_permiso('mi_carta')): ?>
                    <a href="<?= site_url('admin/mi_carta') ?>" class="nav-link">📋 Mi Carta</a>
                <?php endif; ?>
                
                <?php if($tiene_permiso('mesas')): ?>
                    <a href="<?= site_url('mesas') ?>" class="nav-link">🪑 Mesas</a>
                <?php endif; ?>
                
                <?php if($tiene_permiso('cocina')): ?>
                    <a href="<?= site_url('cocina') ?>" class="nav-link">🔥 Cocina</a>
                <?php endif; ?>
                
                <?php if($rol == 'admin' || $rol == 'admin_sucursal'): ?>
                    <a href="<?= site_url('admin/usuarios') ?>" class="nav-link active">👥 Usuarios</a>
                <?php endif; ?>
                
                <?php if($rol == 'admin'): ?>
                    <a href="<?= site_url('admin/sucursales') ?>" class="nav-link">🏢 Sucursales</a>
                <?php endif; ?>
                
                <a href="<?= site_url('login/salir') ?>" class="btn btn-danger btn-action">🚪 Salir</a>
            </div>
        </div>
    </nav>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div id="toastNotificacion" class="toast" role="alert">
            <div class="toast-header">
                <strong class="me-auto" id="toastTitulo">Notificación</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body" id="toastMensaje"></div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="admin-header">
            <h1>👥 Gestión de Usuarios</h1>
            <p>Administra los usuarios del sistema y sus permisos por sucursal</p>
        </div>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                ✅ <?= $this->session->flashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                ❌ <?= $this->session->flashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">📋 Lista de Usuarios</h5>
                    <button class="btn btn-primary btn-action" onclick="abrirModalCrear()">
                        ➕ Crear Usuario
                    </button>
                </div>

                <!-- Filtros -->
                <div class="filters">
                    <div class="filter-group">
                        <label class="form-label">🔍 Filtrar por Rol</label>
                        <select class="form-select" id="filtroRol" onchange="filtrarUsuarios()">
                            <option value="">Todos los roles</option>
                            <option value="admin">Super Admin</option>
                            <option value="admin_sucursal">Admin Sucursal</option>
                            <option value="usuario">Usuario</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="form-label">🏢 Filtrar por Sucursal</label>
                        <select class="form-select" id="filtroSucursal" onchange="filtrarUsuarios()">
                            <option value="">Todas las sucursales</option>
                            <?php foreach ($sucursales as $sucursal): ?>
                                <option value="<?= $sucursal->id_sucursal ?>">
                                    <?= $sucursal->nombre ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Nombre Completo</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Sucursal</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaUsuarios">
                            <?php if (empty($usuarios)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        No hay usuarios registrados
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr data-rol="<?= $usuario->rol ?>" 
                                        data-sucursal="<?= $usuario->id_sucursal ?? '' ?>">
                                        <td><strong>#<?= $usuario->id_usuario ?></strong></td>
                                        <td>
                                            <div class="user-info">
                                                <div class="user-avatar">
                                                    <?= strtoupper(substr($usuario->usuario, 0, 1)) ?>
                                                </div>
                                                <strong><?= htmlspecialchars($usuario->usuario) ?></strong>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($usuario->nombre_completo) ?></td>
                                        <td><?= htmlspecialchars($usuario->email) ?></td>
                                        <td>
                                            <?php if ($usuario->rol == 'admin'): ?>
                                                <span class="badge bg-primary">Admin</span>
                                            <?php elseif ($usuario->rol == 'admin_sucursal'): ?>
                                                <span class="badge bg-warning">Admin Sucursal</span>
                                            <?php elseif ($usuario->rol == 'usuario'): ?>
                                                <span class="badge bg-info">Usuario</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">❓ <?= htmlspecialchars($usuario->rol) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($usuario->nombre_sucursal): ?>
                                                <span class="badge bg-info">
                                                    🏢 <?= htmlspecialchars($usuario->nombre_sucursal) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($usuario->activo): ?>
                                                <span class="badge bg-success">✅ Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">❌ Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-warning btn-sm btn-action" 
                                                        onclick="abrirModalEditar(<?= $usuario->id_usuario ?>)">
                                                    ✏️
                                                </button>
                                                <?php if ($usuario->activo): ?>
                                                    <button class="btn btn-danger btn-sm btn-action" 
                                                            onclick="cambiarEstado(<?= $usuario->id_usuario ?>, 0)">
                                                        🔒
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-success btn-sm btn-action" 
                                                            onclick="cambiarEstado(<?= $usuario->id_usuario ?>, 1)">
                                                        🔓
                                                    </button>
                                                <?php endif; ?>
                                                <button class="btn btn-danger btn-sm btn-action" 
                                                        onclick="confirmarEliminar(<?= $usuario->id_usuario ?>, '<?= htmlspecialchars($usuario->usuario) ?>')">
                                                    🗑️
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Crear Usuario -->
    <div class="modal fade" id="modalCrear" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>➕ Crear Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formCrear" onsubmit="crearUsuario(event)">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">👤 Usuario *</label>
                            <input type="text" name="usuario" class="form-control" required 
                                   placeholder="Ej: admin_nuevo">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">🔒 Contraseña *</label>
                            <input type="password" name="contrasena" class="form-control" required 
                                   minlength="6" placeholder="Mínimo 6 caracteres">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">📝 Nombre Completo *</label>
                            <input type="text" name="nombre_completo" class="form-control" required 
                                   placeholder="Ej: Juan Pérez">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">📧 Email *</label>
                            <input type="email" name="email" class="form-control" required 
                                   placeholder="correo@ejemplo.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">🎭 Rol *</label>
                            <select name="rol" class="form-select" id="rolCrear" required 
                                    onchange="toggleSucursalField('crear')">
                                <option value="">Selecciona un rol</option>
                                <option value="admin">⭐ Admin</option>
                                <option value="admin_sucursal">👤 Admin Sucursal</option>
                                <option value="usuario">👁️ Usuario</option>
                            </select>
                            <small class="form-text text-muted">
                                <strong>Super Admin:</strong> Control total del sistema · 
                                <strong>Admin Sucursal:</strong> Gestión de su sucursal · 
                                <strong>Usuario:</strong> Solo visualización
                            </small>
                        </div>
                        <div class="mb-3 sucursal-field" id="sucursalFieldCrear">
                            <label class="form-label">🏢 Sucursal *</label>
                            <select name="id_sucursal" class="form-select">
                                <option value="">Selecciona una sucursal</option>
                                <?php foreach ($sucursales as $sucursal): ?>
                                    <?php if ($sucursal->activo): ?>
                                        <option value="<?= $sucursal->id_sucursal ?>">
                                            <?= htmlspecialchars($sucursal->nombre) ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Permisos para rol usuario -->
                        <div class="mb-3 permisos-field" id="permisosFieldCrear" style="display: none;">
                            <label class="form-label">🔐 Permisos de Acceso</label>
                            <div class="card">
                                <div class="card-body">
                                    <small class="text-muted d-block mb-2">Selecciona las ventanas a las que este usuario tendrá acceso:</small>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permisos[pedidos]" id="permisoCrearPedidos">
                                        <label class="form-check-label" for="permisoCrearPedidos">
                                            📦 Pedidos
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permisos[categorias]" id="permisoCrearCategorias">
                                        <label class="form-check-label" for="permisoCrearCategorias">
                                            🏷️ Categorías
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permisos[productos]" id="permisoCrearProductos">
                                        <label class="form-check-label" for="permisoCrearProductos">
                                            🛍️ Productos
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permisos[mi_carta]" id="permisoCrearMicarta">
                                        <label class="form-check-label" for="permisoCrearMicarta">
                                            📋 Mi Carta
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permisos[mesas]" id="permisoCrearMesas">
                                        <label class="form-check-label" for="permisoCrearMesas">
                                            🪑 Mesas
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permisos[cocina]" id="permisoCrearCocina">
                                        <label class="form-check-label" for="permisoCrearCocina">
                                            🔥 Cocina
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            ❌ Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            💾 Guardar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Usuario -->
    <div class="modal fade" id="modalEditar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>✏️ Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formEditar">
                    <input type="hidden" name="id_usuario" id="editarId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">👤 Usuario *</label>
                            <input type="text" name="usuario" id="editarUsuario" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">🔒 Nueva Contraseña</label>
                            <input type="password" name="contrasena" id="editarContrasena" 
                                   class="form-control" minlength="6" 
                                   placeholder="Dejar vacío para mantener actual">
                            <small class="text-muted">Solo completa si deseas cambiar la contraseña</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">📝 Nombre Completo *</label>
                            <input type="text" name="nombre_completo" id="editarNombre" 
                                   class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">📧 Email *</label>
                            <input type="email" name="email" id="editarEmail" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">🎭 Rol *</label>
                            <select name="rol" id="editarRol" class="form-select" required 
                                    onchange="toggleSucursalField('editar')">
                                <option value="admin">⭐ Admin</option>
                                <option value="admin_sucursal">👤 Admin Sucursal</option>
                                <option value="usuario">👁️ Usuario</option>
                            </select>
                            <small class="form-text text-muted">
                                <strong>Super Admin:</strong> Control total · 
                                <strong>Admin Sucursal:</strong> Gestión operativa · 
                                <strong>Usuario:</strong> Solo lectura
                            </small>
                        </div>
                        <div class="mb-3 sucursal-field" id="sucursalFieldEditar">
                            <label class="form-label">🏢 Sucursal *</label>
                            <select name="id_sucursal" id="editarSucursal" class="form-select">
                                <option value="">Selecciona una sucursal</option>
                                <?php foreach ($sucursales as $sucursal): ?>
                                    <?php if ($sucursal->activo): ?>
                                        <option value="<?= $sucursal->id_sucursal ?>">
                                            <?= htmlspecialchars($sucursal->nombre) ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Permisos para rol usuario -->
                        <div class="mb-3 permisos-field" id="permisosFieldEditar" style="display: none;">
                            <label class="form-label">🔐 Permisos de Acceso</label>
                            <div class="card">
                                <div class="card-body">
                                    <small class="text-muted d-block mb-2">Selecciona las ventanas a las que este usuario tendrá acceso:</small>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permisos[pedidos]" id="permisoEditarPedidos">
                                        <label class="form-check-label" for="permisoEditarPedidos">
                                            📦 Pedidos
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permisos[categorias]" id="permisoEditarCategorias">
                                        <label class="form-check-label" for="permisoEditarCategorias">
                                            🏷️ Categorías
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permisos[productos]" id="permisoEditarProductos">
                                        <label class="form-check-label" for="permisoEditarProductos">
                                            🛍️ Productos
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permisos[mi_carta]" id="permisoEditarMicarta">
                                        <label class="form-check-label" for="permisoEditarMicarta">
                                            📋 Mi Carta
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permisos[mesas]" id="permisoEditarMesas">
                                        <label class="form-check-label" for="permisoEditarMesas">
                                            🪑 Mesas
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permisos[cocina]" id="permisoEditarCocina">
                                        <label class="form-check-label" for="permisoEditarCocina">
                                            🔥 Cocina
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            ❌ Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" id="btnGuardarEdicion">
                            💾 Actualizar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div class="modal fade" id="modalConfirmacion" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" id="confirmacionHeader">
                    <h5 class="modal-title" id="confirmacionTitulo">Confirmar acción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="confirmacionMensaje"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        ❌ Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="confirmacionBoton">
                        ✅ Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const usuarios = <?= json_encode($usuarios) ?>;
        
        // Inicializar evento del formulario de edición después de que el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            // Evento para el botón de guardar edición
            document.getElementById('btnGuardarEdicion').addEventListener('click', function(e) {
                e.preventDefault();
                editarUsuario(e);
            });
            
            // También manejar el evento submit del formulario
            document.getElementById('formEditar').addEventListener('submit', function(e) {
                e.preventDefault();
                editarUsuario(e);
            });
        });

        function abrirModalCrear() {
            document.getElementById('formCrear').reset();
            document.getElementById('sucursalFieldCrear').classList.remove('show');
            new bootstrap.Modal(document.getElementById('modalCrear')).show();
        }

        function abrirModalEditar(id) {
            console.log('Abriendo modal para editar usuario ID:', id);
            const usuario = usuarios.find(u => u.id_usuario == id || u.id == id);
            if (!usuario) {
                console.error('Usuario no encontrado con ID:', id);
                mostrarToast('error', 'Usuario no encontrado');
                return;
            }
            console.log('Usuario encontrado:', usuario);

            document.getElementById('editarId').value = usuario.id_usuario || usuario.id;
            document.getElementById('editarUsuario').value = usuario.usuario;
            document.getElementById('editarNombre').value = usuario.nombre_completo;
            document.getElementById('editarEmail').value = usuario.email;
            document.getElementById('editarRol').value = usuario.rol;
            document.getElementById('editarContrasena').value = '';
            
            // Mostrar campo sucursal para admin_sucursal y usuario
            const sucursalField = document.getElementById('sucursalFieldEditar');
            const sucursalSelect = document.getElementById('editarSucursal');
            
            if (usuario.rol === 'admin_sucursal' || usuario.rol === 'usuario') {
                // Usar style.display además de las clases para asegurar visibilidad
                sucursalField.style.display = 'block';
                sucursalField.classList.add('show');
                sucursalSelect.value = usuario.id_sucursal || '';
                sucursalSelect.required = true;
            } else {
                sucursalField.style.display = 'none';
                sucursalField.classList.remove('show');
                sucursalSelect.required = false;
                // Limpiar valor solo si es admin (que no necesita sucursal)
                sucursalSelect.value = '';
            }
            
            // Mostrar y cargar permisos SOLO para rol usuario
            const permisosFieldEditar = document.getElementById('permisosFieldEditar');
            if (usuario.rol === 'usuario') {
                permisosFieldEditar.style.display = 'block';
                
                // Parsear permisos (puede ser string JSON o ya objeto)
                let permisos = {};
                try {
                    permisos = typeof usuario.permisos === 'string' ? JSON.parse(usuario.permisos) : (usuario.permisos || {});
                } catch (e) {
                    permisos = {};
                }
                
                // Marcar checkboxes según permisos actuales
                document.getElementById('permisoEditarPedidos').checked = permisos.pedidos === true;
                document.getElementById('permisoEditarCategorias').checked = permisos.categorias === true;
                document.getElementById('permisoEditarProductos').checked = permisos.productos === true;
                document.getElementById('permisoEditarMicarta').checked = permisos.mi_carta === true;
                document.getElementById('permisoEditarMesas').checked = permisos.mesas === true;
                document.getElementById('permisoEditarCocina').checked = permisos.cocina === true;
            } else {
                permisosFieldEditar.style.display = 'none';
            }

            new bootstrap.Modal(document.getElementById('modalEditar')).show();
        }

        function toggleSucursalField(tipo) {
            const rol = document.getElementById(`${tipo === 'crear' ? 'rolCrear' : 'editarRol'}`).value;
            const field = document.getElementById(`sucursalField${tipo.charAt(0).toUpperCase() + tipo.slice(1)}`);
            const select = field.querySelector('select');
            
            // Obtener campo de permisos
            const permisosField = document.getElementById(`permisosField${tipo.charAt(0).toUpperCase() + tipo.slice(1)}`);

            // Mostrar campo de sucursal para admin_sucursal y usuario
            if (rol === 'admin_sucursal' || rol === 'usuario') {
                // Usar style.display en lugar de clases CSS para mayor fiabilidad
                field.style.display = 'block';
                field.classList.add('show');
                select.required = true;
            } else {
                field.style.display = 'none';
                field.classList.remove('show');
                select.required = false;
                // NO limpiar el valor, solo ocultarlo
                // select.value = ''; // COMENTADO: No limpiar para preservar el valor
            }
            
            // Mostrar campo de permisos SOLO para rol usuario
            if (rol === 'usuario') {
                permisosField.style.display = 'block';
            } else {
                permisosField.style.display = 'none';
                // Solo desmarcar checkboxes si NO estamos editando
                if (tipo === 'crear') {
                    const checkboxes = permisosField.querySelectorAll('input[type="checkbox"]');
                    checkboxes.forEach(cb => cb.checked = false);
                }
            }
        }

        // Función para mostrar notificaciones Toast
        function mostrarToast(tipo, mensaje) {
            const toastEl = document.getElementById('toastNotificacion');
            const toastHeader = toastEl.querySelector('.toast-header');
            const toastTitulo = document.getElementById('toastTitulo');
            const toastMensaje = document.getElementById('toastMensaje');

            // Remover clases previas
            toastHeader.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'text-white');

            // Configurar según el tipo
            if (tipo === 'success') {
                toastHeader.classList.add('bg-success', 'text-white');
                toastTitulo.textContent = '✅ Éxito';
            } else if (tipo === 'error') {
                toastHeader.classList.add('bg-danger', 'text-white');
                toastTitulo.textContent = '❌ Error';
            } else if (tipo === 'warning') {
                toastHeader.classList.add('bg-warning', 'text-white');
                toastTitulo.textContent = '⚠️ Advertencia';
            }

            toastMensaje.textContent = mensaje;

            // Mostrar el toast
            const toast = new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 3000
            });
            toast.show();
        }

        // Función para mostrar modal de confirmación
        function mostrarConfirmacion(titulo, mensaje, tipoBoton, callback) {
            return new Promise((resolve) => {
                const modal = document.getElementById('modalConfirmacion');
                const header = document.getElementById('confirmacionHeader');
                const tituloEl = document.getElementById('confirmacionTitulo');
                const mensajeEl = document.getElementById('confirmacionMensaje');
                const boton = document.getElementById('confirmacionBoton');

                // Configurar estilos según el tipo
                header.classList.remove('bg-danger', 'bg-warning', 'bg-info', 'text-white');
                boton.classList.remove('btn-danger', 'btn-warning', 'btn-primary');

                if (tipoBoton === 'danger') {
                    header.classList.add('bg-danger', 'text-white');
                    boton.classList.add('btn-danger');
                } else if (tipoBoton === 'warning') {
                    header.classList.add('bg-warning', 'text-white');
                    boton.classList.add('btn-warning');
                } else {
                    header.classList.add('bg-info', 'text-white');
                    boton.classList.add('btn-primary');
                }

                tituloEl.textContent = titulo;
                mensajeEl.textContent = mensaje;

                // Manejar la confirmación
                const confirmar = () => {
                    resolve(true);
                    bootstrap.Modal.getInstance(modal).hide();
                    boton.removeEventListener('click', confirmar);
                };

                boton.addEventListener('click', confirmar);

                // Si se cierra el modal sin confirmar
                modal.addEventListener('hidden.bs.modal', () => {
                    resolve(false);
                }, { once: true });

                new bootstrap.Modal(modal).show();
            });
        }

        async function crearUsuario(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            
            // Los checkboxes ya vienen como permisos[*] del formulario
            // Solo asegurarnos que los NO marcados también se envíen
            const rol = formData.get('rol');
            if (rol === 'usuario') {
                // Solo los checkboxes marcados se envían, los no marcados no
                // El backend los maneja con isset() así que está bien
            }

            try {
                const response = await fetch('<?= site_url("usuarios/crear") ?>', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    mostrarToast('success', data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    mostrarToast('error', data.message);
                }
            } catch (error) {
                mostrarToast('error', 'Error al crear el usuario');
                console.error(error);
            }
        }

        async function editarUsuario(e) {
            e.preventDefault();
            
            // Prevenir cierre automático del modal
            e.stopPropagation();
            
            // Obtener el formulario correctamente
            const form = document.getElementById('formEditar');
            const formData = new FormData(form);
            const id = formData.get('id_usuario');
            
            // Validación básica
            if (!id) {
                mostrarToast('error', 'Error: No se pudo identificar el usuario a editar');
                console.error('ID de usuario no encontrado');
                return;
            }
            
            // Asegurar que id_sucursal se envíe correctamente
            const rol = formData.get('rol');
            const idSucursal = formData.get('id_sucursal');
            
            // Si el rol requiere sucursal pero no se envió, obtenerla del select
            if ((rol === 'admin_sucursal' || rol === 'usuario') && !idSucursal) {
                const sucursalSelect = document.getElementById('editarSucursal');
                if (sucursalSelect && sucursalSelect.value) {
                    formData.set('id_sucursal', sucursalSelect.value);
                }
            }
            
            // DEBUG COMPLETO: Mostrar todos los datos que se van a enviar
            console.log('=== DEBUG EDITAR USUARIO ===');
            console.log('ID Usuario:', id);
            console.log('Rol seleccionado:', formData.get('rol'));
            console.log('\nTodos los campos del FormData:');
            for (let [key, value] of formData.entries()) {
                console.log(`  ${key}: ${value}`);
            }
            console.log('\nEstado de checkboxes de permisos:');
            console.log('  permisoEditarPedidos:', document.getElementById('permisoEditarPedidos').checked);
            console.log('  permisoEditarCategorias:', document.getElementById('permisoEditarCategorias').checked);
            console.log('  permisoEditarProductos:', document.getElementById('permisoEditarProductos').checked);
            console.log('  permisoEditarMicarta:', document.getElementById('permisoEditarMicarta').checked);
            console.log('  permisoEditarMesas:', document.getElementById('permisoEditarMesas').checked);
            console.log('  permisoEditarCocina:', document.getElementById('permisoEditarCocina').checked);
            console.log('========================');

            try {
                console.log('Enviando petición a:', `<?= site_url("usuarios/editar") ?>/${id}`);
                const response = await fetch(`<?= site_url("usuarios/editar") ?>/${id}`, {
                    method: 'POST',
                    body: formData
                });

                // Verificar que la respuesta sea válida
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const text = await response.text();
                console.log('Respuesta raw del servidor:', text);
                
                let data;
                try {
                    data = JSON.parse(text);
                } catch (parseError) {
                    console.error('Error al parsear respuesta JSON:', parseError);
                    console.error('Respuesta recibida:', text);
                    mostrarToast('error', 'Error en la respuesta del servidor');
                    return;
                }
                
                console.log('Respuesta parseada:', data);

                if (data.success) {
                    // Cerrar modal PRIMERO
                    const modalEl = document.getElementById('modalEditar');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Esperar a que el modal se cierre completamente antes de mostrar toast
                    modalEl.addEventListener('hidden.bs.modal', function onModalHidden() {
                        // Remover listener para evitar duplicados
                        modalEl.removeEventListener('hidden.bs.modal', onModalHidden);
                        
                        // Mostrar toast de éxito
                        mostrarToast('success', data.message);
                        
                        // Actualizar el array de usuarios en memoria para reflejar cambios
                        const usuarioIndex = usuarios.findIndex(u => u.id_usuario == id || u.id == id);
                        if (usuarioIndex !== -1) {
                            // Actualizar datos básicos
                            usuarios[usuarioIndex].usuario = formData.get('usuario');
                            usuarios[usuarioIndex].nombre_completo = formData.get('nombre_completo');
                            usuarios[usuarioIndex].email = formData.get('email');
                            usuarios[usuarioIndex].rol = formData.get('rol');
                            usuarios[usuarioIndex].id_sucursal = formData.get('id_sucursal');
                            
                            // Construir objeto de permisos desde checkboxes
                            if (formData.get('rol') === 'usuario') {
                                const permisosActualizados = {
                                    pedidos: document.getElementById('permisoEditarPedidos').checked,
                                    categorias: document.getElementById('permisoEditarCategorias').checked,
                                    productos: document.getElementById('permisoEditarProductos').checked,
                                    mi_carta: document.getElementById('permisoEditarMicarta').checked,
                                    mesas: document.getElementById('permisoEditarMesas').checked,
                                    cocina: document.getElementById('permisoEditarCocina').checked
                                };
                                usuarios[usuarioIndex].permisos = JSON.stringify(permisosActualizados);
                            } else {
                                usuarios[usuarioIndex].permisos = null;
                            }
                        }
                        
                        // Recargar página después de 2 segundos para refrescar la tabla
                        setTimeout(() => location.reload(), 2000);
                    }, { once: true });
                } else {
                    console.error('Error del servidor:', data.message);
                    mostrarToast('error', data.message || 'Error al actualizar el usuario');
                }
            } catch (error) {
                console.error('Error completo:', error);
                mostrarToast('error', 'Error al actualizar el usuario: ' + error.message);
            }
        }

        async function cambiarEstado(id, nuevoEstado) {
            const accion = nuevoEstado ? 'activar' : 'desactivar';
            const confirmado = await mostrarConfirmacion(
                `${accion === 'activar' ? '✅' : '⚠️'} ${accion.charAt(0).toUpperCase() + accion.slice(1)} Usuario`,
                `¿Estás seguro de que deseas ${accion} este usuario?`,
                accion === 'activar' ? 'primary' : 'warning'
            );

            if (!confirmado) return;

            try {
                const response = await fetch(`<?= site_url("usuarios/cambiar_estado") ?>/${id}/${nuevoEstado}`, {
                    method: 'POST'
                });

                const data = await response.json();

                if (data.success) {
                    mostrarToast('success', data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    mostrarToast('error', data.message);
                }
            } catch (error) {
                mostrarToast('error', 'Error al cambiar el estado');
                console.error(error);
            }
        }

        async function confirmarEliminar(id, usuario) {
            const confirmado = await mostrarConfirmacion(
                '🗑️ Eliminar Usuario',
                `¿Estás seguro de eliminar al usuario "${usuario}"?\n\nEsta acción no se puede deshacer.`,
                'danger'
            );

            if (!confirmado) return;

            try {
                const response = await fetch(`<?= site_url("usuarios/eliminar") ?>/${id}`, {
                    method: 'POST'
                });

                const data = await response.json();

                if (data.success) {
                    mostrarToast('success', data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    mostrarToast('error', data.message);
                }
            } catch (error) {
                mostrarToast('error', 'Error al eliminar el usuario');
                console.error(error);
            }
        }

        function filtrarUsuarios() {
            const filtroRol = document.getElementById('filtroRol').value;
            const filtroSucursal = document.getElementById('filtroSucursal').value;
            const filas = document.querySelectorAll('#tablaUsuarios tr[data-rol]');

            filas.forEach(fila => {
                const rol = fila.dataset.rol;
                const sucursal = fila.dataset.sucursal;
                let mostrar = true;

                if (filtroRol && rol !== filtroRol) {
                    mostrar = false;
                }

                if (filtroSucursal && sucursal !== filtroSucursal) {
                    mostrar = false;
                }

                fila.style.display = mostrar ? '' : 'none';
            });
        }
    </script>
</body>
</html>
