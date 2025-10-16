<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de Cocina - Fudo</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin-ui.css') ?>">
    <style>
        /* Cocina-specific styles */
        .list-group-item {
            border: none;
            border-bottom: 1px solid #e9ecef;
            padding: 1.25rem 1.5rem;
            cursor: pointer;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        .btn-cocina {
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-preparar {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            color: white;
        }

        .btn-lista {
            background: linear-gradient(135deg, var(--accent-2) 0%, #8fb355 100%);
            color: white;
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
    </style>
</head>
<body>
    <?php 
    $active_page = 'cocina';
    include(APPPATH . 'views/admin/components/navbar.php'); 
    ?>

    <div class="container-fluid">
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