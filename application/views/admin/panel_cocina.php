<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Cocina - Fudo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 12px;
        }
        .list-group-item {
            border: none;
            border-bottom: 1px solid #e9ecef;
            cursor: pointer;
            transition: all 0.2s;
        }
        .list-group-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
        .pointer {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Header con navegación -->
    <div class="admin-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0"><i class="bi bi-fire"></i> Panel Cocina</h2>
                <div class="btn-group">
                    <a href="<?= site_url('admin') ?>" class="btn btn-light btn-sm"><i class="bi bi-box-seam"></i> Pedidos</a>
                    <a href="<?= site_url('admin/categorias') ?>" class="btn btn-light btn-sm"><i class="bi bi-tag-fill"></i> Categorías</a>
                    <a href="<?= site_url('admin/productos') ?>" class="btn btn-light btn-sm"><i class="bi bi-bag-fill"></i> Productos</a>
                    <a href="<?= site_url('mesas') ?>" class="btn btn-light btn-sm"><i class="bi bi-grid-3x3"></i> Mesas</a>
                    <a href="<?= site_url('cocina') ?>" class="btn btn-light btn-sm active"><i class="bi bi-fire"></i> Cocina</a>
                    <a href="<?= site_url('login/salir') ?>" class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Salir</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0"><i class="bi bi-hourglass-split"></i> Pedidos Pendientes</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="listaPedidos">
                            <p class="text-center py-4 text-muted">Cargando...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0"><i class="bi bi-card-list"></i> Detalle del Pedido</h5>
                    </div>
                    <div class="card-body">
                        <div id="detallePedido">
                            <p class="text-center text-muted py-4">Selecciona un pedido para ver detalles.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
+
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
+        async function fetchPendientes(){
+            try{
+                const res = await fetch('<?= site_url('cocina/pendientes_json') ?>');
+                const pedidos = await res.json();
+                renderLista(pedidos);
+            }catch(e){
+                document.getElementById('listaPedidos').innerHTML = '<p>Error cargando pedidos</p>';
+                console.error(e);
+            }
+        }
+
+        function renderLista(pedidos){
+            if(!pedidos || pedidos.length===0){
+                document.getElementById('listaPedidos').innerHTML = '<p>No hay pedidos pendientes.</p>';
+                return;
+            }
+            let html = '<ul class="list-group">';
+            pedidos.forEach(p=>{
+                html += `<li class="list-group-item d-flex justify-content-between align-items-center pointer" onclick="verDetalle(${p.id_pedido})">`
+                    + `<div><strong>#${p.id_pedido}</strong> Mesa: ${p.id_mesa? p.id_mesa : '-'} <br/><small>${p.fecha}</small></div>`
+                    + `<div><span class="badge bg-warning">${p.estado}</span> <button class="btn btn-sm btn-success ms-2" onclick="cambiarEstado(event, ${p.id_pedido}, 'En preparaci\u00f3n')">En preparación</button> <button class="btn btn-sm btn-primary ms-2" onclick="cambiarEstado(event, ${p.id_pedido}, 'Lista')">Listo</button></div>`
+                    + `</li>`;
+            });
+            html += '</ul>';
+            document.getElementById('listaPedidos').innerHTML = html;
+        }
+
+        async function verDetalle(id){
+            const res = await fetch('<?= site_url('cocina/detalle_json') ?>/'+id);
+            const detalle = await res.json();
+            let html = '<table class="table"><thead><tr><th>Producto</th><th>Cant</th><th>Subtotal</th></tr></thead><tbody>';
+            detalle.forEach(d=>{
+                html += `<tr><td>${d.nombre}</td><td>${d.cantidad}</td><td>$${new Intl.NumberFormat('es-CL').format(d.subtotal)}</td></tr>`;
+            });
+            html += '</tbody></table>';
+            document.getElementById('detallePedido').innerHTML = html;
+        }
+
+        async function cambiarEstado(evt, id, estado){
+            evt.stopPropagation();
+            try{
+                const res = await fetch('<?= site_url('admin/actualizar_estado') ?>',{
+                    method:'POST',
+                    headers:{'Content-Type':'application/json'},
+                    body: JSON.stringify({id_pedido:id, estado:estado})
+                });
+                const j = await res.json();
+                if(j.ok){
+                    fetchPendientes();
+                } else {
+                    alert('Error al actualizar estado');
+                }
+            }catch(e){
+                alert('Error al actualizar estado');
+                console.error(e);
+            }
+        }
+
+        // polling cada 5s
+        fetchPendientes();
+        setInterval(fetchPendientes, 5000);
+    </script>
+</body>
+</html>
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+*** End Patch