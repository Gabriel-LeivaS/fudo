<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Pedidos - Fudo</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin-ui.css') ?>">
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
                    <a href="<?= site_url('admin') ?>" class="nav-link active">📦 Pedidos</a>
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
            <h1>📦 Gestión de Pedidos</h1>
            <p>Administra los pedidos en tiempo real de tu sucursal</p>
        </div>
        <div class="card">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0">📋 Listado de Pedidos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="60">ID</th>
                                <th width="80">Mesa</th>
                                <th>Cliente</th>
                                <th width="150">Fecha</th>
                                <th width="120" class="text-center">Estado</th>
                                <th width="120" class="text-end">Total</th>
                                <th width="250" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($pedidos)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No hay pedidos registrados
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pedidos as $p): ?>
                                <tr>
                                    <td><strong>#<?= $p->id_pedido ?></strong></td>
                                    <td><?= $p->id_mesa ?? '-' ?></td>
                                    <td>
                                        <div>
                                            <strong>ID: <?= $p->id_cliente ?></strong>
                                        </div>
                                        <small class="text-muted">
                                            <!-- Aquí irán los datos del cliente cuando se actualice el backend -->
                                        </small>
                                    </td>
                                    <td><?= $p->fecha ?></td>
                                    <td class="text-center">
                                        <?php if($p->estado == 'Pendiente'): ?>
                                            <span class="badge bg-warning badge-custom">Pendiente</span>
                                        <?php elseif($p->estado == 'En preparación'): ?>
                                            <span class="badge bg-info badge-custom">En preparación</span>
                                        <?php else: ?>
                                            <span class="badge bg-success badge-custom">Lista</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <strong>$<?= number_format($p->total, 0, ',', '.') ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary btn-action" 
                                                onclick="verDetallePedido(<?= $p->id_pedido ?>)"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalDetalle">
                                            👁️ Ver
                                        </button>
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

    <!-- Modal de Detalle del Pedido -->
    <div class="modal fade" id="modalDetalle" tabindex="-1" aria-labelledby="modalDetalleLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%); color: white; border: none;">
                    <h5 class="modal-title" id="modalDetalleLabel">📋 Detalle del Pedido</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalDetalleBody" style="padding: 2rem;">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        async function cambiarEstado(id, nuevoEstado) {
            try {
                const response = await fetch('<?= site_url('admin/actualizar_estado') ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({id_pedido: id, estado: nuevoEstado})
                });
                
                const result = await response.json();
                
                if(result.ok) {
                    // Actualizar inmediatamente en lugar de recargar
                    actualizarPedidos();
                } else {
                    alert('Error al actualizar el estado');
                }
            } catch(error) {
                console.error('Error:', error);
                alert('Error de conexión');
            }
        }

        // Auto-actualización de pedidos cada 5 segundos
        async function actualizarPedidos() {
            try {
                const response = await fetch('<?= site_url('admin/obtener_pedidos_ajax') ?>');
                const data = await response.json();
                
                if(data.pedidos) {
                    renderizarPedidos(data.pedidos);
                }
            } catch(error) {
                console.error('Error al actualizar pedidos:', error);
            }
        }

        function renderizarPedidos(pedidos) {
            const tbody = document.querySelector('tbody');
            
            if(pedidos.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No hay pedidos registrados
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = pedidos.map(p => {
                let estadoBadge = '';
                if(p.estado === 'Pendiente') {
                    estadoBadge = '<span class="badge bg-warning badge-custom">Pendiente</span>';
                } else if(p.estado === 'En preparación') {
                    estadoBadge = '<span class="badge bg-info badge-custom">En preparación</span>';
                } else {
                    estadoBadge = '<span class="badge bg-success badge-custom">Lista</span>';
                }
                
                let botones = `<button 
                    class="btn btn-sm" 
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 6px 12px; border-radius: 8px; font-weight: 600; display: inline-block; margin: 2px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(102,126,234,0.3); cursor: pointer;"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(102,126,234,0.4)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(102,126,234,0.3)';"
                    onclick="verDetallePedido(${p.id_pedido})"
                    data-bs-toggle="modal" 
                    data-bs-target="#modalDetalle">
                    👁️ Ver
                </button>`;
                
                return `
                    <tr>
                        <td><strong>#${p.id_pedido}</strong></td>
                        <td>${p.id_mesa || '-'}</td>
                        <td>
                            <div><strong>ID: ${p.id_cliente}</strong></div>
                            <small class="text-muted"></small>
                        </td>
                        <td>${p.fecha}</td>
                        <td class="text-center">${estadoBadge}</td>
                        <td class="text-end"><strong>$${new Intl.NumberFormat('es-CL').format(p.total)}</strong></td>
                        <td class="text-center">${botones}</td>
                    </tr>
                `;
            }).join('');
        }

        // Iniciar auto-actualización cada 5 segundos
        setInterval(actualizarPedidos, 5000);

        // Función para cargar detalle del pedido en el modal
        async function verDetallePedido(id_pedido) {
            const modalBody = document.getElementById('modalDetalleBody');
            
            // Mostrar spinner de carga
            modalBody.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Cargando detalle del pedido...</p>
                </div>
            `;
            
            try {
                const response = await fetch('<?= site_url('admin/detalle_pedido_json/') ?>' + id_pedido);
                const data = await response.json();
                
                if(data.error) {
                    modalBody.innerHTML = `
                        <div class="alert alert-danger">
                            <strong>Error:</strong> ${data.error}
                        </div>
                    `;
                    return;
                }
                
                const pedido = data.pedido;
                const detalle = data.detalle;
                
                // Determinar badge de estado
                let estadoBadge = '';
                if(pedido.estado === 'Pendiente') {
                    estadoBadge = '<span class="badge bg-warning" style="font-size: 1rem; padding: 0.5rem 1rem;">⏳ Pendiente</span>';
                } else if(pedido.estado === 'En preparación') {
                    estadoBadge = '<span class="badge bg-info" style="font-size: 1rem; padding: 0.5rem 1rem;">👨‍🍳 En preparación</span>';
                } else {
                    estadoBadge = '<span class="badge bg-success" style="font-size: 1rem; padding: 0.5rem 1rem;">✅ Lista</span>';
                }
                
                // Renderizar contenido
                let html = `
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h3 style="color: var(--accent); font-weight: 700;">Pedido #${pedido.id_pedido}</h3>
                            <p class="text-muted mb-2">📅 ${pedido.fecha}</p>
                            <p class="mb-0"><strong>Mesa:</strong> ${pedido.id_mesa || 'N/A'}</p>
                            <p class="mb-0"><strong>Cliente ID:</strong> ${pedido.id_cliente}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            ${estadoBadge}
                            <h2 class="mt-3 mb-0" style="color: var(--accent-2); font-weight: 700;">
                                $${new Intl.NumberFormat('es-CL').format(pedido.total)}
                            </h2>
                        </div>
                    </div>
                    
                    <hr style="border-top: 2px solid var(--bg-light);">
                    
                    <h5 class="mb-3" style="font-weight: 700;">📦 Productos</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead style="background: var(--bg-light);">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                detalle.forEach(item => {
                    const precioUnitario = item.subtotal / item.cantidad;
                    html += `
                        <tr>
                            <td><strong>${item.nombre}</strong></td>
                            <td class="text-center">
                                <span class="badge bg-secondary">${item.cantidad}</span>
                            </td>
                            <td class="text-end">$${new Intl.NumberFormat('es-CL').format(precioUnitario)}</td>
                            <td class="text-end"><strong>$${new Intl.NumberFormat('es-CL').format(item.subtotal)}</strong></td>
                        </tr>
                    `;
                });
                
                html += `
                            </tbody>
                            <tfoot style="border-top: 2px solid var(--accent);">
                                <tr>
                                    <td colspan="3" class="text-end"><strong style="font-size: 1.1rem;">Total:</strong></td>
                                    <td class="text-end">
                                        <strong style="font-size: 1.3rem; color: var(--accent);">
                                            $${new Intl.NumberFormat('es-CL').format(pedido.total)}
                                        </strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                `;
                
                modalBody.innerHTML = html;
                
            } catch(error) {
                console.error('Error al cargar detalle:', error);
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <strong>Error:</strong> No se pudo cargar el detalle del pedido. Por favor, intenta nuevamente.
                    </div>
                `;
            }
        }
    </script>
</body>
</html>
