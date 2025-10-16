<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Categorías - Fudo</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root {
            --accent: #b08c6a;
            --accent-2: #a3c06b;
            --muted: #6c6c6c;
            --card-radius: 14px;
            --shadow: 0 14px 36px rgba(11,11,11,0.06);
            --bg-light: #fbf8f6;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { 
            height: 100%; 
            font-family: 'Montserrat', system-ui, sans-serif; 
            color: #222; 
            background: var(--bg-light);
            padding: 20px;
        }
        
        /* Navbar Superior */
        .navbar {
            background: white;
            padding: 15px 0;
            margin-bottom: 20px;
            border-radius: 14px;
            box-shadow: var(--shadow);
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
            text-decoration: none;
        }
        
        .nav-link:hover {
            background: var(--bg-light);
            color: var(--accent);
        }
        
        .nav-link.active {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            color: white;
        }
        
        /* Admin Header */
        .admin-header {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            color: white;
            padding: 30px;
            border-radius: 14px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
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
            box-shadow: var(--shadow);
            border-radius: var(--card-radius);
            background: white;
            margin-bottom: 2rem;
        }
        .card-header {
            background: white;
            border-bottom: 1px solid #f0f0f0;
            padding: 1.25rem 1.5rem;
            border-radius: var(--card-radius) var(--card-radius) 0 0 !important;
        }
        .card-header h5 {
            font-weight: 700;
            font-size: 18px;
            margin: 0;
            color: #333;
        }
        .btn-primary {
            background: var(--accent-2);
            border: none;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 10px;
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            background: #94b35e;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(163,192,107,0.3);
        }
        .table {
            margin-bottom: 0;
        }
        .table thead th {
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            color: #495057;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem;
        }
        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            font-size: 14px;
        }
        .table-hover tbody tr {
            transition: all 0.2s ease;
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
            transform: translateX(4px);
        }
        .badge-custom {
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 12px;
        }
        .action-buttons {
            display: flex;
            gap: 0.4rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-action {
            padding: 0.5rem 0.9rem;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease;
            border: none;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.2);
        }
        .btn-action:active {
            transform: translateY(0);
        }
        .btn-warning {
            background: #f39c12;
            color: white;
        }
        .btn-warning:hover {
            background: #e67e22;
            color: white;
        }
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        .btn-secondary:hover {
            background: #7f8c8d;
            color: white;
        }
        .btn-success {
            background: var(--accent-2);
            color: white;
        }
        .btn-success:hover {
            background: #94b35e;
            color: white;
        }
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        .btn-danger:hover {
            background: #c0392b;
            color: white;
        }
        /* Botón toggle específico */
        .btn-toggle {
            min-width: 40px;
            padding: 0.5rem 0.7rem;
            font-size: 18px;
        }
        .modal-content {
            border: none;
            border-radius: 14px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .modal-header {
            border-bottom: 1px solid #f0f0f0;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            color: white;
            border-radius: 14px 14px 0 0;
        }
        .modal-title {
            font-weight: 700;
            font-size: 18px;
        }
        .modal-body {
            padding: 1.5rem;
        }
        .form-label {
            font-weight: 600;
            font-size: 13px;
            color: #333;
            margin-bottom: 0.5rem;
        }
        .form-control, .form-select {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 14px;
            font-family: 'Montserrat', inherit;
            transition: all 0.2s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--accent-2);
            box-shadow: 0 0 0 3px rgba(163,192,107,0.15);
        }
        .form-text {
            font-size: 12px;
            color: #6c757d;
            margin-top: 0.25rem;
        }
        .btn-close {
            filter: brightness(0) invert(1);
        }
        @media (max-width: 768px) {
            .admin-header h2 {
                font-size: 20px;
            }
            .btn-group {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            .btn-group .btn {
                font-size: 11px;
                padding: 6px 10px;
            }
            .table {
                font-size: 12px;
            }
            .btn-action {
                padding: 0.3rem 0.5rem;
                font-size: 11px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar Superior -->
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
                    <a href="<?= site_url('admin') ?>" class="nav-link">📦 Pedidos</a>
                <?php endif; ?>
                
                <?php if($tiene_permiso('categorias')): ?>
                    <a href="<?= site_url('admin/categorias') ?>" class="nav-link active">🏷️ Categorías</a>
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
                    <a href="<?= site_url('admin/usuarios') ?>" class="nav-link">👥 Usuarios</a>
                <?php endif; ?>
                
                <?php if($rol == 'admin'): ?>
                    <a href="<?= site_url('admin/sucursales') ?>" class="nav-link">🏢 Sucursales</a>
                <?php endif; ?>
                
                <!-- Información del Usuario -->
                <div class="dropdown me-2">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <span class="d-none d-md-inline">👤 <?= $this->session->userdata('nombre') ?></span>
                        <span class="d-md-none">👤</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">👤 <?= $this->session->userdata('nombre') ?></h6></li>
                        <li><span class="dropdown-item-text">
                            <?php 
                            $rol_display = [
                                'admin' => '🔧 Super Admin',
                                'admin_sucursal' => '🏢 Admin Sucursal',
                                'usuario' => '👨‍💼 Usuario'
                            ];
                            echo $rol_display[$rol] ?? $rol;
                            ?>
                        </span></li>
                        <?php if($rol == 'admin_sucursal'): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><span class="dropdown-item-text text-muted">🏢 <?= $this->session->userdata('nombre_sucursal') ?></span></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <a href="<?= site_url('login/salir') ?>" class="btn btn-danger btn-action">🚪 Salir</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <!-- Admin Header -->
        <div class="admin-header">
            <h1>🏷️ Gestión de Categorías</h1>
            <p>Organiza y administra las categorías de productos de tu carta</p>
        </div>
        <?php if($this->session->userdata('rol') == 'admin'): ?>
        <!-- Filtro de Sucursal para Super Admin -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">🏢 Seleccione una Sucursal:</label>
                    </div>
                    <div class="col-md-6">
                        <select id="filtroSucursal" class="form-select" onchange="filtrarPorSucursal()">
                            <option value="">-- Seleccione una sucursal --</option>
                            <?php foreach($sucursales as $suc): ?>
                                <option value="<?= $suc->id_sucursal ?>"><?= $suc->nombre ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 text-end">
                        <span class="badge bg-info" id="contadorCategorias" style="display: none;">0 categorías</span>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- Badge indicador para Admin Sucursal -->
        <div class="alert alert-info d-flex justify-content-between align-items-center mb-3">
            <span>🏢 <strong>Sucursal:</strong> <?= $this->session->userdata('nombre_sucursal') ?></span>
            <span class="badge bg-primary"><?= count($categorias) ?> categorías</span>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5>📋 Listado de Categorías</h5>
                <?php if($tiene_permiso('categorias')): ?>
                <button class="btn btn-primary" onclick="abrirModalCrear()" id="btnNuevaCategoria">
                    ➕ Nueva Categoría
                </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if($this->session->userdata('rol') == 'admin'): ?>
                <!-- Mensaje inicial para super admin -->
                <div id="mensajeSeleccionSucursal" class="text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-building" style="font-size: 3rem; color: #b08c6a;"></i>
                    </div>
                    <h5 class="text-muted">Seleccione una sucursal para ver sus categorías</h5>
                    <p class="text-muted">Use el selector de sucursal arriba para comenzar</p>
                </div>
                <?php endif; ?>

                <div class="table-responsive" id="tablaContainer" <?= $this->session->userdata('rol') == 'admin' ? 'style="display: none;"' : '' ?>>
                    <table class="table table-hover align-middle" id="tablaCategorias">
                        <thead class="table-light">
                            <tr>
                                <th width="80">ID</th>
                                <th>Nombre</th>
                                <?php if($this->session->userdata('rol') == 'admin'): ?>
                                <th width="200">Sucursal</th>
                                <?php endif; ?>
                                <th width="120" class="text-center">Estado</th>
                                <th width="200" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($categorias)): ?>
                                <tr>
                                    <td colspan="<?= $this->session->userdata('rol') == 'admin' ? '5' : '4' ?>" class="text-center text-muted py-4">
                                        No hay categorías registradas
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($categorias as $cat): ?>
                                    <tr data-sucursal="<?= $cat->id_sucursal ?? '' ?>" data-estado="<?= $cat->estado ? 'true' : 'false' ?>" data-id="<?= $cat->id_categoria ?>">
                                        <td><strong>#<?= $cat->id_categoria ?></strong></td>
                                        <td><?= htmlspecialchars($cat->nombre) ?></td>
                                        <?php if($this->session->userdata('rol') == 'admin'): ?>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= htmlspecialchars($cat->nombre_sucursal ?? 'Sin sucursal') ?>
                                            </span>
                                        </td>
                                        <?php endif; ?>
                                        <td class="text-center">
                                            <?php if($cat->estado): ?>
                                                <span class="badge bg-success badge-custom">Activa</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary badge-custom">Inactiva</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="action-buttons">
                                                <?php if($tiene_permiso('categorias')): ?>
                                                <button class="btn btn-warning btn-action" 
                                                        onclick="abrirModalEditar(<?= $cat->id_categoria ?>, '<?= htmlspecialchars($cat->nombre, ENT_QUOTES) ?>')">
                                                    ✏️
                                                </button>
                                                <button class="btn <?= $cat->estado ? 'btn-secondary' : 'btn-success' ?> btn-action btn-toggle" 
                                                        onclick="toggleEstado(<?= $cat->id_categoria ?>, <?= $cat->estado ? 'false' : 'true' ?>)"
                                                        title="<?= $cat->estado ? 'Desactivar' : 'Activar' ?>">
                                                    <?= $cat->estado ? '👁️‍🗨️' : '👁️' ?>
                                                </button>
                                                <button class="btn btn-danger btn-action" 
                                                        onclick="eliminarCategoria(<?= $cat->id_categoria ?>, '<?= htmlspecialchars($cat->nombre, ENT_QUOTES) ?>')">
                                                    🗑️
                                                </button>
                                                <?php endif; ?>
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

    <!-- Modal Crear/Editar Categoría -->
    <div class="modal fade" id="modalCategoria" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitulo">Nueva Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCategoria">
                        <input type="hidden" id="id_categoria" name="id_categoria">
                        
                        <?php if($this->session->userdata('rol') == 'admin'): ?>
                        <!-- Campo de Sucursal solo para Super Admin -->
                        <div class="mb-3" id="campoSucursal">
                            <label for="id_sucursal" class="form-label">🏢 Sucursal *</label>
                            <select class="form-select" id="id_sucursal" name="id_sucursal" required>
                                <option value="">-- Seleccione una sucursal --</option>
                                <?php foreach($sucursales as $suc): ?>
                                    <option value="<?= $suc->id_sucursal ?>"><?= $suc->nombre ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Seleccione la sucursal a la que pertenecerá esta categoría</div>
                        </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de la Categoría *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                            <div class="form-text">Ejemplo: Bebidas, Comidas, Postres, etc.</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarCategoria()">
                        💾 Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let modal;
        let esEdicion = false;

        document.addEventListener('DOMContentLoaded', function() {
            modal = new bootstrap.Modal(document.getElementById('modalCategoria'));
            
            // DEBUG: Mostrar estados de todas las categorías
            console.log('=== DEBUG: Estados de Categorías ===');
            const filas = document.querySelectorAll('#tablaCategorias tbody tr[data-id]');
            filas.forEach(fila => {
                const id = fila.getAttribute('data-id');
                const estado = fila.getAttribute('data-estado');
                const badge = fila.querySelector('.badge')?.textContent;
                console.log(`ID: ${id}, Estado (data): ${estado}, Badge: ${badge}`);
            });
            console.log('===================================');
            
            <?php if($this->session->userdata('rol') == 'admin'): ?>
            // Restaurar sucursal seleccionada después de reload
            const sucursalGuardada = sessionStorage.getItem('sucursalSeleccionada');
            if(sucursalGuardada) {
                const filtroSucursal = document.getElementById('filtroSucursal');
                if(filtroSucursal) {
                    filtroSucursal.value = sucursalGuardada;
                    // Aplicar el filtro automáticamente
                    filtrarPorSucursal();
                    // Limpiar sessionStorage
                    sessionStorage.removeItem('sucursalSeleccionada');
                }
            }
            <?php endif; ?>
        });

        function abrirModalCrear() {
            esEdicion = false;
            document.getElementById('modalTitulo').textContent = 'Nueva Categoría';
            document.getElementById('formCategoria').reset();
            document.getElementById('id_categoria').value = '';
            
            <?php if($this->session->userdata('rol') == 'admin'): ?>
            // Si hay una sucursal seleccionada en el filtro, pre-seleccionarla en el modal
            const sucursalSeleccionada = document.getElementById('filtroSucursal')?.value;
            if(sucursalSeleccionada) {
                document.getElementById('id_sucursal').value = sucursalSeleccionada;
            }
            <?php endif; ?>
            
            modal.show();
        }

        function abrirModalEditar(id, nombre) {
            esEdicion = true;
            document.getElementById('modalTitulo').textContent = 'Editar Categoría';
            document.getElementById('id_categoria').value = id;
            document.getElementById('nombre').value = nombre;
            
            <?php if($this->session->userdata('rol') == 'admin'): ?>
            // Buscar la fila para obtener la sucursal
            const filas = document.querySelectorAll('#tablaCategorias tbody tr');
            filas.forEach(fila => {
                if(fila.querySelector('td:first-child strong')?.textContent === '#' + id) {
                    const sucursalId = fila.getAttribute('data-sucursal');
                    if(sucursalId) {
                        document.getElementById('id_sucursal').value = sucursalId;
                    }
                }
            });
            <?php endif; ?>
            
            modal.show();
        }

        async function guardarCategoria() {
            const form = document.getElementById('formCategoria');
            const formData = new FormData(form);
            
            const nombre = formData.get('nombre').trim();
            if(!nombre) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campo requerido',
                    text: 'El nombre de la categoría es obligatorio',
                    confirmButtonColor: '#b08c6a'
                });
                return;
            }

            <?php if($this->session->userdata('rol') == 'admin'): ?>
            // Validar que se haya seleccionado una sucursal (solo para super admin)
            const idSucursal = formData.get('id_sucursal');
            if(!idSucursal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sucursal requerida',
                    text: 'Debe seleccionar una sucursal para la categoría',
                    confirmButtonColor: '#b08c6a'
                });
                return;
            }
            <?php endif; ?>

            const url = esEdicion 
                ? '<?= site_url('admin/categoria_editar') ?>'
                : '<?= site_url('admin/categoria_crear') ?>';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if(result.success) {
                    modal.hide();
                    await Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: result.message,
                        confirmButtonColor: '#b08c6a',
                        timer: 1500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    
                    <?php if($this->session->userdata('rol') == 'admin'): ?>
                    // Para super admin: guardar sucursal seleccionada y recargar
                    const sucursalActual = document.getElementById('filtroSucursal')?.value;
                    if(sucursalActual) {
                        // Guardar en sessionStorage para mantener después del reload
                        sessionStorage.setItem('sucursalSeleccionada', sucursalActual);
                    }
                    <?php endif; ?>
                    
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'Error al guardar',
                        confirmButtonColor: '#b08c6a'
                    });
                }
            } catch(error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor',
                    confirmButtonColor: '#b08c6a'
                });
            }
        }

        async function toggleEstado(id, nuevoEstado) {
            // Debug: verificar valores
            console.log('Toggle Estado - ID:', id, 'Nuevo Estado:', nuevoEstado, 'Tipo:', typeof nuevoEstado);
            
            const formData = new FormData();
            formData.append('id_categoria', id);
            formData.append('estado', nuevoEstado);

            try {
                const response = await fetch('<?= site_url('admin/categoria_toggle_estado') ?>', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                // Debug: verificar respuesta
                console.log('Respuesta del servidor:', result);

                if(result.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: result.message || 'Estado actualizado correctamente',
                        confirmButtonColor: '#b08c6a',
                        timer: 1500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    
                    <?php if($this->session->userdata('rol') == 'admin'): ?>
                    // Guardar sucursal seleccionada antes de recargar
                    const sucursalActual = document.getElementById('filtroSucursal')?.value;
                    if(sucursalActual) {
                        sessionStorage.setItem('sucursalSeleccionada', sucursalActual);
                    }
                    <?php endif; ?>
                    
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'Error al cambiar el estado',
                        confirmButtonColor: '#b08c6a'
                    });
                }
            } catch(error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor',
                    confirmButtonColor: '#b08c6a'
                });
            }
        }

        async function eliminarCategoria(id, nombre) {
            const result = await Swal.fire({
                title: '¿Estás seguro?',
                html: `¿Deseas eliminar la categoría <strong>"${nombre}"</strong>?<br><br><small class="text-danger">Esta acción no se puede deshacer.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '✓ Sí, eliminar',
                cancelButtonText: '✕ Cancelar',
                reverseButtons: true
            });

            if(!result.isConfirmed) {
                return;
            }

            const formData = new FormData();
            formData.append('id_categoria', id);

            try {
                const response = await fetch('<?= site_url('admin/categoria_eliminar') ?>', {
                    method: 'POST',
                    body: formData
                });

                const resultData = await response.json();

                if(resultData.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: '¡Eliminado!',
                        text: resultData.message,
                        confirmButtonColor: '#b08c6a',
                        timer: 1500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    
                    <?php if($this->session->userdata('rol') == 'admin'): ?>
                    // Guardar sucursal seleccionada antes de recargar
                    const sucursalActual = document.getElementById('filtroSucursal')?.value;
                    if(sucursalActual) {
                        sessionStorage.setItem('sucursalSeleccionada', sucursalActual);
                    }
                    <?php endif; ?>
                    
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: resultData.message || 'Error al eliminar',
                        confirmButtonColor: '#b08c6a'
                    });
                }
            } catch(error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor',
                    confirmButtonColor: '#b08c6a'
                });
            }
        }

        // Filtrar categorías por sucursal (solo para super admin)
        function filtrarPorSucursal() {
            const sucursalId = document.getElementById('filtroSucursal').value;
            const tabla = document.getElementById('tablaCategorias');
            const filas = tabla.querySelectorAll('tbody tr');
            const mensajeSeleccion = document.getElementById('mensajeSeleccionSucursal');
            const tablaContainer = document.getElementById('tablaContainer');
            const contadorBadge = document.getElementById('contadorCategorias');
            let contador = 0;

            // Si no hay sucursal seleccionada, ocultar tabla y mostrar mensaje
            if(sucursalId === '') {
                if(mensajeSeleccion) mensajeSeleccion.style.display = 'block';
                if(tablaContainer) tablaContainer.style.display = 'none';
                if(contadorBadge) contadorBadge.style.display = 'none';
                return;
            }

            // Si hay sucursal seleccionada, mostrar tabla y ocultar mensaje
            if(mensajeSeleccion) mensajeSeleccion.style.display = 'none';
            if(tablaContainer) tablaContainer.style.display = 'block';
            if(contadorBadge) contadorBadge.style.display = 'inline-block';

            // Filtrar filas
            filas.forEach(fila => {
                const sucursalFila = fila.getAttribute('data-sucursal');
                
                if(sucursalFila === sucursalId) {
                    fila.style.display = '';
                    contador++;
                } else {
                    fila.style.display = 'none';
                }
            });

            // Actualizar contador
            if(contadorBadge) {
                contadorBadge.textContent = contador + ' categorías';
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>

