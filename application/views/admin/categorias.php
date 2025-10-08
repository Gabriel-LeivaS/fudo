<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Categorías - Fudo</title>
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
        .btn-primary {
            background: var(--accent-2);
            border: none;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 10px;
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            background: #94b35e;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(163,192,107,0.3);
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
        .action-buttons {
            display: flex;
            gap: 0.4rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-action {
            padding: 0.5rem 0.9rem;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease;
            border: none;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.2);
        }
        .btn-action:active {
            transform: translateY(0);
        }
        .btn-warning {
            background: #f39c12;
            color: white;
        }
        .btn-warning:hover {
            background: #e67e22;
            color: white;
        }
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        .btn-secondary:hover {
            background: #7f8c8d;
            color: white;
        }
        .btn-success {
            background: var(--accent-2);
            color: white;
        }
        .btn-success:hover {
            background: #94b35e;
            color: white;
        }
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        .btn-danger:hover {
            background: #c0392b;
            color: white;
        }
        /* Botón toggle específico */
        .btn-toggle {
            min-width: 40px;
            padding: 0.5rem 0.7rem;
            font-size: 18px;
        }
        .modal-content {
            border: none;
            border-radius: 14px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .modal-header {
            border-bottom: 1px solid #f0f0f0;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            color: white;
            border-radius: 14px 14px 0 0;
        }
        .modal-title {
            font-weight: 700;
            font-size: 18px;
        }
        .modal-body {
            padding: 1.5rem;
        }
        .form-label {
            font-weight: 600;
            font-size: 13px;
            color: #333;
            margin-bottom: 0.5rem;
        }
        .form-control, .form-select {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 14px;
            font-family: 'Montserrat', inherit;
            transition: all 0.2s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--accent-2);
            box-shadow: 0 0 0 3px rgba(163,192,107,0.15);
        }
        .form-text {
            font-size: 12px;
            color: #6c757d;
            margin-top: 0.25rem;
        }
        .btn-close {
            filter: brightness(0) invert(1);
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
                <h2>🏷️ Gestión de Categorías</h2>
                <div class="btn-group">
                    <a href="<?= site_url('admin') ?>" class="btn btn-light btn-sm">📦 Pedidos</a>
                    <a href="<?= site_url('admin/categorias') ?>" class="btn btn-light btn-sm active">🏷️ Categorías</a>
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
        <?php if($this->session->userdata('rol') == 'admin'): ?>
        <!-- Filtro de Sucursal para Super Admin -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">🏢 Filtrar por Sucursal:</label>
                    </div>
                    <div class="col-md-6">
                        <select id="filtroSucursal" class="form-select" onchange="filtrarPorSucursal()">
                            <option value="">Todas las sucursales</option>
                            <?php foreach($sucursales as $suc): ?>
                                <option value="<?= $suc->id_sucursal ?>"><?= $suc->nombre ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 text-end">
                        <span class="badge bg-info" id="contadorCategorias"><?= count($categorias) ?> categorías</span>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- Badge indicador para Admin Sucursal -->
        <div class="alert alert-info d-flex justify-content-between align-items-center mb-3">
            <span>🏢 <strong>Sucursal:</strong> <?= $this->session->userdata('nombre_sucursal') ?></span>
            <span class="badge bg-primary"><?= count($categorias) ?> categorías</span>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5>📋 Listado de Categorías</h5>
                <button class="btn btn-primary" onclick="abrirModalCrear()">
                    ➕ Nueva Categoría
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tablaCategorias">
                        <thead class="table-light">
                            <tr>
                                <th width="80">ID</th>
                                <th>Nombre</th>
                                <?php if($this->session->userdata('rol') == 'admin'): ?>
                                <th width="200">Sucursal</th>
                                <?php endif; ?>
                                <th width="120" class="text-center">Estado</th>
                                <th width="200" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($categorias)): ?>
                                <tr>
                                    <td colspan="<?= $this->session->userdata('rol') == 'admin' ? '5' : '4' ?>" class="text-center text-muted py-4">
                                        No hay categorías registradas
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($categorias as $cat): ?>
                                    <tr data-sucursal="<?= $cat->id_sucursal ?? '' ?>">
                                        <td><strong>#<?= $cat->id_categoria ?></strong></td>
                                        <td><?= htmlspecialchars($cat->nombre) ?></td>
                                        <?php if($this->session->userdata('rol') == 'admin'): ?>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= htmlspecialchars($cat->nombre_sucursal ?? 'Sin sucursal') ?>
                                            </span>
                                        </td>
                                        <?php endif; ?>
                                        <td class="text-center">
                                            <?php if($cat->estado): ?>
                                                <span class="badge bg-success badge-custom">Activa</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary badge-custom">Inactiva</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="action-buttons">
                                                <button class="btn btn-warning btn-action" 
                                                        onclick="abrirModalEditar(<?= $cat->id_categoria ?>, '<?= htmlspecialchars($cat->nombre, ENT_QUOTES) ?>')">
                                                    ✏️
                                                </button>
                                                <button class="btn <?= $cat->estado ? 'btn-secondary' : 'btn-success' ?> btn-action btn-toggle" 
                                                        onclick="toggleEstado(<?= $cat->id_categoria ?>, <?= $cat->estado ? 'false' : 'true' ?>)"
                                                        title="<?= $cat->estado ? 'Desactivar' : 'Activar' ?>">
                                                    <?= $cat->estado ? '👁️‍🗨️' : '👁️' ?>
                                                </button>
                                                <button class="btn btn-danger btn-action" 
                                                        onclick="eliminarCategoria(<?= $cat->id_categoria ?>, '<?= htmlspecialchars($cat->nombre, ENT_QUOTES) ?>')">
                                                    🗑️
                                                </button>
                                            </div>
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

    <!-- Modal Crear/Editar Categoría -->
    <div class="modal fade" id="modalCategoria" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitulo">Nueva Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCategoria">
                        <input type="hidden" id="id_categoria" name="id_categoria">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de la Categoría</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                            <div class="form-text">Ejemplo: Bebidas, Comidas, Postres, etc.</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarCategoria()">
                        💾 Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let modal;
        let esEdicion = false;

        document.addEventListener('DOMContentLoaded', function() {
            modal = new bootstrap.Modal(document.getElementById('modalCategoria'));
        });

        function abrirModalCrear() {
            esEdicion = false;
            document.getElementById('modalTitulo').textContent = 'Nueva Categoría';
            document.getElementById('formCategoria').reset();
            document.getElementById('id_categoria').value = '';
            modal.show();
        }

        function abrirModalEditar(id, nombre) {
            esEdicion = true;
            document.getElementById('modalTitulo').textContent = 'Editar Categoría';
            document.getElementById('id_categoria').value = id;
            document.getElementById('nombre').value = nombre;
            modal.show();
        }

        async function guardarCategoria() {
            const form = document.getElementById('formCategoria');
            const formData = new FormData(form);
            
            const nombre = formData.get('nombre').trim();
            if(!nombre) {
                alert('El nombre es obligatorio');
                return;
            }

            const url = esEdicion 
                ? '<?= site_url('admin/categoria_editar') ?>'
                : '<?= site_url('admin/categoria_crear') ?>';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if(result.success) {
                    modal.hide();
                    alert(result.message);
                    location.reload();
                } else {
                    alert(result.message || 'Error al guardar');
                }
            } catch(error) {
                console.error('Error:', error);
                alert('Error de conexión');
            }
        }

        async function toggleEstado(id, nuevoEstado) {
            const formData = new FormData();
            formData.append('id_categoria', id);
            formData.append('estado', nuevoEstado);

            try {
                const response = await fetch('<?= site_url('admin/categoria_toggle_estado') ?>', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if(result.success) {
                    location.reload();
                } else {
                    alert(result.message || 'Error al cambiar el estado');
                }
            } catch(error) {
                console.error('Error:', error);
                alert('Error de conexión');
            }
        }

        async function eliminarCategoria(id, nombre) {
            if(!confirm(`¿Estás seguro de eliminar la categoría "${nombre}"?\n\nEsta acción no se puede deshacer.`)) {
                return;
            }

            const formData = new FormData();
            formData.append('id_categoria', id);

            try {
                const response = await fetch('<?= site_url('admin/categoria_eliminar') ?>', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if(result.success) {
                    alert(result.message);
                    location.reload();
                } else {
                    alert(result.message || 'Error al eliminar');
                }
            } catch(error) {
                console.error('Error:', error);
                alert('Error de conexión');
            }
        }

        // Filtrar categorías por sucursal (solo para super admin)
        function filtrarPorSucursal() {
            const sucursalId = document.getElementById('filtroSucursal').value;
            const tabla = document.getElementById('tablaCategorias');
            const filas = tabla.querySelectorAll('tbody tr');
            let contador = 0;

            filas.forEach(fila => {
                const sucursalFila = fila.getAttribute('data-sucursal');
                
                if(sucursalId === '' || sucursalFila === sucursalId) {
                    fila.style.display = '';
                    contador++;
                } else {
                    fila.style.display = 'none';
                }
            });

            // Actualizar contador
            document.getElementById('contadorCategorias').textContent = contador + ' categorías';
        }
    </script>
</body>
</html>
