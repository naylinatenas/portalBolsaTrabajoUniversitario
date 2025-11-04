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
$total_ofertas = count($ofertaDAO->listarActivas());
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
</div>

<?php include '../layout/footer.php'; ?>
