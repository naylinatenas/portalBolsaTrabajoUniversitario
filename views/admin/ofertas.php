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
require_once _DIR_ . '/../../models/OfertaDAO.php';
require_once _DIR_ . '/../../models/EmpresaDAO.php';

try {
    $dao = new OfertaDAO();
    $ofertas = $dao->listar();
    
    $empresaDAO = new EmpresaDAO();
    $empresas = $empresaDAO->listar();
} catch (Exception $e) {
    die("Error al cargar datos: " . $e->getMessage());
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
      <h2 class="fw-bold text-primary mb-1"><i class="bi bi-briefcase"></i> Gestión de Ofertas Laborales</h2>
      <p class="text-muted mb-0">Administra las ofertas publicadas en el sistema</p>
    </div>
    <a href="/PORTALBOLSATRABAJOUNIVERSITARIO/views/admin/form_oferta.php" class="btn btn-primary">
      <i class="bi bi-plus-circle"></i> Nueva Oferta
    </a>
  </div>

  <!-- Estadísticas -->
  <?php
  $total = is_array($ofertas) ? count($ofertas) : 0;
  $activas = 0;
  $pausadas = 0;
  $cerradas = 0;
  
  if (is_array($ofertas)) {
      foreach ($ofertas as $o) {
          if (isset($o['estado_oferta'])) {
              switch ($o['estado_oferta']) {
                  case 'activa':
                      $activas++;
                      break;
                  case 'pausada':
                      $pausadas++;
                      break;
                  case 'cerrada':
                      $cerradas++;
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
          <div class="bg-primary bg-opacity-10 p-3 rounded"><i class="bi bi-briefcase text-primary fs-4"></i></div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div><h6 class="text-muted mb-1">Activas</h6><h3 class="mb-0 fw-bold text-success"><?php echo $activas; ?></h3></div>
          <div class="bg-success bg-opacity-10 p-3 rounded"><i class="bi bi-check-circle text-success fs-4"></i></div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div><h6 class="text-muted mb-1">Pausadas</h6><h3 class="mb-0 fw-bold text-warning"><?php echo $pausadas; ?></h3></div>
          <div class="bg-warning bg-opacity-10 p-3 rounded"><i class="bi bi-pause-circle text-warning fs-4"></i></div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div><h6 class="text-muted mb-1">Cerradas</h6><h3 class="mb-0 fw-bold text-danger"><?php echo $cerradas; ?></h3></div>
          <div class="bg-danger bg-opacity-10 p-3 rounded"><i class="bi bi-x-circle text-danger fs-4"></i></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabla -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-semibold">Lista de Ofertas</h5>
      <div class="input-group" style="width: 300px;">
        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
        <input type="text" class="form-control" id="searchInput" placeholder="Buscar oferta...">
      </div>
    </div>

    <div class="card-body p-0">
      <?php if (empty($ofertas)): ?>
        <div class="text-center py-5">
          <i class="bi bi-inbox fs-1 text-muted"></i>
          <p class="text-muted mt-3">No hay ofertas registradas</p>
          <a href="/PORTALBOLSATRABAJOUNIVERSITARIO/views/admin/form_oferta.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Agregar Primera Oferta
          </a>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" id="ofertasTable">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Empresa</th>
                <th>Tipo</th>
                <th>Modalidad</th>
                <th>Salario</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($ofertas as $o): ?>
              <tr>
                <td>#<?php echo htmlspecialchars($o['id_oferta']); ?></td>
                <td><?php echo htmlspecialchars($o['titulo']); ?></td>
                <td><?php echo htmlspecialchars($o['razon_social']); ?></td>
                <td><span class="badge bg-info"><?php echo ucfirst($o['tipo']); ?></span></td>
                <td><span class="badge bg-secondary"><?php echo ucfirst($o['modalidad']); ?></span></td>
                <td>S/ <?php echo number_format($o['salario_referencial'], 2); ?></td>
                <td class="text-center">
                  <?php
                  $estadoClass = 'bg-warning';
                  if ($o['estado_oferta'] == 'activa') {
                      $estadoClass = 'bg-success';
                  } elseif ($o['estado_oferta'] == 'cerrada') {
                      $estadoClass = 'bg-danger';
                  }
                  ?>
                  <span class="badge <?php echo $estadoClass; ?>">
                    <?php echo ucfirst($o['estado_oferta']); ?>
                  </span>
                </td>
                <td class="text-center">
                  <button class="btn btn-outline-primary btn-sm me-2"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEditar"
                    data-id="<?php echo htmlspecialchars($o['id_oferta']); ?>"
                    data-empresa="<?php echo htmlspecialchars($o['empresa_id']); ?>"
                    data-titulo="<?php echo htmlspecialchars($o['titulo']); ?>"
                    data-tipo="<?php echo htmlspecialchars($o['tipo']); ?>"
                    data-modalidad="<?php echo htmlspecialchars($o['modalidad']); ?>"
                    data-salario="<?php echo htmlspecialchars($o['salario_referencial']); ?>"
                    data-estado="<?php echo htmlspecialchars($o['estado_oferta']); ?>">
                    <i class="bi bi-pencil"></i> Editar
                  </button>
                  <button class="btn btn-outline-danger btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEliminar"
                    data-id="<?php echo htmlspecialchars($o['id_oferta']); ?>"
                    data-titulo="<?php echo htmlspecialchars($o['titulo']); ?>">
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
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-sm">
      <form action="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/ofertaControlador.php" method="POST">
        <input type="hidden" name="action" value="editar_oferta">
        <input type="hidden" name="id_oferta" id="editId">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-pencil"></i> Editar Oferta</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 mb-3">
              <label class="form-label">Empresa</label>
              <select name="empresa_id" id="editEmpresa" class="form-select" required>
                <option value="">Seleccione una empresa</option>
                <?php foreach ($empresas as $empresa): ?>
                  <?php if ($empresa['estado'] === 'aprobada'): ?>
                    <option value="<?php echo htmlspecialchars($empresa['id_empresa']); ?>">
                      <?php echo htmlspecialchars($empresa['razon_social']); ?>
                    </option>
                  <?php endif; ?>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 mb-3">
              <label class="form-label">Título</label>
              <input type="text" name="titulo" id="editTitulo" class="form-control" required>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Tipo</label>
              <select name="tipo" id="editTipo" class="form-select" required>
                <option value="practicas">Prácticas</option>
                <option value="part-time">Part-time</option>
                <option value="full-time">Full-time</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Modalidad</label>
              <select name="modalidad" id="editModalidad" class="form-select" required>
                <option value="presencial">Presencial</option>
                <option value="remoto">Remoto</option>
                <option value="mixto">Mixto</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Salario Referencial</label>
              <input type="number" name="salario_referencial" id="editSalario" class="form-control" step="0.01" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Estado</label>
              <select name="estado_oferta" id="editEstado" class="form-select" required>
                <option value="activa">Activa</option>
                <option value="pausada">Pausada</option>
                <option value="cerrada">Cerrada</option>
              </select>
            </div>
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
      <form action="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/ofertaControlador.php" method="POST">
        <input type="hidden" name="action" value="eliminar_oferta">
        <input type="hidden" name="id_oferta" id="deleteId">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title"><i class="bi bi-trash"></i> Confirmar Eliminación</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center">
          <i class="bi bi-exclamation-triangle text-danger fs-1 mb-3"></i>
          <p>¿Eliminar la oferta <strong id="deleteTitulo"></strong>?</p>
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
      const rows = document.querySelectorAll('#ofertasTable tbody tr');
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
      document.getElementById('editEmpresa').value = btn.getAttribute('data-empresa');
      document.getElementById('editTitulo').value = btn.getAttribute('data-titulo');
      document.getElementById('editTipo').value = btn.getAttribute('data-tipo');
      document.getElementById('editModalidad').value = btn.getAttribute('data-modalidad');
      document.getElementById('editSalario').value = btn.getAttribute('data-salario');
      document.getElementById('editEstado').value = btn.getAttribute('data-estado');
    });
  }

  const modalEliminar = document.getElementById('modalEliminar');
  if (modalEliminar) {
    modalEliminar.addEventListener('show.bs.modal', function(event) {
      const btn = event.relatedTarget;
      document.getElementById('deleteId').value = btn.getAttribute('data-id');
      document.getElementById('deleteTitulo').textContent = btn.getAttribute('data-titulo');
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
