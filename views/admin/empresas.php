<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario sea admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: /portalBolsaTrabajoUniversitario/views/errores/acceso_denegado.php");
    exit;
}

include '../layout/header.php';
require_once __DIR__ . '/../../models/EmpresaDAO.php';

try {
    $dao = new EmpresaDAO();
    $empresas = $dao->listar();
} catch (Exception $e) {
    die("Error al cargar empresas: " . $e->getMessage());
}

// Mostrar mensajes de sesión
$mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : null;
$tipo_mensaje = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : 'info';
if ($mensaje) {
    unset($_SESSION['mensaje']);
    unset($_SESSION['tipo_mensaje']);
}
?>

<div class="container mt-4">
  <!-- Mensaje de alerta -->
  <?php if ($mensaje): ?>
  <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
    <i class="bi bi-<?php echo $tipo_mensaje === 'success' ? 'check-circle' : ($tipo_mensaje === 'danger' ? 'exclamation-triangle' : 'info-circle'); ?>"></i>
    <?php echo htmlspecialchars($mensaje); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold text-primary mb-1"><i class="bi bi-building"></i> Gestión de Empresas</h2>
      <p class="text-muted mb-0">Administra las empresas registradas en el sistema</p>
    </div>
    <a href="/PORTALBOLSATRABAJOUNIVERSITARIO/views/admin/form_empresa.php" class="btn btn-primary">
      <i class="bi bi-plus-circle"></i> Nueva Empresa
    </a>
  </div>

  <!-- Estadísticas -->
  <?php
  $total = is_array($empresas) ? count($empresas) : 0;
  $aprobadas = 0;
  $pendientes = 0;
  $rechazadas = 0;
  
  if (is_array($empresas)) {
      foreach ($empresas as $e) {
          if (isset($e['estado'])) {
              switch ($e['estado']) {
                  case 'aprobada':
                      $aprobadas++;
                      break;
                  case 'pendiente':
                      $pendientes++;
                      break;
                  case 'rechazada':
                      $rechazadas++;
                      break;
              }
          }
      }
  }
  ?>
  
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div><h6 class="text-muted mb-1">Total</h6><h3 class="mb-0 fw-bold"><?php echo $total; ?></h3></div>
          <div class="bg-primary bg-opacity-10 p-3 rounded"><i class="bi bi-building text-primary fs-4"></i></div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div><h6 class="text-muted mb-1">Aprobadas</h6><h3 class="mb-0 fw-bold text-success"><?php echo $aprobadas; ?></h3></div>
          <div class="bg-success bg-opacity-10 p-3 rounded"><i class="bi bi-check-circle text-success fs-4"></i></div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div><h6 class="text-muted mb-1">Pendientes</h6><h3 class="mb-0 fw-bold text-warning"><?php echo $pendientes; ?></h3></div>
          <div class="bg-warning bg-opacity-10 p-3 rounded"><i class="bi bi-clock-history text-warning fs-4"></i></div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div><h6 class="text-muted mb-1">Rechazadas</h6><h3 class="mb-0 fw-bold text-danger"><?php echo $rechazadas; ?></h3></div>
          <div class="bg-danger bg-opacity-10 p-3 rounded"><i class="bi bi-x-circle text-danger fs-4"></i></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabla -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-semibold">Lista de Empresas</h5>
      <div class="input-group" style="width: 300px;">
        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
        <input type="text" class="form-control" id="searchInput" placeholder="Buscar empresa...">
      </div>
    </div>

    <div class="card-body p-0">
      <?php if (empty($empresas)): ?>
        <div class="text-center py-5">
          <i class="bi bi-inbox fs-1 text-muted"></i>
          <p class="text-muted mt-3">No hay empresas registradas</p>
          <a href="/PORTALBOLSATRABAJOUNIVERSITARIO/views/admin/form_empresa.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Agregar Primera Empresa
          </a>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" id="empresasTable">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>Razón Social</th>
                <th>Correo</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($empresas as $e): ?>
              <tr>
                <td>#<?php echo htmlspecialchars($e['id_empresa']); ?></td>
                <td><?php echo htmlspecialchars($e['razon_social']); ?></td>
                <td><?php echo htmlspecialchars($e['correo_contacto']); ?></td>
                <td class="text-center">
                  <?php
                  $estadoClass = 'bg-warning';
                  if ($e['estado'] == 'aprobada') {
                      $estadoClass = 'bg-success';
                  } elseif ($e['estado'] == 'rechazada') {
                      $estadoClass = 'bg-danger';
                  }
                  ?>
                  <span class="badge <?php echo $estadoClass; ?>">
                    <?php echo ucfirst($e['estado']); ?>
                  </span>
                </td>
                <td class="text-center">
                  <button class="btn btn-outline-primary btn-sm me-2"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEditar"
                    data-id="<?php echo htmlspecialchars($e['id_empresa']); ?>"
                    data-razon="<?php echo htmlspecialchars($e['razon_social']); ?>"
                    data-correo="<?php echo htmlspecialchars($e['correo_contacto']); ?>"
                    data-estado="<?php echo htmlspecialchars($e['estado']); ?>">
                    <i class="bi bi-pencil"></i> Editar
                  </button>
                  <button class="btn btn-outline-danger btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEliminar"
                    data-id="<?php echo htmlspecialchars($e['id_empresa']); ?>"
                    data-razon="<?php echo htmlspecialchars($e['razon_social']); ?>">
                    <i class="bi bi-trash"></i> Eliminar
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- MODAL EDITAR -->
<div class="modal fade" id="modalEditar" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-sm">
      <form action="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/adminControlador.php" method="POST">
        <input type="hidden" name="action" value="editar_empresa">
        <input type="hidden" name="id_empresa" id="editId">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-pencil"></i> Editar Empresa</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Razón Social</label>
            <input type="text" name="razon_social" id="editRazon" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Correo</label>
            <input type="email" name="correo_contacto" id="editCorreo" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Estado</label>
            <select name="estado" id="editEstado" class="form-select">
              <option value="pendiente">Pendiente</option>
              <option value="aprobada">Aprobada</option>
              <option value="rechazada">Rechazada</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL ELIMINAR -->
<div class="modal fade" id="modalEliminar" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-sm">
      <form action="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/adminControlador.php" method="POST">
        <input type="hidden" name="action" value="eliminar_empresa">
        <input type="hidden" name="id_empresa" id="deleteId">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title"><i class="bi bi-trash"></i> Confirmar Eliminación</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center">
          <i class="bi bi-exclamation-triangle text-danger fs-1 mb-3"></i>
          <p>¿Eliminar la empresa <strong id="deleteRazon"></strong>?</p>
          <p class="text-muted small mb-0">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i> Eliminar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    searchInput.addEventListener('keyup', function() {
      const val = this.value.toLowerCase();
      const rows = document.querySelectorAll('#empresasTable tbody tr');
      rows.forEach(function(tr) {
        const text = tr.textContent.toLowerCase();
        tr.style.display = text.includes(val) ? '' : 'none';
      });
    });
  }

  const modalEditar = document.getElementById('modalEditar');
  if (modalEditar) {
    modalEditar.addEventListener('show.bs.modal', function(event) {
      const btn = event.relatedTarget;
      document.getElementById('editId').value = btn.getAttribute('data-id');
      document.getElementById('editRazon').value = btn.getAttribute('data-razon');
      document.getElementById('editCorreo').value = btn.getAttribute('data-correo');
      document.getElementById('editEstado').value = btn.getAttribute('data-estado');
    });
  }

  const modalEliminar = document.getElementById('modalEliminar');
  if (modalEliminar) {
    modalEliminar.addEventListener('show.bs.modal', function(event) {
      const btn = event.relatedTarget;
      document.getElementById('deleteId').value = btn.getAttribute('data-id');
      document.getElementById('deleteRazon').textContent = btn.getAttribute('data-razon');
    });
  }
});
</script>

<style>
.card {
  transition: transform 0.2s, box-shadow 0.2s;
}
.card:hover { 
  transform: translateY(-3px); 
  box-shadow: 0 4px 12px rgba(0,0,0,0.1)!important; 
}
.table tbody tr {
  transition: background-color 0.2s;
}
.table tbody tr:hover { 
  background-color: rgba(var(--bs-primary-rgb), 0.05); 
}
</style>

<?php include '../layout/footer.php'; ?>