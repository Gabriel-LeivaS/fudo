<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Categorías - Fudo</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin-ui.css') ?>">
</head>
<body>
    <?php 
    $active_page = 'categorias';
    include(APPPATH . 'views/admin/components/navbar.php'); 
    ?>

    <div class="container-fluid">
        <!-- Admin Header -->
        <div class="admin-header">
            <h1>🏷️ Gestión de Categorías</h1>
            <p>Organiza y administra las categorías de productos de tu carta</p>
        </div>
        <?php if($this->session->userdata('rol') == 'admin'): ?>
        <!-- Filtro de Sucursal para Super Admin -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">🏢 Seleccione una Sucursal:</label>
                    </div>
                    <div class="col-md-6">
                        <select id="filtroSucursal" class="form-select" onchange="filtrarPorSucursal()">
                            <option value="">-- Seleccione una sucursal --</option>
                            <?php foreach($sucursales as $suc): ?>
                                <option value="<?= $suc->id_sucursal ?>"><?= $suc->nombre ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 text-end">
                        <span class="badge bg-info" id="contadorCategorias" style="display: none;">0 categorías</span>
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
                <?php if($tiene_permiso('categorias')): ?>
                <button class="btn btn-primary" onclick="abrirModalCrear()" id="btnNuevaCategoria">
                    ➕ Nueva Categoría
                </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if($this->session->userdata('rol') == 'admin'): ?>
                <!-- Mensaje inicial para super admin -->
                <div id="mensajeSeleccionSucursal" class="text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-building" style="font-size: 3rem; color: #b08c6a;"></i>
                    </div>
                    <h5 class="text-muted">Seleccione una sucursal para ver sus categorías</h5>
                    <p class="text-muted">Use el selector de sucursal arriba para comenzar</p>
                </div>
                <?php endif; ?>

                <div class="table-responsive" id="tablaContainer" <?= $this->session->userdata('rol') == 'admin' ? 'style="display: none;"' : '' ?>>
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
                                    <tr data-sucursal="<?= $cat->id_sucursal ?? '' ?>" data-estado="<?= $cat->estado ? 'true' : 'false' ?>" data-id="<?= $cat->id_categoria ?>">
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
                                            <?php if($tiene_permiso('categorias')): ?>
                                            <div class="action-buttons">
                                                <!-- Desktop: Botones normales -->
                                                <button class="btn btn-warning btn-action d-none d-lg-inline-block" 
                                                        onclick="abrirModalEditar(<?= $cat->id_categoria ?>, '<?= htmlspecialchars($cat->nombre, ENT_QUOTES) ?>')">
                                                    ✏️ Editar
                                                </button>
                                                <button class="btn <?= $cat->estado ? 'btn-secondary' : 'btn-success' ?> btn-action btn-toggle d-none d-lg-inline-block" 
                                                        onclick="toggleEstado(<?= $cat->id_categoria ?>, <?= $cat->estado ? 'false' : 'true' ?>)"
                                                        title="<?= $cat->estado ? 'Desactivar' : 'Activar' ?>">
                                                    <?= $cat->estado ? '👁️‍🗨️ Ocultar' : '👁️ Mostrar' ?>
                                                </button>
                                                <button class="btn btn-danger btn-action d-none d-lg-inline-block" 
                                                        onclick="eliminarCategoria(<?= $cat->id_categoria ?>, '<?= htmlspecialchars($cat->nombre, ENT_QUOTES) ?>')">
                                                    🗑️ Eliminar
                                                </button>
                                                
                                                <!-- Móviles/Tablets: Dropdown -->
                                                <button class="mobile-action-trigger d-lg-none" onclick="toggleMobileDropdown(this)">
                                                    ⚙️ Acciones <span style="font-size: 10px;">▼</span>
                                                </button>
                                                <div class="mobile-actions-dropdown">
                                                    <button class="btn btn-warning" onclick="abrirModalEditar(<?= $cat->id_categoria ?>, '<?= htmlspecialchars($cat->nombre, ENT_QUOTES) ?>')">
                                                        ✏️ Editar
                                                    </button>
                                                    <button class="btn <?= $cat->estado ? 'btn-secondary' : 'btn-success' ?>" 
                                                            onclick="toggleEstado(<?= $cat->id_categoria ?>, <?= $cat->estado ? 'false' : 'true' ?>)">
                                                        <?= $cat->estado ? '👁️‍🗨️ Ocultar' : '👁️ Mostrar' ?>
                                                    </button>
                                                    <button class="btn btn-danger" onclick="eliminarCategoria(<?= $cat->id_categoria ?>, '<?= htmlspecialchars($cat->nombre, ENT_QUOTES) ?>')">
                                                        🗑️ Eliminar
                                                    </button>
                                                </div>
                                            </div>
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
                        
                        <?php if($this->session->userdata('rol') == 'admin'): ?>
                        <!-- Campo de Sucursal solo para Super Admin -->
                        <div class="mb-3" id="campoSucursal">
                            <label for="id_sucursal" class="form-label">🏢 Sucursal *</label>
                            <select class="form-select" id="id_sucursal" name="id_sucursal" required>
                                <option value="">-- Seleccione una sucursal --</option>
                                <?php foreach($sucursales as $suc): ?>
                                    <option value="<?= $suc->id_sucursal ?>"><?= $suc->nombre ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Seleccione la sucursal a la que pertenecerá esta categoría</div>
                        </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de la Categoría *</label>
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
            
            // DEBUG: Mostrar estados de todas las categorías
            console.log('=== DEBUG: Estados de Categorías ===');
            const filas = document.querySelectorAll('#tablaCategorias tbody tr[data-id]');
            filas.forEach(fila => {
                const id = fila.getAttribute('data-id');
                const estado = fila.getAttribute('data-estado');
                const badge = fila.querySelector('.badge')?.textContent;
                console.log(`ID: ${id}, Estado (data): ${estado}, Badge: ${badge}`);
            });
            console.log('===================================');
            
            <?php if($this->session->userdata('rol') == 'admin'): ?>
            // Restaurar sucursal seleccionada después de reload
            const sucursalGuardada = sessionStorage.getItem('sucursalSeleccionada');
            if(sucursalGuardada) {
                const filtroSucursal = document.getElementById('filtroSucursal');
                if(filtroSucursal) {
                    filtroSucursal.value = sucursalGuardada;
                    // Aplicar el filtro automáticamente
                    filtrarPorSucursal();
                    // Limpiar sessionStorage
                    sessionStorage.removeItem('sucursalSeleccionada');
                }
            }
            <?php endif; ?>
        });

        function abrirModalCrear() {
            esEdicion = false;
            document.getElementById('modalTitulo').textContent = 'Nueva Categoría';
            document.getElementById('formCategoria').reset();
            document.getElementById('id_categoria').value = '';
            
            <?php if($this->session->userdata('rol') == 'admin'): ?>
            // Si hay una sucursal seleccionada en el filtro, pre-seleccionarla en el modal
            const sucursalSeleccionada = document.getElementById('filtroSucursal')?.value;
            if(sucursalSeleccionada) {
                document.getElementById('id_sucursal').value = sucursalSeleccionada;
            }
            <?php endif; ?>
            
            modal.show();
        }

        function abrirModalEditar(id, nombre) {
            esEdicion = true;
            document.getElementById('modalTitulo').textContent = 'Editar Categoría';
            document.getElementById('id_categoria').value = id;
            document.getElementById('nombre').value = nombre;
            
            <?php if($this->session->userdata('rol') == 'admin'): ?>
            // Buscar la fila para obtener la sucursal
            const filas = document.querySelectorAll('#tablaCategorias tbody tr');
            filas.forEach(fila => {
                if(fila.querySelector('td:first-child strong')?.textContent === '#' + id) {
                    const sucursalId = fila.getAttribute('data-sucursal');
                    if(sucursalId) {
                        document.getElementById('id_sucursal').value = sucursalId;
                    }
                }
            });
            <?php endif; ?>
            
            modal.show();
        }

        async function guardarCategoria() {
            const form = document.getElementById('formCategoria');
            const formData = new FormData(form);
            
            const nombre = formData.get('nombre').trim();
            if(!nombre) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campo requerido',
                    text: 'El nombre de la categoría es obligatorio',
                    confirmButtonColor: '#b08c6a'
                });
                return;
            }

            <?php if($this->session->userdata('rol') == 'admin'): ?>
            // Validar que se haya seleccionado una sucursal (solo para super admin)
            const idSucursal = formData.get('id_sucursal');
            if(!idSucursal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sucursal requerida',
                    text: 'Debe seleccionar una sucursal para la categoría',
                    confirmButtonColor: '#b08c6a'
                });
                return;
            }
            <?php endif; ?>

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
                    await Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: result.message,
                        confirmButtonColor: '#b08c6a',
                        timer: 1500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    
                    <?php if($this->session->userdata('rol') == 'admin'): ?>
                    // Para super admin: guardar sucursal seleccionada y recargar
                    const sucursalActual = document.getElementById('filtroSucursal')?.value;
                    if(sucursalActual) {
                        // Guardar en sessionStorage para mantener después del reload
                        sessionStorage.setItem('sucursalSeleccionada', sucursalActual);
                    }
                    <?php endif; ?>
                    
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'Error al guardar',
                        confirmButtonColor: '#b08c6a'
                    });
                }
            } catch(error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor',
                    confirmButtonColor: '#b08c6a'
                });
            }
        }

        async function toggleEstado(id, nuevoEstado) {
            // Debug: verificar valores
            console.log('Toggle Estado - ID:', id, 'Nuevo Estado:', nuevoEstado, 'Tipo:', typeof nuevoEstado);
            
            const formData = new FormData();
            formData.append('id_categoria', id);
            formData.append('estado', nuevoEstado);

            try {
                const response = await fetch('<?= site_url('admin/categoria_toggle_estado') ?>', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                // Debug: verificar respuesta
                console.log('Respuesta del servidor:', result);

                if(result.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: result.message || 'Estado actualizado correctamente',
                        confirmButtonColor: '#b08c6a',
                        timer: 1500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    
                    <?php if($this->session->userdata('rol') == 'admin'): ?>
                    // Guardar sucursal seleccionada antes de recargar
                    const sucursalActual = document.getElementById('filtroSucursal')?.value;
                    if(sucursalActual) {
                        sessionStorage.setItem('sucursalSeleccionada', sucursalActual);
                    }
                    <?php endif; ?>
                    
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'Error al cambiar el estado',
                        confirmButtonColor: '#b08c6a'
                    });
                }
            } catch(error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor',
                    confirmButtonColor: '#b08c6a'
                });
            }
        }

        async function eliminarCategoria(id, nombre) {
            const result = await Swal.fire({
                title: '¿Estás seguro?',
                html: `¿Deseas eliminar la categoría <strong>"${nombre}"</strong>?<br><br><small class="text-danger">Esta acción no se puede deshacer.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '✓ Sí, eliminar',
                cancelButtonText: '✕ Cancelar',
                reverseButtons: true
            });

            if(!result.isConfirmed) {
                return;
            }

            const formData = new FormData();
            formData.append('id_categoria', id);

            try {
                const response = await fetch('<?= site_url('admin/categoria_eliminar') ?>', {
                    method: 'POST',
                    body: formData
                });

                const resultData = await response.json();

                if(resultData.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: '¡Eliminado!',
                        text: resultData.message,
                        confirmButtonColor: '#b08c6a',
                        timer: 1500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    
                    <?php if($this->session->userdata('rol') == 'admin'): ?>
                    // Guardar sucursal seleccionada antes de recargar
                    const sucursalActual = document.getElementById('filtroSucursal')?.value;
                    if(sucursalActual) {
                        sessionStorage.setItem('sucursalSeleccionada', sucursalActual);
                    }
                    <?php endif; ?>
                    
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: resultData.message || 'Error al eliminar',
                        confirmButtonColor: '#b08c6a'
                    });
                }
            } catch(error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor',
                    confirmButtonColor: '#b08c6a'
                });
            }
        }

        // Filtrar categorías por sucursal (solo para super admin)
        function filtrarPorSucursal() {
            const sucursalId = document.getElementById('filtroSucursal').value;
            const tabla = document.getElementById('tablaCategorias');
            const filas = tabla.querySelectorAll('tbody tr');
            const mensajeSeleccion = document.getElementById('mensajeSeleccionSucursal');
            const tablaContainer = document.getElementById('tablaContainer');
            const contadorBadge = document.getElementById('contadorCategorias');
            let contador = 0;

            // Si no hay sucursal seleccionada, ocultar tabla y mostrar mensaje
            if(sucursalId === '') {
                if(mensajeSeleccion) mensajeSeleccion.style.display = 'block';
                if(tablaContainer) tablaContainer.style.display = 'none';
                if(contadorBadge) contadorBadge.style.display = 'none';
                return;
            }

            // Si hay sucursal seleccionada, mostrar tabla y ocultar mensaje
            if(mensajeSeleccion) mensajeSeleccion.style.display = 'none';
            if(tablaContainer) tablaContainer.style.display = 'block';
            if(contadorBadge) contadorBadge.style.display = 'inline-block';

            // Filtrar filas
            filas.forEach(fila => {
                const sucursalFila = fila.getAttribute('data-sucursal');
                
                if(sucursalFila === sucursalId) {
                    fila.style.display = '';
                    contador++;
                } else {
                    fila.style.display = 'none';
                }
            });

            // Actualizar contador
            if(contadorBadge) {
                contadorBadge.textContent = contador + ' categorías';
            }
        }

        // Función para toggle del dropdown móvil
        function toggleMobileDropdown(trigger) {
            const dropdown = trigger.nextElementSibling;
            const isOpen = dropdown.classList.contains('show');
            
            // Cerrar todos los dropdowns abiertos
            document.querySelectorAll('.mobile-actions-dropdown.show').forEach(d => {
                d.classList.remove('show');
            });
            
            // Toggle del dropdown actual
            if (!isOpen) {
                // Calcular posición del botón para position fixed
                const rect = trigger.getBoundingClientRect();
                dropdown.style.top = (rect.bottom + 2) + 'px';
                dropdown.style.left = (rect.right - 140) + 'px'; // 140px es el ancho mínimo del dropdown
                
                // Ajustar si se sale de la pantalla por la izquierda
                if (rect.right - 140 < 0) {
                    dropdown.style.left = rect.left + 'px';
                }
                
                dropdown.classList.add('show');
                
                // Cerrar al hacer click fuera
                setTimeout(() => {
                    document.addEventListener('click', function closeDropdown(e) {
                        if (!trigger.contains(e.target) && !dropdown.contains(e.target)) {
                            dropdown.classList.remove('show');
                            document.removeEventListener('click', closeDropdown);
                        }
                    });
                }, 10);
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>

