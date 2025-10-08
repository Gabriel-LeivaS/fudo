<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>👥 Gestión de Usuarios - Panel FUDO</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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

        .table {
            margin-top: 20px;
        }

        .table th {
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            color: var(--muted);
            border-bottom: 2px solid #dee2e6;
        }

        .table td {
            vertical-align: middle;
            font-size: 14px;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
        }

        .badge.bg-success {
            background: #d4edda !important;
            color: #155724;
        }

        .badge.bg-danger {
            background: #f8d7da !important;
            color: #721c24;
        }

        .badge.bg-primary {
            background: #d1ecf1 !important;
            color: #0c5460;
        }

        .badge.bg-warning {
            background: #fff3cd !important;
            color: #856404;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .modal-content {
            border-radius: 14px;
            border: none;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            color: white;
            border-radius: 14px 14px 0 0;
            padding: 20px 25px;
        }

        .modal-header h5 {
            font-weight: 700;
            margin: 0;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .modal-body {
            padding: 25px;
        }

        .form-label {
            font-weight: 600;
            font-size: 14px;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent-2);
            box-shadow: 0 0 0 0.2rem rgba(163, 192, 107, 0.15);
        }

        .alert {
            border-radius: 10px;
            border: none;
            font-weight: 600;
        }

        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 15px 20px;
            border-radius: 14px;
            margin-bottom: 30px;
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
        }

        .nav-link:hover {
            background: var(--bg-light);
            color: var(--accent);
        }

        .filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .sucursal-field {
            display: none;
        }

        .sucursal-field.show {
            display: block;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container-fluid">
            <span class="navbar-brand">🍽️ FUDO</span>
            <div class="d-flex align-items-center gap-3">
                <a href="<?= site_url('admin') ?>" class="nav-link">📊 Dashboard</a>
                <a href="<?= site_url('admin/categorias') ?>" class="nav-link">🏷️ Categorías</a>
                <a href="<?= site_url('admin/productos') ?>" class="nav-link">🛍️ Productos</a>
                <a href="<?= site_url('usuarios') ?>" class="nav-link active">👥 Usuarios</a>
                <a href="<?= site_url('sucursales') ?>" class="nav-link">🏢 Sucursales</a>
                <a href="<?= site_url('login/salir') ?>" class="btn btn-danger btn-action">🚪 Salir</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="admin-header">
            <h1>👥 Gestión de Usuarios</h1>
            <p>Administra los usuarios del sistema y sus permisos por sucursal</p>
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
                    <h5 class="mb-0">📋 Lista de Usuarios</h5>
                    <button class="btn btn-primary btn-action" onclick="abrirModalCrear()">
                        ➕ Crear Usuario
                    </button>
                </div>

                <!-- Filtros -->
                <div class="filters">
                    <div class="filter-group">
                        <label class="form-label">🔍 Filtrar por Rol</label>
                        <select class="form-select" id="filtroRol" onchange="filtrarUsuarios()">
                            <option value="">Todos los roles</option>
                            <option value="admin">Super Admin</option>
                            <option value="admin_sucursal">Admin Sucursal</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="form-label">🏢 Filtrar por Sucursal</label>
                        <select class="form-select" id="filtroSucursal" onchange="filtrarUsuarios()">
                            <option value="">Todas las sucursales</option>
                            <?php foreach ($sucursales as $sucursal): ?>
                                <option value="<?= $sucursal->id_sucursal ?>">
                                    <?= $sucursal->nombre ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Nombre Completo</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Sucursal</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaUsuarios">
                            <?php if (empty($usuarios)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        No hay usuarios registrados
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr data-rol="<?= $usuario->rol ?>" 
                                        data-sucursal="<?= $usuario->id_sucursal ?? '' ?>">
                                        <td><strong>#<?= $usuario->id_usuario ?></strong></td>
                                        <td>
                                            <div class="user-info">
                                                <div class="user-avatar">
                                                    <?= strtoupper(substr($usuario->usuario, 0, 1)) ?>
                                                </div>
                                                <strong><?= htmlspecialchars($usuario->usuario) ?></strong>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($usuario->nombre_completo) ?></td>
                                        <td><?= htmlspecialchars($usuario->email) ?></td>
                                        <td>
                                            <?php if ($usuario->rol == 'admin'): ?>
                                                <span class="badge bg-primary">⭐ Super Admin</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">👤 Admin Sucursal</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($usuario->nombre_sucursal): ?>
                                                <span class="badge bg-info">
                                                    🏢 <?= htmlspecialchars($usuario->nombre_sucursal) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($usuario->activo): ?>
                                                <span class="badge bg-success">✅ Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">❌ Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-warning btn-sm btn-action" 
                                                        onclick="abrirModalEditar(<?= $usuario->id_usuario ?>)">
                                                    ✏️
                                                </button>
                                                <?php if ($usuario->activo): ?>
                                                    <button class="btn btn-danger btn-sm btn-action" 
                                                            onclick="cambiarEstado(<?= $usuario->id_usuario ?>, 0)">
                                                        🔒
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-success btn-sm btn-action" 
                                                            onclick="cambiarEstado(<?= $usuario->id_usuario ?>, 1)">
                                                        🔓
                                                    </button>
                                                <?php endif; ?>
                                                <button class="btn btn-danger btn-sm btn-action" 
                                                        onclick="confirmarEliminar(<?= $usuario->id_usuario ?>, '<?= htmlspecialchars($usuario->usuario) ?>')">
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

    <!-- Modal Crear Usuario -->
    <div class="modal fade" id="modalCrear" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>➕ Crear Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formCrear" onsubmit="crearUsuario(event)">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">👤 Usuario *</label>
                            <input type="text" name="usuario" class="form-control" required 
                                   placeholder="Ej: admin_nuevo">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">🔒 Contraseña *</label>
                            <input type="password" name="contrasena" class="form-control" required 
                                   minlength="6" placeholder="Mínimo 6 caracteres">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">📝 Nombre Completo *</label>
                            <input type="text" name="nombre_completo" class="form-control" required 
                                   placeholder="Ej: Juan Pérez">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">📧 Email *</label>
                            <input type="email" name="email" class="form-control" required 
                                   placeholder="correo@ejemplo.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">🎭 Rol *</label>
                            <select name="rol" class="form-select" id="rolCrear" required 
                                    onchange="toggleSucursalField('crear')">
                                <option value="">Selecciona un rol</option>
                                <option value="admin">⭐ Super Admin</option>
                                <option value="admin_sucursal">👤 Admin Sucursal</option>
                            </select>
                        </div>
                        <div class="mb-3 sucursal-field" id="sucursalFieldCrear">
                            <label class="form-label">🏢 Sucursal *</label>
                            <select name="id_sucursal" class="form-select">
                                <option value="">Selecciona una sucursal</option>
                                <?php foreach ($sucursales as $sucursal): ?>
                                    <?php if ($sucursal->activo): ?>
                                        <option value="<?= $sucursal->id_sucursal ?>">
                                            <?= htmlspecialchars($sucursal->nombre) ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            ❌ Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            💾 Guardar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Usuario -->
    <div class="modal fade" id="modalEditar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>✏️ Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formEditar" onsubmit="editarUsuario(event)">
                    <input type="hidden" name="id_usuario" id="editarId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">👤 Usuario *</label>
                            <input type="text" name="usuario" id="editarUsuario" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">🔒 Nueva Contraseña</label>
                            <input type="password" name="contrasena" id="editarContrasena" 
                                   class="form-control" minlength="6" 
                                   placeholder="Dejar vacío para mantener actual">
                            <small class="text-muted">Solo completa si deseas cambiar la contraseña</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">📝 Nombre Completo *</label>
                            <input type="text" name="nombre_completo" id="editarNombre" 
                                   class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">📧 Email *</label>
                            <input type="email" name="email" id="editarEmail" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">🎭 Rol *</label>
                            <select name="rol" id="editarRol" class="form-select" required 
                                    onchange="toggleSucursalField('editar')">
                                <option value="admin">⭐ Super Admin</option>
                                <option value="admin_sucursal">👤 Admin Sucursal</option>
                            </select>
                        </div>
                        <div class="mb-3 sucursal-field" id="sucursalFieldEditar">
                            <label class="form-label">🏢 Sucursal *</label>
                            <select name="id_sucursal" id="editarSucursal" class="form-select">
                                <option value="">Selecciona una sucursal</option>
                                <?php foreach ($sucursales as $sucursal): ?>
                                    <?php if ($sucursal->activo): ?>
                                        <option value="<?= $sucursal->id_sucursal ?>">
                                            <?= htmlspecialchars($sucursal->nombre) ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            ❌ Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            💾 Actualizar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const usuarios = <?= json_encode($usuarios) ?>;

        function abrirModalCrear() {
            document.getElementById('formCrear').reset();
            document.getElementById('sucursalFieldCrear').classList.remove('show');
            new bootstrap.Modal(document.getElementById('modalCrear')).show();
        }

        function abrirModalEditar(id) {
            const usuario = usuarios.find(u => u.id_usuario == id);
            if (!usuario) return;

            document.getElementById('editarId').value = usuario.id_usuario;
            document.getElementById('editarUsuario').value = usuario.usuario;
            document.getElementById('editarNombre').value = usuario.nombre_completo;
            document.getElementById('editarEmail').value = usuario.email;
            document.getElementById('editarRol').value = usuario.rol;
            document.getElementById('editarContrasena').value = '';
            
            if (usuario.rol === 'admin_sucursal') {
                document.getElementById('sucursalFieldEditar').classList.add('show');
                document.getElementById('editarSucursal').value = usuario.id_sucursal || '';
                document.getElementById('editarSucursal').required = true;
            } else {
                document.getElementById('sucursalFieldEditar').classList.remove('show');
                document.getElementById('editarSucursal').required = false;
            }

            new bootstrap.Modal(document.getElementById('modalEditar')).show();
        }

        function toggleSucursalField(tipo) {
            const rol = document.getElementById(`${tipo === 'crear' ? 'rolCrear' : 'editarRol'}`).value;
            const field = document.getElementById(`sucursalField${tipo.charAt(0).toUpperCase() + tipo.slice(1)}`);
            const select = field.querySelector('select');

            if (rol === 'admin_sucursal') {
                field.classList.add('show');
                select.required = true;
            } else {
                field.classList.remove('show');
                select.required = false;
                select.value = '';
            }
        }

        async function crearUsuario(e) {
            e.preventDefault();
            const formData = new FormData(e.target);

            try {
                const response = await fetch('<?= site_url("usuarios/crear") ?>', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    alert('✅ ' + data.message);
                    location.reload();
                } else {
                    alert('❌ ' + data.message);
                }
            } catch (error) {
                alert('❌ Error al crear el usuario');
                console.error(error);
            }
        }

        async function editarUsuario(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const id = formData.get('id_usuario');

            try {
                const response = await fetch(`<?= site_url("usuarios/editar") ?>/${id}`, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    alert('✅ ' + data.message);
                    location.reload();
                } else {
                    alert('❌ ' + data.message);
                }
            } catch (error) {
                alert('❌ Error al actualizar el usuario');
                console.error(error);
            }
        }

        async function cambiarEstado(id, nuevoEstado) {
            if (!confirm(`¿Deseas ${nuevoEstado ? 'activar' : 'desactivar'} este usuario?`)) {
                return;
            }

            try {
                const response = await fetch(`<?= site_url("usuarios/cambiar_estado") ?>/${id}/${nuevoEstado}`, {
                    method: 'POST'
                });

                const data = await response.json();

                if (data.success) {
                    alert('✅ ' + data.message);
                    location.reload();
                } else {
                    alert('❌ ' + data.message);
                }
            } catch (error) {
                alert('❌ Error al cambiar el estado');
                console.error(error);
            }
        }

        async function confirmarEliminar(id, usuario) {
            if (!confirm(`⚠️ ¿Estás seguro de eliminar al usuario "${usuario}"?\n\nEsta acción no se puede deshacer.`)) {
                return;
            }

            try {
                const response = await fetch(`<?= site_url("usuarios/eliminar") ?>/${id}`, {
                    method: 'POST'
                });

                const data = await response.json();

                if (data.success) {
                    alert('✅ ' + data.message);
                    location.reload();
                } else {
                    alert('❌ ' + data.message);
                }
            } catch (error) {
                alert('❌ Error al eliminar el usuario');
                console.error(error);
            }
        }

        function filtrarUsuarios() {
            const filtroRol = document.getElementById('filtroRol').value;
            const filtroSucursal = document.getElementById('filtroSucursal').value;
            const filas = document.querySelectorAll('#tablaUsuarios tr[data-rol]');

            filas.forEach(fila => {
                const rol = fila.dataset.rol;
                const sucursal = fila.dataset.sucursal;
                let mostrar = true;

                if (filtroRol && rol !== filtroRol) {
                    mostrar = false;
                }

                if (filtroSucursal && sucursal !== filtroSucursal) {
                    mostrar = false;
                }

                fila.style.display = mostrar ? '' : 'none';
            });
        }
    </script>
</body>
</html>
