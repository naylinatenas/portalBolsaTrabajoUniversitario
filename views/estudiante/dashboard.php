<?php
require_once __DIR__ . '/../../config/auth.php';
verificarRol('estudiante');

include '../layout/header.php';
?>

<link rel="stylesheet" href="../css/estudiante.css">

<?php
require_once __DIR__ . '/../../models/EstudianteDAO.php';
require_once __DIR__ . '/../../models/PostulacionDAO.php';
require_once __DIR__ . '/../../models/OfertaDAO.php';

$estDAO = new EstudianteDAO();
$postDAO = new PostulacionDAO();
$ofertaDAO = new OfertaDAO();

$usuario_id = $_SESSION['id_usuario'] ?? null;
$estudiante = $estDAO->obtenerPorUsuario($usuario_id);

// Manejo optimizado por si no se encuentra el estudiante
$total_postulaciones = 0;
if ($estudiante) {
  $total_postulaciones = count($postDAO->listarPorEstudiante($estudiante->id_estudiante));
}

$ofertas_activas = $ofertaDAO->listarActivas();
$total_ofertas = count($ofertas_activas);
$ofertas_recientes = array_slice($ofertas_activas, 0, 5);
?>

<div class="container mt-4 mb-5">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <h2 class="fw-bold m-0"> Bienvenido, <?= htmlspecialchars($_SESSION['nombre'] ?? 'Estudiante') ?> 👋
    </h2>

    <a href="perfil.php" class="btn btn-outline-primary d-flex align-items-center gap-2 shadow-sm">
      <i class="bi bi-person-badge-fill fs-5"></i> Ver Perfil
    </a>
  </div>

  <div class="row g-4 mb-5 justify-content-center">
    <div class="col-md-6 col-lg-4">
      <div class="card card-dash text-center p-4">
        <i class="bi bi-briefcase-fill mb-2"></i>
        <h6 class="mb-1">Ofertas Activas</h6>
        <h2 class="fw-bold"><?= $total_ofertas ?></h2>

        <span class="card-cta mt-2">
          Ver ofertas <i class="bi bi-arrow-right-short"></i>
        </span>
        <a href="ofertas.php" class="stretched-link"></a>
      </div>
    </div>

    <div class="col-md-6 col-lg-4">
      <div class="card card-dash text-center p-4">
        <i class="bi bi-send-check-fill mb-2"></i>
        <h6 class="mb-1">Mis Postulaciones</h6>
        <h2 class="fw-bold"><?= $total_postulaciones ?></h2>

        <span class="card-cta mt-2">
          Ver postulaciones <i class="bi bi-arrow-right-short"></i>
        </span>
        <a href="historial_postulaciones.php" class="stretched-link"></a>
      </div>
    </div>
  </div>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold d-flex align-items-center gap-2">
      <i class="bi bi-pin-angle-fill text-primary fs-4"></i> Ofertas recientes
    </h4>
    <a href="ofertas.php" class="btn btn-outline-primary btn-sm">
      Ver más →
    </a>
  </div>

  <div class="card-list-container">
    <?php if (count($ofertas_recientes) > 0): ?>

      <div class="list-group list-group-flush">

        <?php foreach ($ofertas_recientes as $o): ?>
          <?php $ya_postulo = $estudiante ? $postDAO->existe($o['id_oferta'], $estudiante->id_estudiante) : false; ?>

          <a href="detalle_oferta.php?id=<?= $o['id_oferta'] ?>"
            class="list-group-item list-group-item-action">

            <div class="d-flex justify-content-between align-items-start">
              <h6 class="text-primary"><?= htmlspecialchars($o['titulo']) ?></h6>
              <small class="text-muted text-nowrap ms-3"><?= date('d/m/Y', strtotime($o['fecha_publicacion'])) ?></small>
            </div>

            <div class="d-flex justify-content-between align-items-center">
              <small>
                <?= htmlspecialchars($o['razon_social']) ?> • <?= ucfirst($o['modalidad']) ?>
              </small>

              <?php if ($ya_postulo): ?>
                <span class="badge bg-success">Postulado</span>
              <?php endif; ?>
            </div>

          </a>
        <?php endforeach; ?>

      </div>
    <?php else: ?>
      <div class="alert alert-info m-4 text-center">
        <i class="bi bi-info-circle me-2"></i>
        No hay ofertas disponibles por ahora.
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include '../layout/footer.php'; ?>