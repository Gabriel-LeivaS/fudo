<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Mesas - Fudo</title>
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
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        .qr-cell img {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 5px;
        }
    </style>
</head>
<body>
    <!-- Header con navegación -->
    <div class="admin-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0"><i class="bi bi-grid-3x3"></i> Gestión de Mesas</h2>
                <div class="btn-group">
                    <a href="<?= site_url('admin') ?>" class="btn btn-light btn-sm"><i class="bi bi-box-seam"></i> Pedidos</a>
                    <a href="<?= site_url('admin/categorias') ?>" class="btn btn-light btn-sm"><i class="bi bi-tag-fill"></i> Categorías</a>
                    <a href="<?= site_url('admin/productos') ?>" class="btn btn-light btn-sm"><i class="bi bi-bag-fill"></i> Productos</a>
                    <a href="<?= site_url('mesas') ?>" class="btn btn-light btn-sm active"><i class="bi bi-grid-3x3"></i> Mesas</a>
                    <a href="<?= site_url('cocina') ?>" class="btn btn-light btn-sm"><i class="bi bi-fire"></i> Cocina</a>
                    <a href="<?= site_url('login/salir') ?>" class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Salir</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> Listado de Mesas y Códigos QR</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="200">Mesa</th>
                                <th>Código QR</th>
                                <th width="150" class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($mesas as $m): ?>
                            <tr>
                                <td><strong><?= $m->nombre ?></strong></td>
                                <td class="qr-cell">
                                    <?php if($m->codigo_qr): ?>
                                        <img src="<?= base_url($m->codigo_qr) ?>" width="100" class="mesa-qr-<?= $m->id_mesa ?>">
                                    <?php else: ?>
                                        <span class="text-muted">No generado</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-primary btn-sm btn-action btn-gen-qr" data-id="<?= $m->id_mesa ?>">
                                        <i class="bi bi-qr-code"></i> Generar QR
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('click', function(e){
            if (e.target && e.target.closest('.btn-gen-qr')) {
                var btn = e.target.closest('.btn-gen-qr');
                var id = btn.getAttribute('data-id');
                var originalHTML = btn.innerHTML;
                
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Generando...';
                
                fetch('<?= site_url('mesas/generar_qr/') ?>' + id, { 
                    method: 'GET', 
                    headers: { 'X-Requested-With': 'XMLHttpRequest' } 
                })
                .then(function(res){ 
                    return res.json().catch(function(){ 
                        return { success:false, message: 'Respuesta inválida del servidor' }; 
                    }); 
                })
                .then(function(data){
                    if (data.success) {
                        // actualizar imagen o insertarla
                        var img = document.querySelector('.mesa-qr-' + id);
                        var path = data.path + '?t=' + Date.now();
                        if (img) {
                            img.src = path;
                        } else {
                            var cell = btn.closest('tr').querySelector('.qr-cell');
                            cell.innerHTML = '<img src="' + path + '" width="100" class="mesa-qr-' + id + '">';
                        }
                        alert('QR generado exitosamente');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(function(err){
                    alert('Error en la petición: ' + err);
                })
                .finally(function(){
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                });
            }
        });
    </script>
</body>
</html>
