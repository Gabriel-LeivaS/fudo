<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Mesas - Fudo</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
            white-space: nowrap; 
        }
        
        .nav-link:hover {
            background: var(--bg-light);
            color: var(--accent);
        }
        
        .nav-link.active {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            color: white;
            white-space: nowrap; 
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
        
        /* Mesa Cards */
        .mesa-card {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            background: white;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .mesa-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }
        
        .mesa-card.ocupada {
            border-color: #dc3545;
            background: linear-gradient(135deg, rgba(220,53,69,0.05) 0%, rgba(220,53,69,0.02) 100%);
        }
        
        .mesa-card.libre {
            border-color: var(--accent-2);
            background: linear-gradient(135deg, rgba(163,192,107,0.05) 0%, rgba(163,192,107,0.02) 100%);
        }
        
        .mesa-nombre {
            font-size: 24px;
            font-weight: 800;
            color: #333;
            margin-bottom: 10px;
        }
        
        .mesa-badge {
            font-size: 14px;
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .badge-ocupada {
            background: #dc3545;
            color: white;
        }
        
        .badge-libre {
            background: var(--accent-2);
            color: white;
        }
        
        .btn-action {
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            border: none;
            margin: 5px;
        }
        
        .btn-delete {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }
        
        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220,53,69,0.4);
            color: white;
        }
        
        .btn-liberar {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: white;
        }
        
        .btn-liberar:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(243,156,18,0.4);
            color: white;
        }
        
        .btn-qr {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            color: white;
        }
        
        .btn-qr:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(176,140,106,0.4);
            color: white;
        }
        
        .btn-add-mesa {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            color: white;
            padding: 12px 24px;
            font-weight: 700;
            border-radius: 10px;
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-add-mesa:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(176,140,106,0.4);
            color: white;
        }
        
        /* Estilos de Modal */
        .modal-content {
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .modal-header {
            border-bottom: none;
            padding: 1.5rem 2rem;
        }
        
        .modal-body {
            padding: 2rem;
        }
        
        .modal-footer {
            border-top: 1px solid #f0f0f0;
            padding: 1.25rem 2rem;
        }
        
        .modal-footer .btn-secondary {
            background: #6c757d;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
        }
        
        .modal-footer .btn-secondary:hover {
            background: #5a6268;
        }
        
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            padding: 12px;
            border-radius: 8px;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 0.2rem rgba(176,140,106,0.25);
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        /* Estilos para Toast Notificaciones */
        .toast {
            min-width: 300px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
        }
        
        .toast-body {
            padding: 16px 20px;
            font-size: 15px;
        }
        
        .toast .btn-close {
            margin-right: 8px;
        }
        
        /* Modal de confirmación */
        #confirmButton {
            border: none;
            transition: all 0.3s ease;
        }
        
        #confirmButton:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.3);
        }
        
        #confirmMessage {
            line-height: 1.6;
        }
        
        /* Buscador */
        .search-box {
            position: relative;
        }
        
        .search-box input {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        .search-box input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 0.2rem rgba(176,140,106,0.25);
            outline: none;
        }
        
        .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 0.2rem rgba(176,140,106,0.25);
        }
        
        @media (max-width: 768px) {
            .admin-header h1 {
                font-size: 1.5rem;
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
                    <a href="<?= site_url('admin/categorias') ?>" class="nav-link">🏷️ Categorías</a>
                <?php endif; ?>
                
                <?php if($tiene_permiso('productos')): ?>
                    <a href="<?= site_url('admin/productos') ?>" class="nav-link">🛍️ Productos</a>
                <?php endif; ?>
                
                <?php if($tiene_permiso('mi_carta')): ?>
                    <a href="<?= site_url('admin/mi_carta') ?>" class="nav-link">📋 Mi Carta</a>
                <?php endif; ?>
                
                <?php if($tiene_permiso('mesas')): ?>
                    <a href="<?= site_url('mesas') ?>" class="nav-link active">🪑 Mesas</a>
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
                
                <a href="<?= site_url('login/salir') ?>" class="btn btn-danger btn-action">🚪 Salir</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <!-- Admin Header -->
        <div class="admin-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1>🪑 Gestión de Mesas</h1>
                    <p>Administra las mesas de tu sucursal</p>
                </div>
                <?php 
                // Función helper para verificar permisos
                $rol = $this->session->userdata('rol');
                $permisos = $this->session->userdata('permisos');
                
                $tiene_permiso_mesas = function() use ($rol, $permisos) {
                    // Super admin: Solo acceso a secciones administrativas
                    if($rol == 'admin') {
                        return in_array('mesas', ['categorias', 'productos', 'usuarios', 'sucursales']);
                    }
                    // Admin sucursal: acceso completo
                    if($rol == 'admin_sucursal') {
                        return true;
                    }
                    // Usuario: verificar permisos específicos
                    if($rol == 'usuario' && is_array($permisos)) {
                        return isset($permisos['mesas']) && $permisos['mesas'] === true;
                    }
                    return false;
                };
                ?>
                <?php if($tiene_permiso_mesas()): ?>
                    <button class="btn btn-add-mesa" data-bs-toggle="modal" data-bs-target="#modalCrearMesa">
                        ➕ Nueva Mesa
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Buscador -->
        <div class="row mb-4">
            <div class="col-md-6 col-lg-4">
                <div class="search-box">
                    <input type="text" id="searchMesa" class="form-control" placeholder="🔍 Buscar mesa por número..." onkeyup="filtrarMesas()">
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <select id="filtroEstado" class="form-select" onchange="filtrarMesas()">
                    <option value="todas">Todas las mesas</option>
                    <option value="libres">🟢 Solo libres</option>
                    <option value="ocupadas">🔴 Solo ocupadas</option>
                </select>
            </div>
        </div>

        <!-- Grid de Mesas -->
        <div class="row">
            <?php if(empty($mesas)): ?>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <h5 class="text-muted">No hay mesas registradas</h5>
                            <p>Crea tu primera mesa haciendo click en "Nueva Mesa"</p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach($mesas as $mesa): ?>
                    <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                        <div class="mesa-card <?= $mesa->ocupada ? 'ocupada' : 'libre' ?>">
                            <div class="text-center">
                                <div class="mesa-nombre"><?= $mesa->nombre ?></div>
                                <span class="mesa-badge <?= $mesa->ocupada ? 'badge-ocupada' : 'badge-libre' ?>">
                                    <?= $mesa->ocupada ? '🔴 Ocupada' : '🟢 Libre' ?>
                                </span>
                                
                                <?php if(isset($mesa->nombre_sucursal) && $rol == 'admin'): ?>
                                    <div class="text-muted mb-3" style="font-size: 13px;">
                                        <strong>Sucursal:</strong> <?= $mesa->nombre_sucursal ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="d-flex flex-column gap-2 mt-3">
                                    <button class="btn btn-qr btn-action" onclick="generarQR(<?= $mesa->id_mesa ?>)">
                                        📱 Generar QR
                                    </button>
                                    
                                    <?php if($tiene_permiso_mesas()): ?>
                                        <?php if($mesa->ocupada): ?>
                                            <button class="btn btn-liberar btn-action" onclick="liberarMesa(<?= $mesa->id_mesa ?>, '<?= $mesa->nombre ?>')">
                                                🔓 Liberar Mesa
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-delete btn-action" onclick="eliminarMesa(<?= $mesa->id_mesa ?>, '<?= $mesa->nombre ?>')">
                                                🗑️ Eliminar
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Crear Mesa -->
    <div class="modal fade" id="modalCrearMesa" tabindex="-1" aria-labelledby="modalCrearMesaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%); color: white;">
                    <h5 class="modal-title" id="modalCrearMesaLabel">➕ Nueva Mesa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p style="font-size: 16px; color: #333; margin-bottom: 1.5rem;">
                        Se creará una nueva mesa con numeración automática consecutiva.
                    </p>
                    
                    <form id="formCrearMesa">
                        <?php if($rol == 'admin'): ?>
                            <div class="mb-3">
                                <label for="sucursalMesa" class="form-label">Sucursal</label>
                                <select class="form-select" id="sucursalMesa" name="id_sucursal" required>
                                    <option value="">Seleccione una sucursal</option>
                                    <!-- Aquí se cargarían las sucursales dinámicamente -->
                                </select>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="id_sucursal" value="<?= $id_sucursal ?>">
                        <?php endif; ?>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-add-mesa" onclick="crearMesa()">Crear Mesa</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor de Notificaciones Toast -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div id="toastNotificacion" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage" style="font-weight: 600;">
                    <!-- Mensaje dinámico -->
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-labelledby="modalConfirmacionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" id="confirmHeader" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white;">
                    <h5 class="modal-title" id="modalConfirmacionLabel">
                        <span id="confirmIcon">⚠️</span> Confirmar Acción
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 2rem;">
                    <p id="confirmMessage" style="font-size: 16px; margin: 0; color: #333;"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn" id="confirmButton" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; font-weight: 600; padding: 10px 24px; border-radius: 8px;">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para mostrar notificaciones Toast
        function mostrarToast(mensaje, tipo = 'success') {
            const toastEl = document.getElementById('toastNotificacion');
            const toastBody = document.getElementById('toastMessage');
            
            // Configurar colores según el tipo
            if(tipo === 'success') {
                toastEl.style.background = 'linear-gradient(135deg, var(--accent-2) 0%, #7da83f 100%)';
                toastEl.style.color = 'white';
            } else if(tipo === 'error') {
                toastEl.style.background = 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)';
                toastEl.style.color = 'white';
            } else if(tipo === 'warning') {
                toastEl.style.background = 'linear-gradient(135deg, #f39c12 0%, #e67e22 100%)';
                toastEl.style.color = 'white';
            }
            
            toastBody.textContent = mensaje;
            
            const toast = new bootstrap.Toast(toastEl, {
                animation: true,
                autohide: true,
                delay: 3000
            });
            
            toast.show();
        }
        
        // Función para confirmar acciones con modal personalizado
        function confirmarAccion(mensaje, tipo = 'danger', iconoEmoji = '⚠️') {
            return new Promise((resolve) => {
                const modal = new bootstrap.Modal(document.getElementById('modalConfirmacion'));
                const confirmButton = document.getElementById('confirmButton');
                const confirmMessage = document.getElementById('confirmMessage');
                const confirmHeader = document.getElementById('confirmHeader');
                const confirmIcon = document.getElementById('confirmIcon');
                
                // Configurar mensaje
                confirmMessage.innerHTML = mensaje.replace(/\n/g, '<br>');
                confirmIcon.textContent = iconoEmoji;
                
                // Configurar colores según tipo
                if(tipo === 'danger') {
                    confirmHeader.style.background = 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)';
                    confirmButton.style.background = 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)';
                } else if(tipo === 'warning') {
                    confirmHeader.style.background = 'linear-gradient(135deg, #f39c12 0%, #e67e22 100%)';
                    confirmButton.style.background = 'linear-gradient(135deg, #f39c12 0%, #e67e22 100%)';
                }
                
                // Limpiar eventos previos
                const newConfirmButton = confirmButton.cloneNode(true);
                confirmButton.parentNode.replaceChild(newConfirmButton, confirmButton);
                
                // Evento de confirmación
                newConfirmButton.addEventListener('click', () => {
                    modal.hide();
                    resolve(true);
                });
                
                // Evento de cancelación
                document.getElementById('modalConfirmacion').addEventListener('hidden.bs.modal', () => {
                    resolve(false);
                }, { once: true });
                
                modal.show();
            });
        }
    </script>
    <script>
        async function crearMesa() {
            const form = document.getElementById('formCrearMesa');
            const formData = new FormData(form);
            
            // Validar que se haya seleccionado sucursal si es admin
            <?php if($rol == 'admin'): ?>
            const sucursal = formData.get('id_sucursal');
            if(!sucursal) {
                mostrarToast('Debe seleccionar una sucursal', 'error');
                return;
            }
            <?php endif; ?>
            
            try {
                const response = await fetch('<?= site_url('mesas/crear') ?>', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if(result.success) {
                    // Cerrar el modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalCrearMesa'));
                    modal.hide();
                    
                    mostrarToast(result.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    mostrarToast('Error: ' + result.message, 'error');
                }
            } catch(error) {
                console.error('Error:', error);
                mostrarToast('Error al crear la mesa', 'error');
            }
        }
        
        async function eliminarMesa(id, nombre) {
            const confirmado = await confirmarAccion(
                `¿Está seguro de eliminar la mesa <strong>"${nombre}"</strong>?<br><br>Esta acción no se puede deshacer.`,
                'danger',
                '🗑️'
            );
            
            if(!confirmado) {
                return;
            }
            
            try {
                const response = await fetch('<?= site_url('mesas/eliminar/') ?>' + id, {
                    method: 'POST'
                });
                
                const result = await response.json();
                
                if(result.success) {
                    mostrarToast(result.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    mostrarToast('Error: ' + result.message, 'error');
                }
            } catch(error) {
                console.error('Error:', error);
                mostrarToast('Error al eliminar la mesa', 'error');
            }
        }
        
        async function liberarMesa(id, nombre) {
            const confirmado = await confirmarAccion(
                `¿Está seguro de liberar la mesa <strong>"${nombre}"</strong>?<br><br>Esto marcará todos los pedidos activos como completados.`,
                'warning',
                '🔓'
            );
            
            if(!confirmado) {
                return;
            }
            
            try {
                const response = await fetch('<?= site_url('mesas/liberar/') ?>' + id, {
                    method: 'POST'
                });
                
                const result = await response.json();
                
                if(result.success) {
                    mostrarToast(result.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    mostrarToast('Error: ' + result.message, 'error');
                }
            } catch(error) {
                console.error('Error:', error);
                mostrarToast('Error al liberar la mesa', 'error');
            }
        }
        
        async function generarQR(id) {
            try {
                const response = await fetch('<?= site_url('mesas/generar_qr/') ?>' + id, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const result = await response.json();
                
                if(result.success) {
                    mostrarToast('QR generado exitosamente', 'success');
                    // Opcional: abrir el QR en una nueva ventana
                    setTimeout(() => {
                        window.open('<?= base_url() ?>' + result.path, '_blank');
                    }, 500);
                } else {
                    mostrarToast('Error: ' + result.message, 'error');
                }
            } catch(error) {
                console.error('Error:', error);
                mostrarToast('Error al generar QR', 'error');
            }
        }
        
        // Función para filtrar mesas
        function filtrarMesas() {
            const searchValue = document.getElementById('searchMesa').value.toLowerCase();
            const filtroEstado = document.getElementById('filtroEstado').value;
            const mesasCards = document.querySelectorAll('.mesa-card');
            let visibleCount = 0;
            
            mesasCards.forEach(card => {
                const cardParent = card.closest('.col-md-6');
                const mesaNombre = card.querySelector('.mesa-nombre').textContent.toLowerCase();
                const mesaBadge = card.querySelector('.mesa-badge');
                const esOcupada = mesaBadge.classList.contains('badge-ocupada');
                
                // Filtro por búsqueda
                const matchSearch = mesaNombre.includes(searchValue);
                
                // Filtro por estado
                let matchEstado = true;
                if(filtroEstado === 'libres') {
                    matchEstado = !esOcupada;
                } else if(filtroEstado === 'ocupadas') {
                    matchEstado = esOcupada;
                }
                
                // Mostrar u ocultar
                if(matchSearch && matchEstado) {
                    cardParent.style.display = '';
                    visibleCount++;
                } else {
                    cardParent.style.display = 'none';
                }
            });
            
            // Mensaje si no hay resultados
            let noResultsMsg = document.getElementById('noResultsMessage');
            if(visibleCount === 0) {
                if(!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.id = 'noResultsMessage';
                    noResultsMsg.className = 'col-12 text-center py-5';
                    noResultsMsg.innerHTML = '<h5 class="text-muted">No se encontraron mesas</h5><p>Intenta con otro filtro o búsqueda</p>';
                    document.querySelector('.row:has(.mesa-card)').appendChild(noResultsMsg);
                }
                noResultsMsg.style.display = 'block';
            } else {
                if(noResultsMsg) {
                    noResultsMsg.style.display = 'none';
                }
            }
        }
    </script>
</body>
</html>

