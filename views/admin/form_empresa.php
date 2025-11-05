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
?>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h3 class="fw-bold text-primary mb-1">Registrar Nueva Empresa</h3>
      <p class="text-muted mb-0">Complete los datos de la empresa</p>
    </div>
    <a href="/PORTALBOLSATRABAJOUNIVERSITARIO/views/admin/empresas.php" class="btn btn-secondary">
      <i class="bi bi-arrow-left"></i> Volver
    </a>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body p-4">
      <form action="/portalBolsaTrabajoUniversitario/controllers/adminControlador.php" method="POST" id="formEmpresa">
        <input type="hidden" name="action" value="crear_empresa">

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Razón Social <span class="text-danger">*</span></label>
            <input type="text" name="razon_social" class="form-control" placeholder="Ej: Empresa SAC" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">RUC <span class="text-danger">*</span></label>
            <input type="text" name="ruc" class="form-control" placeholder="Ej: 20123456789" pattern="[0-9]{11}" maxlength="11" required>
            <small class="text-muted">11 dígitos</small>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Dirección</label>
            <input type="text" name="direccion" class="form-control" placeholder="Ej: Av. Principal 123">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Teléfono</label>
            <input type="text" name="telefono" class="form-control" placeholder="Ej: 987654321" pattern="[0-9]{9}" maxlength="9">
            <small class="text-muted">9 dígitos</small>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Correo de Contacto <span class="text-danger">*</span></label>
          <input type="email" name="correo_contacto" class="form-control" placeholder="correo@empresa.com" required>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Estado Inicial</label>
          <select name="estado" class="form-select">
            <option value="pendiente">Pendiente de revisión</option>
            <option value="aprobada" selected>Aprobada</option>
            <option value="rechazada">Rechazada</option>
          </select>
        </div>

        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-save"></i> Guardar Empresa
          </button>
          <a href="/PORTALBOLSATRABAJOUNIVERSITARIO/views/admin/empresas.php" class="btn btn-outline-secondary">
            Cancelar
          </a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Debug del formulario
document.getElementById('formEmpresa').addEventListener('submit', function(e) {
    console.log('Formulario enviándose...');
    console.log('Action:', this.action);
    console.log('Method:', this.method);
    
    // Verificar que todos los campos requeridos tengan valor
    const razonSocial = document.querySelector('input[name="razon_social"]').value;
    const ruc = document.querySelector('input[name="ruc"]').value;
    const correo = document.querySelector('input[name="correo_contacto"]').value;
    
    console.log('Razón Social:', razonSocial);
    console.log('RUC:', ruc);
    console.log('Correo:', correo);
    
    if (!razonSocial || !ruc || !correo) {
        alert('Por favor complete todos los campos requeridos');
        e.preventDefault();
        return false;
    }
});
</script>

<?php include '../layout/footer.php'; ?>