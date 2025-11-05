<?php
// Cargar autenticación primero
require_once __DIR__ . '/../../config/auth.php';
verificarRol('empresa');

// Cargar modelos ANTES del header
require_once __DIR__ . '/../../models/PostulacionDAO.php';
require_once __DIR__ . '/../../models/EmpresaDAO.php';

// Ahora cargar el header
include '../layout/header.php';

// Instanciar DAOs
$postDAO = new PostulacionDAO();
$empresaDAO = new EmpresaDAO();

// Obtener datos
$usuario_id = $_SESSION['id_usuario'];
$empresa = $empresaDAO->obtenerPorUsuario($usuario_id);

// Verificar que la empresa existe
if (!$empresa) {
    echo '<div class="container mt-5"><div class="alert alert-danger">No se encontró tu perfil de empresa. Por favor, contacta al administrador.</div></div>';
    include '../layout/footer.php';
    exit;
}

$postulaciones = $postDAO->listarPorEmpresa($empresa->id_empresa);
?>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
  background: #f8f9fa;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  transition: transform 0.2s;
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>

<div class="container mt-4">
  <!-- Encabezado -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-primary">
      <i class="bi bi-people-fill"></i> Postulaciones Recibidas
    </h3>
    <a href="mis_ofertas.php" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Volver
    </a>
  </div>

  <!-- Mensajes de sesión -->
  <?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?? 'info' ?> alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($_SESSION['mensaje']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php 
      unset($_SESSION['mensaje']);
      unset($_SESSION['tipo_mensaje']);
    ?>
  <?php endif; ?>

  <!-- Listado de postulaciones -->
  <?php if (empty($postulaciones)): ?>
    <div class="alert alert-info text-center">
      <i class="bi bi-inbox fs-3 d-block mb-2"></i>
      <p class="mb-0">Aún no has recibido postulaciones.</p>
    </div>
  <?php else: ?>
    <div class="row g-3">
      <?php foreach ($postulaciones as $p): ?>
        <div class="col-md-6 col-lg-4">
          <div class="card p-3 h-100">
            <!-- Encabezado con nombre y estado -->
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h5 class="text-primary mb-0">
                <?= htmlspecialchars($p['nombre_estudiante']) ?>
              </h5>
              <span class="badge bg-<?= $p['estado_postulacion']=='aceptada'?'success':($p['estado_postulacion']=='rechazada'?'danger':'warning') ?>">
                <?= ucfirst($p['estado_postulacion']) ?>
              </span>
            </div>

            <!-- Información de la oferta -->
            <p class="mb-1">
              <i class="bi bi-briefcase text-muted"></i>
              <strong>Oferta:</strong> <?= htmlspecialchars($p['titulo_oferta']) ?>
            </p>

            <!-- Email del estudiante -->
            <p class="mb-1">
              <i class="bi bi-envelope text-muted"></i>
              <small><?= htmlspecialchars($p['correo_estudiante']) ?></small>
            </p>

            <!-- Fecha de postulación -->
            <p class="text-muted small mb-3">
              <i class="bi bi-calendar"></i>
              <?= date('d/m/Y H:i', strtotime($p['fecha_postulacion'])) ?>
            </p>

            <!-- Botón para ver CV -->
            <?php if (!empty($p['cv_path'])): ?>
              <a href="/PORTALBOLSATRABAJOUNIVERSITARIO/<?= htmlspecialchars($p['cv_path']) ?>" 
                 class="btn btn-outline-info btn-sm mb-2 w-100" 
                 target="_blank">
                <i class="bi bi-file-earmark-pdf"></i> Ver CV
              </a>
            <?php endif; ?>

            <!-- Botones de acción solo si está pendiente -->
            <?php if ($p['estado_postulacion'] === 'pendiente'): ?>
              <div class="mt-auto">
                <form action="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/postulacionControlador.php" method="POST">
                  <input type="hidden" name="id_postulacion" value="<?= $p['id_postulacion'] ?>">
                  <div class="d-grid gap-2">
                    <button name="action" value="aceptar" class="btn btn-success btn-sm">
                      <i class="bi bi-check-circle"></i> Aceptar
                    </button>
                    <button name="action" value="rechazar" class="btn btn-danger btn-sm">
                      <i class="bi bi-x-circle"></i> Rechazar
                    </button>
                  </div>
                </form>
              </div>
            <?php endif; ?>

            <!-- Mostrar comentario si existe -->
            <?php if (!empty($p['comentario_empresa'])): ?>
              <div class="mt-2 p-2 bg-light rounded">
                <small class="text-muted">
                  <strong>Comentario:</strong><br>
                  <?= nl2br(htmlspecialchars($p['comentario_empresa'])) ?>
                </small>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php include '../layout/footer.php'; ?> 
