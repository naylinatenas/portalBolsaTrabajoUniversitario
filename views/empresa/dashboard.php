<?php
include '../layout/header.php';
require_once __DIR__ . '/../../models/OfertaDAO.php';
require_once __DIR__ . '/../../models/PostulacionDAO.php';
require_once __DIR__ . '/../../models/EmpresaDAO.php';

$ofertaDAO = new OfertaDAO();
$postDAO = new PostulacionDAO();
$empDAO = new EmpresaDAO();

$usuario_id = $_SESSION['id_usuario'];
$empresa = $empDAO->obtenerPorUsuario($usuario_id);

$total_ofertas = count($ofertaDAO->listarPorEmpresa($empresa->id_empresa));
$total_postulaciones = $postDAO->contarPorEmpresa($empresa->id_empresa);
?>

<div class="container mt-4">
  <h2 class="fw-bold text-primary mb-4">Panel de Empresa</h2>
  <div class="row g-4">
    <div class="col-md-6">
      <div class="card text-center p-4">
        <i class="bi bi-briefcase text-primary fs-1 mb-2"></i>
        <h5>Mis Ofertas Publicadas</h5>
        <h3 class="text-primary"><?= $total_ofertas ?></h3>
        <a href="mis_ofertas.php" class="btn btn-primario mt-2">Ver Ofertas</a>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card text-center p-4">
        <i class="bi bi-envelope-open text-primary fs-1 mb-2"></i>
        <h5>Postulaciones Recibidas</h5>
        <h3 class="text-primary"><?= $total_postulaciones ?></h3>
        <a href="postulaciones_recibidas.php" class="btn btn-primario mt-2">Ver Postulaciones</a>
      </div>
    </div>
  </div>
</div>

<?php include '../layout/footer.php'; ?>
