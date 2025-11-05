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
    $empresaDAO = new EmpresaDAO();
    $empresas = $empresaDAO->listar();
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
      <h2 class="fw-bold text-primary mb-1"><i class="bi bi-briefcase-fill"></i> Nueva Oferta Laboral</h2>
      <p class="text-muted mb-0">Complete el formulario para registrar una nueva oferta</p>
    </div>
    <a href="/PORTALBOLSATRABAJOUNIVERSITARIO/views/admin/ofertas.php" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Volver
    </a>
  </div>

  <!-- Formulario -->
  <div class="card border-0 shadow-sm">
    <div class="card-body p-4">
      <form action="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/ofertaControlador.php" method="POST">
        <input type="hidden" name="action" value="crear_oferta">
        
        <!-- Información de la Empresa -->
        <div class="mb-4">
          <h5 class="fw-semibold mb-3 text-primary">
            <i class="bi bi-building"></i> Información de la Empresa
          </h5>
          <div class="row">
            <div class="col-md-12">
              <div class="mb-3">
                <label for="empresa_id" class="form-label">Empresa <span class="text-danger">*</span></label>
                <select class="form-select" id="empresa_id" name="empresa_id" required>
                  <option value="">Seleccione una empresa</option>
                  <?php foreach ($empresas as $empresa): ?>
                    <?php if ($empresa['estado'] === 'aprobada'): ?>
                      <option value="<?php echo htmlspecialchars($empresa['id_empresa']); ?>">
                        <?php echo htmlspecialchars($empresa['razon_social']); ?>
                      </option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>
                <div class="form-text">Solo se muestran empresas aprobadas</div>
              </div>
            </div>
          </div>
        </div>

        <hr class="my-4">

        <!-- Información de la Oferta -->
        <div class="mb-4">
          <h5 class="fw-semibold mb-3 text-primary">
            <i class="bi bi-file-text"></i> Detalles de la Oferta
          </h5>
          <div class="row">
            <div class="col-md-12">
              <div class="mb-3">
                <label for="titulo" class="form-label">Título del Puesto <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="titulo" name="titulo" required 
                       placeholder="Ej: Desarrollador Web Junior">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="5" required
                          placeholder="Describa las responsabilidades, requisitos y beneficios del puesto..."></textarea>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="mb-3">
                <label for="tipo" class="form-label">Tipo de Contrato <span class="text-danger">*</span></label>
                <select class="form-select" id="tipo" name="tipo" required>
                  <option value="">Seleccione...</option>
                  <option value="practicas">Prácticas</option>
                  <option value="part-time">Part-time</option>
                  <option value="full-time">Full-time</option>
                </select>
              </div>
            </div>

            <div class="col-md-4">
              <div class="mb-3">
                <label for="modalidad" class="form-label">Modalidad <span class="text-danger">*</span></label>
                <select class="form-select" id="modalidad" name="modalidad" required>
                  <option value="">Seleccione...</option>
                  <option value="presencial">Presencial</option>
                  <option value="remoto">Remoto</option>
                  <option value="mixto">Mixto</option>
                </select>
              </div>
            </div>

            <div class="col-md-4">
              <div class="mb-3">
                <label for="salario_referencial" class="form-label">Salario Referencial (S/) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="salario_referencial" name="salario_referencial" 
                       step="0.01" min="0" required placeholder="1200.00">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="fecha_publicacion" class="form-label">Fecha de Publicación <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="fecha_publicacion" name="fecha_publicacion" 
                       value="<?php echo date('Y-m-d'); ?>" required>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label for="fecha_cierre" class="form-label">Fecha de Cierre <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="fecha_cierre" name="fecha_cierre" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="mb-3">
                <label for="estado_oferta" class="form-label">Estado <span class="text-danger">*</span></label>
                <select class="form-select" id="estado_oferta" name="estado_oferta" required>
                  <option value="activa" selected>Activa</option>
                  <option value="pausada">Pausada</option>
                  <option value="cerrada">Cerrada</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Botones -->
        <div class="d-flex justify-content-end gap-2">
          <a href="/PORTALBOLSATRABAJOUNIVERSITARIO/views/admin/ofertas.php" class="btn btn-secondary">
            <i class="bi bi-x-circle"></i> Cancelar
          </a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Guardar Oferta
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Validación de fechas
document.addEventListener('DOMContentLoaded', function() {
  const fechaPublicacion = document.getElementById('fecha_publicacion');
  const fechaCierre = document.getElementById('fecha_cierre');
  
  // Establecer fecha mínima de cierre como hoy
  const hoy = new Date().toISOString().split('T')[0];
  fechaCierre.min = hoy;
  
  // Validar que fecha de cierre sea posterior a fecha de publicación
  fechaPublicacion.addEventListener('change', function() {
    fechaCierre.min = this.value;
    if (fechaCierre.value && fechaCierre.value < this.value) {
      fechaCierre.value = this.value;
    }
  });
  
  fechaCierre.addEventListener('change', function() {
    if (this.value < fechaPublicacion.value) {
      alert('La fecha de cierre debe ser posterior a la fecha de publicación');
      this.value = fechaPublicacion.value;
    }
  });
});
</script>

<style>
.form-label {
  font-weight: 500;
  color: #495057;
}

.card {
  transition: box-shadow 0.3s ease;
}

.form-control:focus,
.form-select:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}

.text-danger {
  font-size: 0.875rem;
}
</style>

<?php include '../layout/footer.php'; ?>