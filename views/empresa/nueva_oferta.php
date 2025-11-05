<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario sea empresa
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'empresa') {
    header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/errores/acceso_denegado.php");
    exit;
}

include '../layout/header.php';
require_once __DIR__ . '/../../models/EmpresaDAO.php';

try {
    // Obtener la empresa asociada al usuario logueado
    $empresaDAO = new EmpresaDAO();
    $empresa = $empresaDAO->obtenerPorUsuario($_SESSION['id_usuario']);
    
    if (!$empresa) {
        throw new Exception("No se encontró la empresa asociada a este usuario");
    }
    
    // Verificar que la empresa esté aprobada
    if ($empresa->estado !== 'aprobada') {
        $_SESSION['mensaje'] = 'Su empresa debe estar aprobada para publicar ofertas';
        $_SESSION['tipo_mensaje'] = 'warning';
        header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/empresa/mis_ofertas.php");
        exit;
    }
} catch (Exception $e) {
    die("Error al cargar información de la empresa: " . $e->getMessage());
}

// Mostrar mensajes de sesión
$mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : null;
$tipo_mensaje = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : 'info';
if ($mensaje) {
    unset($_SESSION['mensaje']);
    unset($_SESSION['tipo_mensaje']);
}
?>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
  body {
    background: #f5f7fa;
    font-family: "Segoe UI", sans-serif;
  }

  .main-container {
    max-width: 900px;
    margin: 3rem auto;
    padding: 1rem;
  }

  .page-header {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    color: white;
    box-shadow: 0 4px 15px rgba(30,60,114,0.2);
  }

  .page-header h1 {
    font-size: 1.6rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
  }

  .page-header p {
    font-size: 0.95rem;
    opacity: 0.9;
    margin: 0;
  }

  .form-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 2rem;
    border-top: 4px solid #1e88e5;
  }

  .form-card label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
  }

  .form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    padding: 0.75rem;
  }

  .form-control:focus, .form-select:focus {
    border-color: #1e88e5;
    box-shadow: 0 0 0 0.25rem rgba(30,136,229,0.15);
  }

  .btn-save {
    background: #1e88e5;
    color: white;
    font-weight: 600;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 8px;
    transition: all 0.3s;
  }

  .btn-save:hover {
    background: #1565c0;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(30,136,229,0.3);
    color: white;
  }

  .btn-cancel {
    background: #e9ecef;
    color: #495057;
    font-weight: 600;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 8px;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
  }

  .btn-cancel:hover {
    background: #dee2e6;
    color: #1e3c72;
    transform: translateY(-2px);
  }

  .section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e3c72;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e9ecef;
  }

  /* Modo oscuro */
  body.modo-oscuro {
    background-color: #1a1a1a;
    color: #e8eaed;
  }

  body.modo-oscuro .page-header {
    background: linear-gradient(135deg, #202124, #3c4043);
    color: #e8eaed;
  }

  body.modo-oscuro .form-card {
    background: #2d2d2d;
    color: #e8eaed;
    border-top: 4px solid #8ab4f8;
  }

  body.modo-oscuro .form-control,
  body.modo-oscuro .form-select {
    background-color: #3c4043;
    color: #e8eaed;
    border: 1px solid #5f6368;
  }

  body.modo-oscuro .form-control:focus,
  body.modo-oscuro .form-select:focus {
    background-color: #3c4043;
    border-color: #8ab4f8;
  }

  body.modo-oscuro .btn-save {
    background: #8ab4f8;
    color: #202124;
  }

  body.modo-oscuro .btn-cancel {
    background: #5f6368;
    color: #fff;
  }

  body.modo-oscuro .section-title {
    color: #8ab4f8;
    border-bottom-color: #5f6368;
  }

  .empresa-info {
    background: #e3f2fd;
    border-left: 4px solid #1e88e5;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 2rem;
  }

  body.modo-oscuro .empresa-info {
    background: #1e3a5f;
    border-left-color: #8ab4f8;
  }
</style>

<!-- Botón modo oscuro -->
<button id="btnModoOscuro" class="btn-modo-oscuro" onclick="toggleModoOscuro()" title="Cambiar tema">
  <svg id="iconoSol" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
    <path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/>
  </svg>
  <svg id="iconoLuna" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" style="display:none;">
    <path d="M6 .278a.768.768 0 0 1 .08.858A7.208 7.208 0 0 0 5.202 4.6C5.202 8.62 8.48 11.877 12.52 11.877c.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"/>
  </svg>
</button>

<!-- Contenido principal -->
<div class="main-container">
  <!-- Mensaje de alerta -->
  <?php if ($mensaje): ?>
  <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
    <i class="bi bi-<?php echo $tipo_mensaje === 'success' ? 'check-circle' : ($tipo_mensaje === 'danger' ? 'exclamation-triangle' : 'info-circle'); ?>"></i>
    <?php echo htmlspecialchars($mensaje); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <div class="page-header">
    <h1><i class="bi bi-plus-circle"></i> Publicar Nueva Oferta</h1>
    <p>Complete el formulario para publicar una nueva oferta laboral</p>
  </div>

  <!-- Información de la empresa -->
  <div class="empresa-info">
    <strong><i class="bi bi-building"></i> Empresa:</strong> <?= htmlspecialchars($empresa->razon_social) ?>
  </div>

  <div class="form-card">
    <form action="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/ofertaControlador.php" method="POST" id="formOferta">
      <input type="hidden" name="action" value="crear_oferta">
      <input type="hidden" name="empresa_id" value="<?= $empresa->id_empresa ?>">

      <!-- Información básica -->
      <div class="section-title">
        <i class="bi bi-file-text"></i> Información de la Oferta
      </div>

      <div class="mb-3">
        <label class="form-label">Título del Puesto <span class="text-danger">*</span></label>
        <input type="text" name="titulo" class="form-control" required 
               placeholder="Ej: Desarrollador Web Junior, Asistente de Marketing, etc.">
      </div>

      <div class="mb-3">
        <label class="form-label">Descripción <span class="text-danger">*</span></label>
        <textarea name="descripcion" rows="6" class="form-control" required
                  placeholder="Describa las responsabilidades, requisitos y beneficios del puesto..."></textarea>
        <small class="text-muted">Incluya: responsabilidades, requisitos académicos, habilidades necesarias y beneficios</small>
      </div>

      <!-- Detalles del puesto -->
      <div class="section-title mt-4">
        <i class="bi bi-info-circle"></i> Detalles del Puesto
      </div>

      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label">Tipo de Contrato <span class="text-danger">*</span></label>
          <select name="tipo" class="form-select" required>
            <option value="">Seleccione...</option>
            <option value="practicas">Prácticas</option>
            <option value="part-time">Part-time</option>
            <option value="full-time">Full-time</option>
          </select>
        </div>

        <div class="col-md-4 mb-3">
          <label class="form-label">Modalidad <span class="text-danger">*</span></label>
          <select name="modalidad" class="form-select" required>
            <option value="">Seleccione...</option>
            <option value="presencial">Presencial</option>
            <option value="remoto">Remoto</option>
            <option value="mixto">Mixto</option>
          </select>
        </div>

        <div class="col-md-4 mb-3">
          <label class="form-label">Salario Referencial (S/)</label>
          <input type="number" name="salario_referencial" step="0.01" min="0" 
                 class="form-control" placeholder="1200.00">
          <small class="text-muted">Opcional</small>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Fecha de Publicación <span class="text-danger">*</span></label>
          <input type="date" name="fecha_publicacion" class="form-control" 
                 value="<?= date('Y-m-d') ?>" required readonly>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Fecha de Cierre <span class="text-danger">*</span></label>
          <input type="date" name="fecha_cierre" id="fecha_cierre" class="form-control" 
                 min="<?= date('Y-m-d') ?>" required>
          <small class="text-muted">Fecha límite para postulaciones</small>
        </div>
      </div>

      <!-- Botones -->
      <div class="d-flex justify-content-between mt-4">
        <a href="/PORTALBOLSATRABAJOUNIVERSITARIO/views/empresa/mis_ofertas.php" class="btn-cancel">
          <i class="bi bi-arrow-left-circle"></i> Cancelar
        </a>
        <button type="submit" class="btn-save">
          <i class="bi bi-check-circle"></i> Publicar Oferta
        </button>
      </div>
    </form>
  </div>
</div>

<script>
// Cargar preferencia de modo oscuro
document.addEventListener('DOMContentLoaded', function() {
  const modoOscuro = localStorage.getItem('modoOscuro') === 'true';
  if (modoOscuro) {
    document.body.classList.add('modo-oscuro');
    document.getElementById('iconoSol').style.display = 'none';
    document.getElementById('iconoLuna').style.display = 'block';
  }

  // Validación de fechas
  const fechaCierre = document.getElementById('fecha_cierre');
  const hoy = new Date().toISOString().split('T')[0];
  fechaCierre.min = hoy;
});

function toggleModoOscuro() {
  const body = document.body;
  const iconoSol = document.getElementById('iconoSol');
  const iconoLuna = document.getElementById('iconoLuna');

  body.classList.toggle('modo-oscuro');

  if (body.classList.contains('modo-oscuro')) {
    iconoSol.style.display = 'none';
    iconoLuna.style.display = 'block';
    localStorage.setItem('modoOscuro', 'true');
  } else {
    iconoSol.style.display = 'block';
    iconoLuna.style.display = 'none';
    localStorage.setItem('modoOscuro', 'false');
  }
}

// Validación del formulario
document.getElementById('formOferta').addEventListener('submit', function(e) {
  const titulo = document.querySelector('input[name="titulo"]').value.trim();
  const descripcion = document.querySelector('textarea[name="descripcion"]').value.trim();
  const tipo = document.querySelector('select[name="tipo"]').value;
  const modalidad = document.querySelector('select[name="modalidad"]').value;
  const fechaCierre = document.querySelector('input[name="fecha_cierre"]').value;

  if (!titulo || !descripcion || !tipo || !modalidad || !fechaCierre) {
    alert('Por favor complete todos los campos requeridos');
    e.preventDefault();
    return false;
  }

  // Validar que la fecha de cierre sea futura
  const hoy = new Date().toISOString().split('T')[0];
  if (fechaCierre < hoy) {
    alert('La fecha de cierre debe ser igual o posterior a hoy');
    e.preventDefault();
    return false;
  }

  console.log('Formulario válido, enviando...');
});
</script>

<?php include '../layout/footer.php'; ?>