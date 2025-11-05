<?php
require_once __DIR__ . '/../../config/auth.php';
verificarRol('estudiante');

include '../layout/header.php';
require_once __DIR__ . '/../../models/EstudianteDAO.php';
require_once __DIR__ . '/../../models/PostulacionDAO.php';
require_once __DIR__ . '/../../models/OfertaDAO.php';

$estDAO = new EstudianteDAO();
$postDAO = new PostulacionDAO();
$ofertaDAO = new OfertaDAO();

$usuario_id = $_SESSION['id_usuario'] ?? null;
$estudiante = $estDAO->obtenerPorUsuario($usuario_id);
$total_postulaciones = $estudiante ? count($postDAO->listarPorEstudiante($estudiante->id_estudiante)) : 0;
$ofertas_activas = $ofertaDAO->listarActivas();
$total_ofertas = count($ofertas_activas);
$ofertas_recientes = array_slice($ofertas_activas, 0, 5);
?>

<div class="container mt-4">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <h2 class="fw-bold text-primary m-0">
      Bienvenido, <?= $_SESSION['nombre'] ?? 'Estudiante' ?> 👋
    </h2>

    <a href="perfil.php" class="btn btn-outline-primary d-flex align-items-center gap-2 shadow-sm hover-elevate-sm">
      <i class="bi bi-person-badge-fill fs-5"></i> Ver Perfil
    </a>
  </div>

  <!-- TARJETAS -->
<div class="row g-4 mb-4 justify-content-center">
    <div class="col-md-6 col-lg-4">
      <div class="card card-dash text-center p-4 shadow-sm hover-elevate">
        <i class="bi bi-briefcase-fill text-primary fs-1 mb-2"></i>
        <h6 class="text-muted mb-1">Ofertas Activas</h6>
        <h2 class="fw-bold text-primary"><?= $total_ofertas ?></h2>
        <a href="ofertas.php" class="stretched-link"></a>
      </div>
    </div>

    <div class="col-md-6 col-lg-4">
      <div class="card card-dash text-center p-4 shadow-sm hover-elevate">
        <i class="bi bi-send-check-fill text-primary fs-1 mb-2"></i>
        <h6 class="text-muted mb-1">Mis Postulaciones</h6>
        <h2 class="fw-bold text-primary"><?= $total_postulaciones ?></h2>
        <a href="historial_postulaciones.php" class="stretched-link"></a>
      </div>
    </div>
  </div>

  <!-- OFERTAS RECIENTES -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold text-dark d-flex align-items-center gap-2">
      <i class="bi bi-pin-angle-fill text-primary fs-4"></i> Ofertas recientes
    </h4>
    <a href="ofertas.php" class="btn btn-outline-primary btn-sm hover-elevate-sm">
      Ver más →
    </a>
  </div>

  <?php if (count($ofertas_recientes) > 0): ?>
    <div class="list-group shadow-sm rounded-3 overflow-hidden">
      <?php foreach ($ofertas_recientes as $o): ?>
        <a href="detalle_oferta.php?id=<?= $o['id_oferta'] ?>"
           class="list-group-item list-group-item-action py-3 hover-elevate-sm">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="mb-1 fw-bold"><?= htmlspecialchars($o['titulo']) ?></h6>
              <small class="text-muted">
                <?= htmlspecialchars($o['razon_social']) ?> • <?= htmlspecialchars($o['modalidad']) ?>
              </small>
            </div>
            <small class="text-muted"><?= date('d/m/Y', strtotime($o['fecha_publicacion'])) ?></small>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="alert alert-info">No hay ofertas disponibles por ahora.</div>
  <?php endif; ?>
</div>

<?php include '../layout/footer.php'; ?>
