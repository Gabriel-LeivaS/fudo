<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Cocina - Fudo</title>
    <link href="https://fonts.googleapis.com/css2?fa                    <?php if($tiene_permiso('productos')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('admin/productos') ?>">🛍️ Productos</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if($tiene_permiso('mi_carta')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('admin/mi_carta') ?>">📋 Mi Carta</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if($tiene_permiso('mesas')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('mesas') ?>">🪑 Mesas</a>
                        </li>
                    <?php endif; ?>ght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --accent: #b08c6a;
            --accent-2: #a3c06b;
            --muted: #6c6c6c;
            --card-radius: 14px;
            --shadow: 0 14px 36px rgba(11,11,11,0.06);
            --bg-light: #fbf8f6;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Montserrat', system-ui, sans-serif;
            background: var(--bg-light);
            min-height: 100vh;
            padding: 20px;
            color: #222;
        }

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
            color: white !important;
        }

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
            border-radius: var(--card-radius);
            box-shadow: var(--shadow);
            background: white;
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #f0f0f0;
            padding: 1.25rem 1.5rem;
            font-weight: 700;
            font-size: 18px;
            color: #333;
        }

        .list-group-item {
            border: none;
            border-bottom: 1px solid #e9ecef;
            padding: 1.25rem 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .list-group-item:hover {
            background: linear-gradient(135deg, rgba(176,140,106,0.05) 0%, rgba(163,192,107,0.05) 100%);
            transform: translateX(8px);
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        .badge-custom {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .btn-cocina {
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin: 0 0.25rem;
        }

        .btn-cocina:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .btn-preparar {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            color: white;
        }

        .btn-lista {
            background: linear-gradient(135deg, var(--accent-2) 0%, #8fb355 100%);
            color: white;
        }

        .table {
            margin: 0;
        }

        .table thead th {
            background: var(--bg-light);
            border: none;
            color: #333;
            font-weight: 700;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .pedido-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .pedido-id {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--accent);
        }

        @media (max-width: 768px) {
            .admin-header h1 {
                font-size: 1.5rem;
            }
            
            .btn-cocina {
                padding: 0.4rem 0.8rem;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar moderna -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= site_url('admin') ?>">🍽️ FUDO</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php 
                $rol = $this->session->userdata('rol');
                $permisos = $this->session->userdata('permisos');
                
                // Función helper para verificar permisos
                $tiene_permiso = function($seccion) use ($rol, $permisos) {
                    // Pedidos: Solo admin_sucursal y usuarios con permiso (NO super admin)
                    if($seccion == 'pedidos') {
                        return $rol == 'admin_sucursal' || ($rol == 'usuario' && is_array($permisos) && isset($permisos['pedidos']) && $permisos['pedidos'] === true);
                    }
                    // Resto de secciones: admin y admin_sucursal tienen acceso
                    if($rol == 'admin' || $rol == 'admin_sucursal') return true;
                    if($rol == 'usuario' && is_array($permisos)) {
                        return isset($permisos[$seccion]) && $permisos[$seccion] === true;
                    }
                    return false;
                };
                ?>
                
                <ul class="navbar-nav ms-auto">
                    <?php if($tiene_permiso('pedidos')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('admin') ?>">📦 Pedidos</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if($tiene_permiso('categorias')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('admin/categorias') ?>">🏷️ Categorías</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if($tiene_permiso('productos')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('admin/productos') ?>">�️ Productos</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if($tiene_permiso('micarta')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('admin/mi_carta') ?>">📋 Mi Carta</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if($tiene_permiso('mesas')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('mesas') ?>">🪑 Mesas</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if($tiene_permiso('cocina')): ?>
                        <li class="nav-item">
                            <a class="nav-link active" href="<?= site_url('cocina') ?>">👨‍🍳 Cocina</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if($rol == 'admin' || $rol == 'admin_sucursal'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('admin/usuarios') ?>">👥 Usuarios</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if($rol == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('admin/sucursales') ?>">🏢 Sucursales</a>
                        </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('login/salir') ?>" style="color: #dc3545 !important;">🚪 Salir</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid" style="max-width: 95%;">
        <!-- Admin Header -->
        <div class="admin-header">
            <h1>👨‍🍳 Panel de Cocina</h1>
            <p>Gestiona y prepara los pedidos en tiempo real</p>
        </div>
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">⏳ Pedidos Pendientes</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="listaPedidos">
                            <p class="text-center py-4 text-muted">Cargando pedidos...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">📋 Detalle del Pedido</h5>
                    </div>
                    <div class="card-body">
                        <div id="detallePedido">
                            <p class="text-center text-muted py-4">Selecciona un pedido de la lista para ver los detalles.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const pedidoInicial = <?= isset($id_pedido_inicial) ? $id_pedido_inicial : 'null' ?>;
        
        async function fetchPendientes(){
            try{
                const res = await fetch('<?= site_url('cocina/pendientes_json') ?>');
                const pedidos = await res.json();
                renderLista(pedidos);
                
                // Si hay un pedido inicial, mostrarlo automáticamente
                if(pedidoInicial && pedidos.some(p => p.id_pedido == pedidoInicial)) {
                    verDetalle(pedidoInicial);
                }
            }catch(e){
                document.getElementById('listaPedidos').innerHTML = '<p class="text-center py-4 text-danger">Error cargando pedidos</p>';
                console.error(e);
            }
        }

        function renderLista(pedidos){
            if(!pedidos || pedidos.length===0){
                document.getElementById('listaPedidos').innerHTML = '<p class="text-center py-4 text-muted">No hay pedidos pendientes en este momento.</p>';
                return;
            }
            
            let html = '<ul class="list-group list-group-flush">';
            pedidos.forEach(p=>{
                let badgeColor = 'bg-warning';
                if(p.estado === 'En preparación') badgeColor = 'bg-info';
                if(p.estado === 'Lista') badgeColor = 'bg-success';
                
                html += `<li class="list-group-item" onclick="verDetalle(${p.id_pedido})">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1"><strong>#${p.id_pedido}</strong> - Mesa ${p.id_mesa ? p.id_mesa : 'N/A'}</h6>
                            <small class="text-muted">${p.fecha}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge ${badgeColor} badge-custom mb-2">${p.estado}</span>
                            <div>
                                ${p.estado === 'Pendiente' ? 
                                    `<button class="btn btn-cocina btn-preparar btn-sm" onclick="cambiarEstado(event, ${p.id_pedido}, 'En preparación')">👨‍🍳 Preparar</button>` : ''}
                                ${p.estado !== 'Lista' ? 
                                    `<button class="btn btn-cocina btn-lista btn-sm" onclick="cambiarEstado(event, ${p.id_pedido}, 'Lista')">✅ Listo</button>` : ''}
                            </div>
                        </div>
                    </div>
                </li>`;
            });
            html += '</ul>';
            document.getElementById('listaPedidos').innerHTML = html;
        }

        async function verDetalle(id){
            try {
                const res = await fetch('<?= site_url('cocina/detalle_json') ?>/'+id);
                const detalle = await res.json();
                
                if(!detalle || detalle.length === 0) {
                    document.getElementById('detallePedido').innerHTML = '<p class="text-center text-muted py-4">No hay detalles disponibles.</p>';
                    return;
                }
                
                let total = 0;
                let html = `
                    <div class="pedido-header">
                        <span class="pedido-id">#${id}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>`;
                
                detalle.forEach(d=>{
                    total += parseFloat(d.subtotal);
                    html += `<tr>
                        <td><strong>${d.nombre}</strong></td>
                        <td class="text-center"><span class="badge bg-secondary">${d.cantidad}</span></td>
                        <td class="text-end">$${new Intl.NumberFormat('es-CL').format(d.subtotal)}</td>
                    </tr>`;
                });
                
                html += `</tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-end"><strong>Total:</strong></td>
                            <td class="text-end"><strong style="font-size: 1.2rem; color: var(--accent);">$${new Intl.NumberFormat('es-CL').format(total)}</strong></td>
                        </tr>
                    </tfoot>
                </table>
                </div>`;
                
                document.getElementById('detallePedido').innerHTML = html;
            } catch(e) {
                document.getElementById('detallePedido').innerHTML = '<p class="text-center text-danger py-4">Error cargando detalles</p>';
                console.error(e);
            }
        }

        async function cambiarEstado(evt, id, estado){
            evt.stopPropagation();
            try{
                const res = await fetch('<?= site_url('admin/actualizar_estado') ?>',{
                    method:'POST',
                    headers:{'Content-Type':'application/json'},
                    body: JSON.stringify({id_pedido:id, estado:estado})
                });
                const j = await res.json();
                if(j.ok){
                    fetchPendientes();
                    // Mantener el detalle visible si es el pedido actual
                    setTimeout(() => verDetalle(id), 300);
                } else {
                    alert('Error al actualizar estado del pedido');
                }
            }catch(e){
                alert('Error de conexión al actualizar estado');
                console.error(e);
            }
        }

        // Cargar pedidos al inicio y actualizar cada 5 segundos
        fetchPendientes();
        setInterval(fetchPendientes, 5000);
    </script>
</body>
</html>