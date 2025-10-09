<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🏢 Gestión de Sucursales - Panel FUDO</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent: #b08c6a;
            --accent-2: #a3c06b;
            --muted: #6c6c6c;
            --bg-light: #fbf8f6;
        }

        * {
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background: var(--bg-light);
            padding: 20px;
        }

        /* Navbar Superior */
        .navbar {
            background: white;
            padding: 15px 0;
            margin-bottom: 20px;
            border-radius: 14px;
            box-shadow: 0 14px 36px rgba(11,11,11,0.06);
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
            color: white;
        }

        .admin-header {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            color: white;
            padding: 30px;
            border-radius: 14px;
            margin-bottom: 30px;
            box-shadow: 0 14px 36px rgba(11,11,11,0.06);
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
            border-radius: 14px;
            box-shadow: 0 14px 36px rgba(11,11,11,0.06);
        }

        .card-body {
            padding: 25px;
        }

        .btn-action {
            padding: 0.5rem 0.9rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(176, 140, 106, 0.3);
        }

        .btn-success {
            background: #28a745;
            border: none;
        }

        .btn-danger {
            background: #dc3545;
            border: none;
        }

        .btn-warning {
            background: #ffc107;
            border: none;
            color: #333;
        }

        .btn-info {
            background: #17a2b8;
            border: none;
            color: white;
        }

        .table {
            margin-top: 20px;
        }

        .table th {
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            color: var(--muted);
            border-bottom: 2px solid #dee2e6;
            padding: 15px;
        }

        .table td {
            vertical-align: middle;
            padding: 15px;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
        }

        .badge-success {
            background: #28a745;
        }

        .badge-danger {
            background: #dc3545;
        }

        .modal-content {
            border-radius: 14px;
            border: none;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            color: white;
            border-radius: 14px 14px 0 0;
        }

        .modal-title {
            font-weight: 700;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 10px 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 0.2rem rgba(176, 140, 106, 0.25);
        }

        .alert {
            border-radius: 10px;
            border: none;
            font-weight: 600;
        }

        .sucursal-stats {
            display: inline-flex;
            gap: 15px;
            margin-top: 10px;
        }

        .stat-badge {
            background: var(--bg-light);
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--muted);
        }

        @media (max-width: 768px) {
            .table {
                font-size: 13px;
            }
            
            .btn-action {
                padding: 0.4rem 0.7rem;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar Superior -->
    <nav class="navbar">
        <div class="container-fluid">
            <span class="navbar-brand">� FUDO</span>
            <div class="d-flex align-items-center gap-3">
                <?php if($this->session->userdata('rol') == 'admin_sucursal'): ?>
                    <a href="<?= site_url('admin') ?>" class="nav-link">📦 Pedidos</a>
                <?php endif; ?>
                <a href="<?= site_url('admin/categorias') ?>" class="nav-link">🏷️ Categorías</a>
                <a href="<?= site_url('admin/productos') ?>" class="nav-link">🛍️ Productos</a>
                <?php if($this->session->userdata('rol') == 'admin_sucursal'): ?>
                    <a href="<?= site_url('admin/mi_carta') ?>" class="nav-link">📋 Mi Carta</a>
                    <a href="<?= site_url('mesas') ?>" class="nav-link">🪑 Mesas</a>
                    <a href="<?= site_url('cocina') ?>" class="nav-link">🔥 Cocina</a>
                <?php endif; ?>
                <?php if($this->session->userdata('rol') == 'admin'): ?>
                    <a href="<?= site_url('usuarios') ?>" class="nav-link">👥 Usuarios</a>
                    <a href="<?= site_url('sucursales') ?>" class="nav-link active">🏢 Sucursales</a>
                <?php endif; ?>
                <?php if($this->session->userdata('rol') == 'usuario'): ?>
                    <span class="badge bg-info">👁️ Solo Lectura</span>
                <?php endif; ?>
                <a href="<?= site_url('login/salir') ?>" class="btn btn-danger btn-action">🚪 Salir</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <!-- Admin Header -->
        <div class="admin-header">
            <h1>🏢 Gestión de Sucursales</h1>
            <p>Administra las sucursales del sistema y sus configuraciones</p>
        </div>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                ✅ <?= $this->session->flashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                ❌ <?= $this->session->flashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">📋 Lista de Sucursales</h5>
                    <button class="btn btn-primary btn-action" onclick="abrirModalCrear()">
                        ➕ Nueva Sucursal
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Dirección</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaSucursales">
                            <?php if (empty($sucursales)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        No hay sucursales registradas
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($sucursales as $sucursal): ?>
                                    <tr data-id="<?= $sucursal->id_sucursal ?>">
                                        <td><strong>#<?= $sucursal->id_sucursal ?></strong></td>
                                        <td>
                                            <strong><?= htmlspecialchars($sucursal->nombre) ?></strong>
                                            <?php if (isset($sucursal->total_usuarios) && $sucursal->total_usuarios > 0): ?>
                                                <div class="sucursal-stats">
                                                    <span class="stat-badge">
                                                        👥 <?= $sucursal->total_usuarios ?> usuarios
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($sucursal->direccion ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($sucursal->telefono ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($sucursal->email ?? 'N/A') ?></td>
                                        <td>
                                            <?php if ($sucursal->activo): ?>
                                                <span class="badge badge-success">Activa</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Inactiva</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-info btn-action btn-sm" 
                                                    onclick="verEstadisticas(<?= $sucursal->id_sucursal ?>)"
                                                    title="Ver estadísticas">
                                                📊
                                            </button>
                                            <button class="btn btn-warning btn-action btn-sm" 
                                                    onclick="abrirModalEditar(<?= $sucursal->id_sucursal ?>)"
                                                    title="Editar">
                                                ✏️
                                            </button>
                                            <button class="btn <?= $sucursal->activo ? 'btn-secondary' : 'btn-success' ?> btn-action btn-sm" 
                                                    onclick="cambiarEstado(<?= $sucursal->id_sucursal ?>, <?= $sucursal->activo ? 'false' : 'true' ?>)"
                                                    title="<?= $sucursal->activo ? 'Desactivar' : 'Activar' ?>">
                                                <?= $sucursal->activo ? '🔒' : '✅' ?>
                                            </button>
                                            <button class="btn btn-danger btn-action btn-sm" 
                                                    onclick="eliminarSucursal(<?= $sucursal->id_sucursal ?>)"
                                                    title="Eliminar">
                                                🗑️
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

    <!-- Modal Crear/Editar Sucursal -->
    <div class="modal fade" id="modalSucursal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nueva Sucursal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formSucursal">
                        <input type="hidden" id="id_sucursal" name="id_sucursal">
                        
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de la Sucursal *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>

                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion">
                        </div>

                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarSucursal()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Estadísticas -->
    <div class="modal fade" id="modalEstadisticas" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">📊 Estadísticas de Sucursal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="contenidoEstadisticas">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const baseUrl = '<?= site_url() ?>';
        let modalSucursal;
        let modalEstadisticas;
        let modoEdicion = false;

        document.addEventListener('DOMContentLoaded', function() {
            modalSucursal = new bootstrap.Modal(document.getElementById('modalSucursal'));
            modalEstadisticas = new bootstrap.Modal(document.getElementById('modalEstadisticas'));
        });

        function abrirModalCrear() {
            modoEdicion = false;
            document.getElementById('modalTitle').textContent = 'Nueva Sucursal';
            document.getElementById('formSucursal').reset();
            document.getElementById('id_sucursal').value = '';
            modalSucursal.show();
        }

        function abrirModalEditar(id) {
            modoEdicion = true;
            document.getElementById('modalTitle').textContent = 'Editar Sucursal';
            
            // Buscar la sucursal en la tabla
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (row) {
                const cells = row.cells;
                document.getElementById('id_sucursal').value = id;
                document.getElementById('nombre').value = cells[1].querySelector('strong').textContent;
                document.getElementById('direccion').value = cells[2].textContent !== 'N/A' ? cells[2].textContent : '';
                document.getElementById('telefono').value = cells[3].textContent !== 'N/A' ? cells[3].textContent : '';
                document.getElementById('email').value = cells[4].textContent !== 'N/A' ? cells[4].textContent : '';
                modalSucursal.show();
            }
        }

        async function guardarSucursal() {
            const form = document.getElementById('formSucursal');
            const formData = new FormData(form);
            
            const url = modoEdicion ? `${baseUrl}/sucursales/editar` : `${baseUrl}/sucursales/crear`;
            
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    await Swal.fire({
                        title: '¡Éxito!',
                        text: result.message,
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    modalSucursal.hide();
                    location.reload();
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: result.message,
                        icon: 'error'
                    });
                }
            } catch (error) {
                Swal.fire({
                    title: 'Error',
                    text: 'Error al guardar la sucursal',
                    icon: 'error'
                });
                console.error('Error:', error);
            }
        }

        async function eliminarSucursal(id) {
            const result = await Swal.fire({
                title: '⚠️ ¿Eliminar Sucursal?',
                html: '¿Estás seguro de eliminar esta sucursal?<br><small class="text-muted">Esta acción no se puede deshacer</small>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });

            if (!result.isConfirmed) return;
            
            try {
                const formData = new FormData();
                formData.append('id_sucursal', id);
                
                const response = await fetch(`${baseUrl}/sucursales/eliminar`, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    await Swal.fire({
                        title: '¡Eliminada!',
                        text: result.message,
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    location.reload();
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: result.message,
                        icon: 'error'
                    });
                }
            } catch (error) {
                Swal.fire({
                    title: 'Error',
                    text: 'Error al eliminar la sucursal',
                    icon: 'error'
                });
                console.error('Error:', error);
            }
        }

        async function cambiarEstado(id, nuevoEstado) {
            const activar = nuevoEstado === 'true';
            
            const result = await Swal.fire({
                title: activar ? '¿Activar Sucursal?' : '¿Desactivar Sucursal?',
                text: activar ? 'La sucursal estará disponible' : 'La sucursal no estará disponible',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: activar ? '#28a745' : '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: activar ? 'Sí, activar' : 'Sí, desactivar',
                cancelButtonText: 'Cancelar'
            });

            if (!result.isConfirmed) return;
            
            try {
                const formData = new FormData();
                formData.append('id_sucursal', id);
                formData.append('estado', nuevoEstado);
                
                const response = await fetch(`${baseUrl}/sucursales/cambiar_estado`, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    await Swal.fire({
                        title: '¡Éxito!',
                        text: result.message,
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    location.reload();
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: result.message,
                        icon: 'error'
                    });
                }
            } catch (error) {
                Swal.fire({
                    title: 'Error',
                    text: 'Error al cambiar el estado',
                    icon: 'error'
                });
                console.error('Error:', error);
            }
        }

        async function verEstadisticas(id) {
            modalEstadisticas.show();
            document.getElementById('contenidoEstadisticas').innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            `;
            
            try {
                const response = await fetch(`${baseUrl}/sucursales/estadisticas/${id}`);
                const stats = await response.json();
                
                document.getElementById('contenidoEstadisticas').innerHTML = `
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white mb-3">
                                <div class="card-body">
                                    <h3>${stats.total_usuarios || 0}</h3>
                                    <p class="mb-0">👥 Usuarios</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white mb-3">
                                <div class="card-body">
                                    <h3>${stats.total_categorias || 0}</h3>
                                    <p class="mb-0">🏷️ Categorías</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white mb-3">
                                <div class="card-body">
                                    <h3>${stats.total_productos || 0}</h3>
                                    <p class="mb-0">🛍️ Productos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white mb-3">
                                <div class="card-body">
                                    <h3>${stats.total_mesas || 0}</h3>
                                    <p class="mb-0">🪑 Mesas</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('contenidoEstadisticas').innerHTML = `
                    <div class="alert alert-danger">
                        Error al cargar las estadísticas
                    </div>
                `;
            }
        }
    </script>
</body>
</html>
