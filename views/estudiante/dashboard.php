<?php

require_once __DIR__ . '/../../config/auth.php';  // ← protección de sesión
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
  <h2 class="fw-bold text-primary mb-4">Bienvenido, <?= $_SESSION['nombre'] ?? 'Estudiante' ?> 👋</h2>

  <div class="row g-4">
    <div class="col-md-6 col-lg-4">
      <div class="card text-center p-4 border-0">
        <i class="bi bi-briefcase text-primary fs-1 mb-2"></i>
        <h5>Ofertas Disponibles</h5>
        <h3 class="fw-bold text-primary"><?= $total_ofertas ?></h3>
        <a href="ofertas.php" class="btn btn-primario mt-2 w-75 mx-auto">Ver Ofertas</a>
      </div>
    </div>

    <div class="col-md-6 col-lg-4">
      <div class="card text-center p-4 border-0">
        <i class="bi bi-send-check text-primary fs-1 mb-2"></i>
        <h5>Mis Postulaciones</h5>
        <h3 class="fw-bold text-primary"><?= $total_postulaciones ?></h3>
        <a href="historial_postulaciones.php" class="btn btn-primario mt-2 w-75 mx-auto">Ver Historial</a>
      </div>
    </div>

    <div class="col-md-6 col-lg-4">
      <div class="card text-center p-4 border-0">
        <i class="bi bi-person-badge text-primary fs-1 mb-2"></i>
        <h5>Mi Perfil</h5>
        <a href="perfil.php" class="btn btn-outline-primary mt-3 w-75 mx-auto">Ver Perfil</a>
      </div>
    </div>
  </div>

  <div class="mt-5">
    <h4 class="fw-bold mb-3">📌 Ofertas recientes</h4>

    <?php if (count($ofertas_recientes) > 0): ?>
      <div class="list-group shadow-sm">
        <?php foreach ($ofertas_recientes as $o): ?>
          <a href="detalle_oferta.php?id=<?= $o['id_oferta'] ?>" class="list-group-item list-group-item-action">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="mb-1 fw-bold"><?= htmlspecialchars($o['titulo']) ?></h6>
                <small class="text-muted">
                  <?= htmlspecialchars($o['razon_social']) ?> • <?= htmlspecialchars($o['modalidad']) ?>
                </small>
              </div>
              <small class="text-muted">
                <?= date('d/m/Y', strtotime($o['fecha_publicacion'])) ?>
              </small>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="alert alert-info">No hay ofertas disponibles por ahora.</div>
    <?php endif; ?>
  </div>

</div>

<?php include '../layout/footer.php'; ?>