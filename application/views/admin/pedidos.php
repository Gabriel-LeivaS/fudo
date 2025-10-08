<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Pedidos - Fudo</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        :root {
            --accent: #b08c6a;
            --accent-2: #a3c06b;
            --muted: #6c6c6c;
            --card-radius: 14px;
            --shadow: 0 14px 36px rgba(11,11,11,0.06);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { 
            height: 100%; 
            font-family: 'Montserrat', system-ui, sans-serif; 
            color: #222; 
            background: #fbf8f6;
        }
        .admin-header {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }
        .admin-header h2 {
            font-weight: 800;
            font-size: 24px;
            margin: 0;
        }
        .btn-group .btn {
            font-size: 13px;
            font-weight: 600;
            padding: 8px 14px;
            border-radius: 8px;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-group .btn-light {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
        }
        .btn-group .btn-light:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }
        .btn-group .btn-light.active {
            background: white;
            color: var(--accent);
            font-weight: 700;
        }
        .btn-danger {
            background: #e74c3c !important;
            border: none;
        }
        .btn-danger:hover {
            background: #c0392b !important;
            transform: translateY(-2px);
        }
        .container { max-width: 1100px; }
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
        .btn-action {
            padding: 0.4rem 0.75rem;
            font-size: 13px;
            font-weight: 600;
            margin: 0 0.2rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .btn-primary {
            background: #3498db;
            border: none;
        }
        .btn-primary:hover {
            background: #2980b9;
        }
        .btn-warning {
            background: #f39c12;
            border: none;
            color: white;
        }
        .btn-warning:hover {
            background: #e67e22;
        }
        .btn-success {
            background: var(--accent-2);
            border: none;
        }
        .btn-success:hover {
            background: #94b35e;
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
    <!-- Header con navegación -->
    <div class="admin-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h2>📦 Gestión de Pedidos</h2>
                <div class="btn-group">
                    <a href="<?= site_url('admin') ?>" class="btn btn-light btn-sm active">📦 Pedidos</a>
                    <a href="<?= site_url('admin/categorias') ?>" class="btn btn-light btn-sm">🏷️ Categorías</a>
                    <a href="<?= site_url('admin/productos') ?>" class="btn btn-light btn-sm">🛍️ Productos</a>
                    <?php if($this->session->userdata('rol') == 'admin_sucursal'): ?>
                        <a href="<?= site_url('admin/mi_carta') ?>" class="btn btn-light btn-sm">📋 Mi Carta</a>
                        <a href="<?= site_url('mesas') ?>" class="btn btn-light btn-sm">🪑 Mesas</a>
                        <a href="<?= site_url('cocina') ?>" class="btn btn-light btn-sm">🔥 Cocina</a>
                    <?php endif; ?>
                    <?php if($this->session->userdata('rol') == 'admin'): ?>
                        <a href="<?= site_url('usuarios') ?>" class="btn btn-light btn-sm">👥 Usuarios</a>
                        <a href="<?= site_url('sucursales') ?>" class="btn btn-light btn-sm">🏢 Sucursales</a>
                    <?php endif; ?>
                    <a href="<?= site_url('login/salir') ?>" class="btn btn-danger btn-sm">🚪 Salir</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
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
                                        <a href="<?= site_url('admin/detalle/'.$p->id_pedido) ?>" 
                                           class="btn btn-sm btn-primary btn-action">
                                            👁️ Ver
                                        </a>
                                        <?php if($p->estado == 'Pendiente'): ?>
                                            <button class="btn btn-sm btn-warning btn-action" 
                                                    onclick="cambiarEstado(<?= $p->id_pedido ?>,'En preparación')">
                                                ⏱️ Preparar
                                            </button>
                                        <?php endif; ?>
                                        <?php if($p->estado != 'Lista'): ?>
                                            <button class="btn btn-sm btn-success btn-action" 
                                                    onclick="cambiarEstado(<?= $p->id_pedido ?>,'Lista')">
                                                ✅ Lista
                                            </button>
                                        <?php endif; ?>
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
                    location.reload();
                } else {
                    alert('Error al actualizar el estado');
                }
            } catch(error) {
                console.error('Error:', error);
                alert('Error de conexión');
            }
        }
    </script>
</body>
</html>
