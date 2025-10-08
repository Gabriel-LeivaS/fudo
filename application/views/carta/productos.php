<!DOCTYPE html>
<html>
<head>
    <title>Productos - Fudo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">
    <h1 class="mb-4">Productos</h1>
    <div class="row">
        <?php foreach ($productos as $p): ?>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <?php if($p->imagen): ?>
                        <img src="<?= base_url('uploads/'.$p->imagen) ?>" class="card-img-top" alt="<?= $p->nombre ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= $p->nombre ?></h5>
                        <p class="card-text"><?= $p->descripcion ?></p>
                        <p><strong>$<?= number_format($p->precio,0,',','.') ?></strong></p>
                        <button class="btn btn-success" onclick="agregarCarrito(<?= $p->id_producto ?>,'<?= $p->nombre ?>',<?= $p->precio ?>)">Agregar</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        let carrito = [];

        function agregarCarrito(id, nombre, precio){
            let item = carrito.find(i=>i.id_producto===id);
            <!DOCTYPE html>
            <html>
            <head>
                <title>Productos - Fudo</title>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
                <style>.pointer{cursor:pointer}</style>
            </head>
            <body class="container mt-4">
                <h1 class="mb-4">Productos</h1>
+
                <div class="row">
                    <?php foreach ($productos as $p): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <?php if($p->imagen): ?>
                                    <img src="<?= base_url('uploads/'.$p->imagen) ?>" class="card-img-top" alt="<?= $p->nombre ?>">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?= $p->nombre ?></h5>
                                    <p class="card-text"><?= $p->descripcion ?></p>
                                    <p><strong>$<?= number_format($p->precio,0,',','.') ?></strong></p>
+                        
                                    <button class="btn btn-success" onclick="agregarCarrito(<?= $p->id_producto ?>,'<?= $p->nombre ?>',<?= $p->precio ?>)">Agregar</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
+
                <hr />
+
                <h3>Carrito</h3>
+    <div id="carritoContainer">
+        <p>No hay productos agregados.</p>
+    </div>
+
+    <div class="mt-3">
+        <button id="btnEnviar" class="btn btn-primary" onclick="enviarPedido()" disabled>Enviar pedido</button>
+    </div>
+
+    <script>
+        let carrito = [];
+
+        // id_mesa desde sesión (si está disponible)
+        const id_mesa = <?= json_encode($this->session->userdata('id_mesa') ?? null) ?>;
+
+        function agregarCarrito(id, nombre, precio){
+            let item = carrito.find(i=>i.id_producto===id);
+            if(item){
+                item.cantidad++;
+                item.subtotal = item.cantidad * item.precio;
+            }else{
+                carrito.push({id_producto:id, nombre:nombre, precio:precio, cantidad:1, subtotal:precio});
+            }
+            renderCarrito();
+        }
+
+
+        function renderCarrito(){
+            const c = document.getElementById('carritoContainer');
+            if(carrito.length===0){
+                c.innerHTML = '<p>No hay productos agregados.</p>';
+                document.getElementById('btnEnviar').disabled = true;
+                return;
+            }
+            let html = '<table class="table"><thead><tr><th>Producto</th><th>Cant</th><th>Subtotal</th><th></th></tr></thead><tbody>';
+            let total = 0;
+            carrito.forEach((it, idx)=>{
+                total += it.subtotal;
+                html += `<tr><td>${it.nombre}</td><td><button class="btn btn-sm btn-secondary" onclick="cambiarCantidad(${idx}, -1)">-</button> ${it.cantidad} <button class="btn btn-sm btn-secondary" onclick="cambiarCantidad(${idx}, 1)">+</button></td><td>$${new Intl.NumberFormat('es-CL').format(it.subtotal)}</td><td><button class="btn btn-sm btn-danger" onclick="quitar(${idx})">Quitar</button></td></tr>`;
+            });
+            html += `</tbody><tfoot><tr><td colspan="2"><strong>Total</strong></td><td colspan="2"><strong>$${new Intl.NumberFormat('es-CL').format(total)}</strong></td></tr></tfoot></table>`;
+            c.innerHTML = html;
+            document.getElementById('btnEnviar').disabled = false;
+        }
+
+
+        function cambiarCantidad(idx, delta){
+            carrito[idx].cantidad += delta;
+            if(carrito[idx].cantidad<=0) carrito.splice(idx,1);
+            else carrito[idx].subtotal = carrito[idx].cantidad * carrito[idx].precio;
+            renderCarrito();
+        }
+
+
+        function quitar(idx){
+            carrito.splice(idx,1);
+            renderCarrito();
+        }
+
+
+        async function enviarPedido(){
+            const payload = {
+                id_mesa: id_mesa,
+                detalle: carrito.map(i=>({id_producto:i.id_producto, cantidad:i.cantidad, subtotal:i.subtotal}))
+            };
+
+            try{
+                const res = await fetch('<?= site_url('pedidos/crear') ?>',{
+                    method:'POST',
+                    headers:{'Content-Type':'application/json'},
+                    body: JSON.stringify(payload)
+                });
+                const json = await res.json();
+                if(json.ok){
+                    alert('Pedido creado. ID: '+json.id_pedido);
+                    carrito = [];
+                    renderCarrito();
+                }else{
+                    alert('Error: '+(json.error||'error desconocido'));
+                }
+            }catch(e){
+                alert('Error al enviar pedido');
+                console.error(e);
+            }
+        }
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
