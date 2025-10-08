<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carta - <?= $nombre_sucursal ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding-bottom: 40px;
        }

        .admin-header {
            background: white;
            padding: 20px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .admin-header h2 {
            margin: 0;
            font-weight: 800;
            color: #2d3748;
        }

        .btn-group .btn {
            font-weight: 600;
            text-transform: none;
            transition: all 0.3s ease;
            border: none;
            font-size: 14px;
        }

        .btn-group .btn-light {
            background: #f7fafc;
            color: #4a5568;
        }

        .btn-group .btn-light:hover {
            background: #edf2f7;
            transform: translateY(-2px);
        }

        .btn-group .btn-light.active {
            background: #667eea;
            color: white;
        }

        .carta-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .sucursal-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .sucursal-badge h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
        }

        .sucursal-badge p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .categoria-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .categoria-header {
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .categoria-header h3 {
            margin: 0;
            font-weight: 800;
            color: #2d3748;
            font-size: 24px;
        }

        .categoria-header .badge {
            font-size: 14px;
            padding: 8px 15px;
            background: #edf2f7;
            color: #4a5568;
            font-weight: 600;
        }

        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .producto-card {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
            background: #f7fafc;
        }

        .producto-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-color: #667eea;
        }

        .producto-nombre {
            font-weight: 800;
            font-size: 18px;
            color: #2d3748;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .producto-descripcion {
            color: #718096;
            font-size: 14px;
            margin-bottom: 15px;
            line-height: 1.6;
            min-height: 42px;
        }

        .producto-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }

        .producto-precio {
            font-weight: 800;
            color: #2d3748;
            font-size: 22px;
        }

        .producto-estado {
            font-size: 13px;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 700;
        }

        .estado-disponible {
            background: #c6f6d5;
            color: #22543d;
        }

        .estado-no-disponible {
            background: #fed7d7;
            color: #742a2a;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #a0aec0;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h4 {
            font-weight: 700;
            margin-bottom: 10px;
        }

        .btn-volver {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: white;
            color: #667eea;
            border: 3px solid #667eea;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .btn-volver:hover {
            background: #667eea;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }

        @media (max-width: 768px) {
            .productos-grid {
                grid-template-columns: 1fr;
            }

            .btn-group {
                flex-direction: column;
                width: 100%;
            }

            .btn-group .btn {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Header con navegación -->
    <div class="admin-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h2>📋 Mi Carta</h2>
                <div class="btn-group">
                    <a href="<?= site_url('admin') ?>" class="btn btn-light btn-sm">📦 Pedidos</a>
                    <a href="<?= site_url('admin/categorias') ?>" class="btn btn-light btn-sm">🏷️ Categorías</a>
                    <a href="<?= site_url('admin/productos') ?>" class="btn btn-light btn-sm">🛍️ Productos</a>
                    <a href="<?= site_url('admin/mi_carta') ?>" class="btn btn-light btn-sm active">📋 Mi Carta</a>
                    <a href="<?= site_url('mesas') ?>" class="btn btn-light btn-sm">🪑 Mesas</a>
                    <a href="<?= site_url('cocina') ?>" class="btn btn-light btn-sm">🔥 Cocina</a>
                    <a href="<?= site_url('login/salir') ?>" class="btn btn-danger btn-sm">🚪 Salir</a>
                </div>
            </div>
        </div>
    </div>

    <div class="carta-container">
        <!-- Badge de sucursal -->
        <div class="sucursal-badge">
            <h1>🏢 <?= htmlspecialchars($nombre_sucursal) ?></h1>
            <p>Vista previa de la carta de tu sucursal</p>
        </div>

        <?php if(empty($categorias)): ?>
            <div class="empty-state">
                <div style="font-size: 64px;">🍽️</div>
                <h4>No hay categorías en tu carta</h4>
                <p>Comienza agregando categorías y productos desde el panel de administración</p>
                <a href="<?= site_url('admin/categorias') ?>" class="btn btn-primary mt-3">➕ Agregar Categorías</a>
            </div>
        <?php else: ?>
            <?php foreach($categorias as $cat): ?>
                <div class="categoria-section">
                    <div class="categoria-header d-flex justify-content-between align-items-center">
                        <h3><?= htmlspecialchars($cat->nombre) ?></h3>
                        <span class="badge">
                            <?php 
                            $cant = isset($productos_por_categoria[$cat->id_categoria]) 
                                    ? count($productos_por_categoria[$cat->id_categoria]) 
                                    : 0;
                            echo $cant . ' producto' . ($cant != 1 ? 's' : '');
                            ?>
                        </span>
                    </div>

                    <?php if(empty($productos_por_categoria[$cat->id_categoria])): ?>
                        <div class="empty-state">
                            <div style="font-size: 48px;">📦</div>
                            <h5>No hay productos en esta categoría</h5>
                            <a href="<?= site_url('admin/productos') ?>" class="btn btn-sm btn-outline-primary mt-2">
                                ➕ Agregar Productos
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="productos-grid">
                            <?php foreach($productos_por_categoria[$cat->id_categoria] as $prod): ?>
                                <div class="producto-card">
                                    <div class="producto-nombre">
                                        <?= htmlspecialchars($prod->nombre) ?>
                                    </div>
                                    <div class="producto-descripcion">
                                        <?= htmlspecialchars($prod->descripcion ?? 'Sin descripción') ?>
                                    </div>
                                    <div class="producto-footer">
                                        <div class="producto-precio">
                                            $<?= number_format($prod->precio, 0, ',', '.') ?>
                                        </div>
                                        <div class="producto-estado <?= $prod->disponible ? 'estado-disponible' : 'estado-no-disponible' ?>">
                                            <?= $prod->disponible ? '✓ Disponible' : '✗ No disponible' ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <a href="<?= site_url('admin') ?>" class="btn-volver">
        ⬅️ Volver al Panel
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
