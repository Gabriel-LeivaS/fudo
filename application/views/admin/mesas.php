<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Mesas - Fudo</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin-ui.css') ?>">
    <style>
        /* Mesa-specific styles */
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
        
        /* Toast styles */
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
        
        /* Search box */
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
        <div class="row" id="mesasContainer">
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
                        <div class="mesa-card <?= $mesa->ocupada ? 'ocupada' : 'libre' ?>" data-mesa-id="<?= $mesa->id_mesa ?>">
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
                                    
                                    <?php if($mesa->ocupada): ?>
                                        <button class="btn btn-info btn-action" onclick="verHistorial(<?= $mesa->id_mesa ?>, '<?= $mesa->nombre ?>')">
                                            📋 Ver Historial
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if($tiene_permiso_mesas()): ?>
                                        <?php if($mesa->ocupada): ?>
                                            <button class="btn btn-success btn-action" onclick="cobrarMesa(<?= $mesa->id_mesa ?>, '<?= $mesa->nombre ?>')">
                                                💰 Cobrar Mesa
                                            </button>
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

    <!-- Modal QR -->
    <div class="modal fade" id="modalQR" tabindex="-1" aria-labelledby="modalQRLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%); color: white;">
                    <h5 class="modal-title" id="modalQRLabel">
                        📱 Código QR - <span id="qrMesaNombre"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" style="padding: 2rem;">
                    <div id="qrContainer">
                        <div id="qrLoading" class="d-flex flex-column align-items-center">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="visually-hidden">Generando QR...</span>
                            </div>
                            <p class="text-muted">Generando código QR...</p>
                        </div>
                        <div id="qrContent" style="display: none;">
                            <img id="qrImage" src="" alt="Código QR" class="img-fluid mb-3" style="max-width: 300px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                            <div class="alert alert-info" style="background: linear-gradient(135deg, rgba(176,140,106,0.1) 0%, rgba(163,192,107,0.1) 100%); border: 1px solid var(--accent); border-radius: 10px;">
                                <strong>📋 Instrucciones:</strong><br>
                                Los clientes pueden escanear este código QR para acceder directamente al menú de esta mesa.
                            </div>
                        </div>
                        <div id="qrError" style="display: none;">
                            <div class="alert alert-danger" style="border-radius: 10px;">
                                <strong>❌ Error:</strong><br>
                                <span id="qrErrorMessage"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-qr" id="btnDescargarQR" style="display: none;" onclick="descargarQR()">
                        💾 Descargar QR
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Historial Mesa -->
    <div class="modal fade" id="modalHistorial" tabindex="-1" aria-labelledby="modalHistorialLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%); color: white;">
                    <h5 class="modal-title" id="modalHistorialLabel">
                        📋 Historial de Pedidos - <span id="historialMesaNombre"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 2rem;">
                    <div id="historialContainer">
                        <div id="historialLoading" class="d-flex flex-column align-items-center">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="visually-hidden">Cargando historial...</span>
                            </div>
                            <p class="text-muted">Cargando historial de pedidos...</p>
                        </div>
                        <div id="historialContent" style="display: none;">
                            <div class="alert alert-info mb-4" style="background: linear-gradient(135deg, rgba(176,140,106,0.1) 0%, rgba(163,192,107,0.1) 100%); border: 1px solid var(--accent); border-radius: 10px;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><strong>💰 Total a Cobrar:</strong></span>
                                    <span class="h4 mb-0" id="totalMesa" style="color: var(--accent); font-weight: 800;">$0</span>
                                </div>
                            </div>
                            <div id="listaPedidos"></div>
                        </div>
                        <div id="historialError" style="display: none;">
                            <div class="alert alert-danger" style="border-radius: 10px;">
                                <strong>❌ Error:</strong><br>
                                <span id="historialErrorMessage"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" id="btnCobrarDesdeHistorial" style="display: none;" onclick="cobrarDesdeHistorial()">
                        💰 Cobrar Mesa
                    </button>
                </div>
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
        
        let qrImagePath = '';
        
        async function generarQR(id) {
            // Obtener nombre de la mesa
            const mesaCard = document.querySelector(`button[onclick="generarQR(${id})"]`).closest('.mesa-card');
            const mesaNombre = mesaCard.querySelector('.mesa-nombre').textContent;
            
            // Configurar modal
            document.getElementById('qrMesaNombre').textContent = mesaNombre;
            document.getElementById('qrLoading').style.display = 'block';
            document.getElementById('qrContent').style.display = 'none';
            document.getElementById('qrError').style.display = 'none';
            document.getElementById('btnDescargarQR').style.display = 'none';
            
            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('modalQR'));
            modal.show();
            
            try {
                const response = await fetch('<?= site_url('mesas/generar_qr/') ?>' + id, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const result = await response.json();
                
                // Ocultar loading
                document.getElementById('qrLoading').style.display = 'none';
                
                if(result.success) {
                    // Mostrar QR
                    qrImagePath = result.path;
                    document.getElementById('qrImage').src = '<?= base_url() ?>' + result.path + '?t=' + Date.now();
                    document.getElementById('qrContent').style.display = 'block';
                    document.getElementById('btnDescargarQR').style.display = 'inline-block';
                    
                    mostrarToast('QR generado exitosamente', 'success');
                } else {
                    // Mostrar error
                    document.getElementById('qrErrorMessage').textContent = result.message;
                    document.getElementById('qrError').style.display = 'block';
                    mostrarToast('Error: ' + result.message, 'error');
                }
            } catch(error) {
                console.error('Error:', error);
                document.getElementById('qrLoading').style.display = 'none';
                document.getElementById('qrErrorMessage').textContent = 'Error de conexión al generar el QR';
                document.getElementById('qrError').style.display = 'block';
                mostrarToast('Error al generar QR', 'error');
            }
        }
        
        function descargarQR() {
            if(qrImagePath) {
                const link = document.createElement('a');
                link.href = '<?= base_url() ?>' + qrImagePath;
                link.download = 'qr_' + document.getElementById('qrMesaNombre').textContent.replace(/\s+/g, '_').toLowerCase() + '.png';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                mostrarToast('QR descargado exitosamente', 'success');
            }
        }

        let currentMesaId = null;

        async function verHistorial(id, nombre) {
            currentMesaId = id;
            
            // Configurar modal
            document.getElementById('historialMesaNombre').textContent = nombre;
            document.getElementById('historialLoading').style.display = 'block';
            document.getElementById('historialContent').style.display = 'none';
            document.getElementById('historialError').style.display = 'none';
            document.getElementById('btnCobrarDesdeHistorial').style.display = 'none';
            
            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('modalHistorial'));
            modal.show();
            
            try {
                const response = await fetch('<?= site_url('mesas/historial/') ?>' + id);
                const result = await response.json();
                
                // Ocultar loading
                document.getElementById('historialLoading').style.display = 'none';
                
                if(result.success) {
                    // Mostrar total
                    document.getElementById('totalMesa').textContent = '$' + parseFloat(result.total_mesa).toLocaleString();
                    
                    // Generar lista de pedidos
                    const listaPedidos = document.getElementById('listaPedidos');
                    listaPedidos.innerHTML = '';
                    
                    if(result.pedidos.length === 0) {
                        listaPedidos.innerHTML = '<div class="alert alert-warning">No hay pedidos activos para esta mesa.</div>';
                    } else {
                        result.pedidos.forEach(pedido => {
                            const pedidoHtml = `
                                <div class="card mb-3" style="border-radius: 10px;">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>Pedido #${pedido.id_pedido}</strong>
                                            <small class="text-muted ms-2">${new Date(pedido.fecha).toLocaleString()}</small>
                                        </div>
                                        <div>
                                            <span class="badge ${getEstadoBadgeClass(pedido.estado)}">${pedido.estado}</span>
                                            <span class="badge bg-success ms-1">$${parseFloat(pedido.total).toLocaleString()}</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        ${pedido.cliente_nombre ? `<p><strong>Cliente:</strong> ${pedido.cliente_nombre}</p>` : ''}
                                        ${pedido.notas ? `<p><strong>Notas:</strong> ${pedido.notas}</p>` : ''}
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Producto</th>
                                                        <th>Cantidad</th>
                                                        <th>Precio Unit.</th>
                                                        <th>Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    ${pedido.detalle.map(item => `
                                                        <tr>
                                                            <td>${item.nombre}</td>
                                                            <td>${item.cantidad}</td>
                                                            <td>$${parseFloat(item.precio).toLocaleString()}</td>
                                                            <td>$${parseFloat(item.subtotal).toLocaleString()}</td>
                                                        </tr>
                                                    `).join('')}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            `;
                            listaPedidos.innerHTML += pedidoHtml;
                        });
                        
                        // Mostrar botón de cobrar si hay pedidos
                        if(result.total_mesa > 0) {
                            document.getElementById('btnCobrarDesdeHistorial').style.display = 'inline-block';
                        }
                    }
                    
                    document.getElementById('historialContent').style.display = 'block';
                } else {
                    // Mostrar error
                    document.getElementById('historialErrorMessage').textContent = result.message;
                    document.getElementById('historialError').style.display = 'block';
                }
            } catch(error) {
                console.error('Error:', error);
                document.getElementById('historialLoading').style.display = 'none';
                document.getElementById('historialErrorMessage').textContent = 'Error de conexión al cargar el historial';
                document.getElementById('historialError').style.display = 'block';
            }
        }

        function getEstadoBadgeClass(estado) {
            switch(estado) {
                case 'Pendiente': return 'bg-warning';
                case 'En preparación': return 'bg-info';
                case 'Lista': return 'bg-primary';
                case 'Completado': return 'bg-success';
                default: return 'bg-secondary';
            }
        }

        async function cobrarMesa(id, nombre) {
            const confirmado = await confirmarAccion(
                `¿Está seguro de cobrar la mesa <strong>"${nombre}"</strong>?<br><br>Esto marcará todos los pedidos activos como completados y liberará la mesa.`,
                'warning',
                '💰'
            );
            
            if(!confirmado) {
                return;
            }
            
            await procesarCobro(id);
        }

        async function cobrarDesdeHistorial() {
            if(!currentMesaId) return;
            
            const confirmado = await confirmarAccion(
                `¿Confirma el cobro de esta mesa?<br><br>Se completarán todos los pedidos activos y se liberará la mesa.`,
                'warning',
                '💰'
            );
            
            if(!confirmado) {
                return;
            }
            
            // Cerrar modal de historial
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalHistorial'));
            modal.hide();
            
            await procesarCobro(currentMesaId);
        }

        async function procesarCobro(id) {
            try {
                const response = await fetch('<?= site_url('mesas/cobrar/') ?>' + id, {
                    method: 'POST'
                });
                
                const result = await response.json();
                
                if(result.success) {
                    mostrarToast(`Mesa cobrada exitosamente. Total: $${parseFloat(result.total_cobrado).toLocaleString()}`, 'success');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    mostrarToast('Error: ' + result.message, 'error');
                }
            } catch(error) {
                console.error('Error:', error);
                mostrarToast('Error al procesar el cobro', 'error');
            }
        }

        // Auto-actualización de mesas cada 10 segundos (igual que pedidos)
        async function actualizarMesas() {
            try {
                const response = await fetch('<?= site_url('mesas/obtener_mesas_ajax') ?>');
                const data = await response.json();
                
                if(data.mesas) {
                    actualizarEstadoMesas(data.mesas);
                }
            } catch(error) {
                console.error('Error al actualizar mesas:', error);
            }
        }

        function actualizarEstadoMesas(mesasActualizadas) {
            // Regenerar completamente las tarjetas de mesa (igual que pedidos regenera la tabla)
            const mesasContainer = document.getElementById('mesasContainer');
            if(!mesasContainer) return;
            
            mesasContainer.innerHTML = mesasActualizadas.map(mesa => {
                // Normalizar nombre de mesa
                let nombreMesa = mesa.nombre.toLowerCase().includes('mesa') ? mesa.nombre : 'Mesa ' + mesa.nombre;
                
                // Determinar estado y clase CSS
                let estadoClase = mesa.ocupada ? 'ocupada' : 'libre';
                let estadoBadge = mesa.ocupada ? 
                    '<span class="mesa-badge badge-ocupada">🔴 Ocupada</span>' : 
                    '<span class="mesa-badge badge-libre">🟢 Libre</span>';
                
                let botonesOcupada = '';
                if(mesa.ocupada) {
                    botonesOcupada = `
                        <button class="btn btn-info btn-action" onclick="verHistorial(${mesa.id_mesa}, '${nombreMesa}')">
                            📋 Ver Historial
                        </button>
                    `;
                    
                    // Solo agregar botones de cobro/liberar si tiene permisos
                    if(<?= $tiene_permiso_mesas() ? 'true' : 'false' ?>) {
                        botonesOcupada += `
                            <button class="btn btn-success btn-action" onclick="cobrarMesa(${mesa.id_mesa}, '${nombreMesa}')">
                                💰 Cobrar Mesa
                            </button>
                            <button class="btn btn-liberar btn-action" onclick="liberarMesa(${mesa.id_mesa}, '${nombreMesa}')">
                                🔓 Liberar Mesa
                            </button>
                        `;
                    }
                }
                
                let botonEliminar = '';
                if(!mesa.ocupada && <?= $tiene_permiso_mesas() ? 'true' : 'false' ?>) {
                    botonEliminar = `
                        <button class="btn btn-delete btn-action" onclick="eliminarMesa(${mesa.id_mesa}, '${nombreMesa}')">
                            🗑️ Eliminar
                        </button>
                    `;
                }
                
                // Mostrar sucursal solo si es admin
                let infoSucursal = '';
                if(<?= $rol == 'admin' ? 'true' : 'false' ?>) {
                    infoSucursal = `
                        <div class="text-muted mb-3" style="font-size: 13px;">
                            <strong>Sucursal:</strong> ${mesa.nombre_sucursal || 'N/A'}
                        </div>
                    `;
                }
                
                return `
                    <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                        <div class="mesa-card ${estadoClase}" data-mesa-id="${mesa.id_mesa}">
                            <div class="text-center">
                                <div class="mesa-nombre">${nombreMesa}</div>
                                ${estadoBadge}
                                
                                ${infoSucursal}
                                
                                <div class="d-flex flex-column gap-2 mt-3">
                                    <button class="btn btn-qr btn-action" onclick="generarQR(${mesa.id_mesa})">
                                        📱 Generar QR
                                    </button>
                                    
                                    ${botonesOcupada}
                                    ${botonEliminar}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Iniciar auto-actualización cada 10 segundos (igual que pedidos pero cada 10 seg)
        setInterval(actualizarMesas, 10000);
        
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

