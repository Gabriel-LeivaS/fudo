<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Sucursales - Fudo</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin-ui.css') ?>">
    <style>
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
    </style>
</head>
<body>
    <?php 
    $active_page = 'sucursales';
    include(APPPATH . 'views/admin/components/navbar.php'); 
    ?>

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
                                <th>Contacto</th>
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
                                        <td>
                                            <div class="contact-info">
                                                <?php if (!empty($sucursal->telefono)): ?>
                                                    <div><small>📞 <?= htmlspecialchars($sucursal->telefono) ?></small></div>
                                                <?php endif; ?>
                                                <?php if (!empty($sucursal->email)): ?>
                                                    <div><small>📧 <?= htmlspecialchars($sucursal->email) ?></small></div>
                                                <?php endif; ?>
                                                <?php if (!empty($sucursal->whatsapp)): ?>
                                                    <div><small>📱 <?= htmlspecialchars($sucursal->whatsapp) ?></small></div>
                                                <?php endif; ?>
                                                <?php if (!empty($sucursal->instagram)): ?>
                                                    <div><small>📷 <?= htmlspecialchars($sucursal->instagram) ?></small></div>
                                                <?php endif; ?>
                                                <?php if (empty($sucursal->telefono) && empty($sucursal->email) && empty($sucursal->whatsapp) && empty($sucursal->instagram)): ?>
                                                    <small class="text-muted">Sin contacto</small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($sucursal->activo === 't' || $sucursal->activo === true): ?>
                                                <span class="badge bg-success">Activa</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactiva</span>
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
                                            <?php $activo = ($sucursal->activo === 't' || $sucursal->activo === true); ?>
                                            <button class="btn <?= $activo ? 'btn-secondary' : 'btn-success' ?> btn-action btn-sm" 
                                                    onclick="cambiarEstado(<?= $sucursal->id_sucursal ?>, <?= $activo ? 'false' : 'true' ?>)"
                                                    title="<?= $activo ? 'Desactivar' : 'Activar' ?>">
                                                <?= $activo ? '🔒' : '✅' ?>
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
                            <input type="text" class="form-control" id="nombre" name="nombre" autocomplete="organization" required>
                        </div>

                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" autocomplete="street-address">
                        </div>

                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" autocomplete="tel">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" autocomplete="email">
                        </div>

                        <div class="mb-3">
                            <label for="whatsapp" class="form-label">WhatsApp</label>
                            <input type="tel" class="form-control" id="whatsapp" name="whatsapp" autocomplete="tel" placeholder="Ej: +56912345678">
                            <div class="form-text">Número de WhatsApp con código de país (Ej: +56912345678)</div>
                        </div>

                        <div class="mb-3">
                            <label for="instagram" class="form-label">Instagram</label>
                            <input type="text" class="form-control" id="instagram" name="instagram" autocomplete="username" placeholder="Ej: @restaurante_centro">
                            <div class="form-text">Usuario de Instagram (con o sin @)</div>
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
        const baseUrl = '<?= rtrim(site_url(), '/') ?>';
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

        async function abrirModalEditar(id) {
            modoEdicion = true;
            document.getElementById('modalTitle').textContent = 'Editar Sucursal';
            
            try {
                // Obtener datos de la sucursal via AJAX
                const url = `${baseUrl}/sucursales/obtener/${id}`;
                const response = await fetch(url);
                const sucursal = await response.json();
                
                if (sucursal.success) {
                    const data = sucursal.data;
                    document.getElementById('id_sucursal').value = id;
                    document.getElementById('nombre').value = data.nombre || '';
                    document.getElementById('direccion').value = data.direccion || '';
                    document.getElementById('telefono').value = data.telefono || '';
                    document.getElementById('email').value = data.email || '';
                    document.getElementById('whatsapp').value = data.whatsapp || '';
                    document.getElementById('instagram').value = data.instagram || '';
                    modalSucursal.show();
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo cargar la información de la sucursal',
                        icon: 'error'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Error de conexión al cargar la sucursal',
                    icon: 'error'
                });
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
            const activar = nuevoEstado === 'true' || nuevoEstado === true;
            
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
                
                const url = `${baseUrl}/sucursales/cambiar_estado`;
                
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Actualizar visualmente el estado sin recargar INMEDIATAMENTE
                    actualizarEstadoVisual(id, nuevoEstado === 'true' || nuevoEstado === true);
                    
                    // Mostrar notificación de éxito sin bloquear
                    Swal.fire({
                        title: '¡Éxito!',
                        text: result.message,
                        icon: 'success',
                        timer: 1500,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
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

        function actualizarEstadoVisual(id, activo) {
            // Encontrar la fila de la sucursal
            const fila = document.querySelector(`button[onclick*="cambiarEstado(${id},"]`).closest('tr');
            
            if (!fila) {
                console.error('No se pudo encontrar la fila para la sucursal ID:', id);
                return;
            }
            
            // Actualizar el badge de estado
            const estadoCell = fila.cells[4]; // Columna de estado (ID, Nombre, Dirección, Contacto, Estado)
            if (!estadoCell) {
                console.error('No se pudo encontrar la celda de estado');
                return;
            }
            
            estadoCell.innerHTML = activo ? 
                '<span class="badge bg-success">Activa</span>' : 
                '<span class="badge bg-danger">Inactiva</span>';
            
            // Actualizar el botón de cambiar estado
            const botonEstado = fila.querySelector(`button[onclick*="cambiarEstado(${id},"]`);
            botonEstado.className = `btn ${activo ? 'btn-secondary' : 'btn-success'} btn-action btn-sm`;
            botonEstado.title = activo ? 'Desactivar' : 'Activar';
            botonEstado.innerHTML = activo ? '🔒' : '✅';
            botonEstado.setAttribute('onclick', `cambiarEstado(${id}, ${activo ? 'false' : 'true'})`);
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
