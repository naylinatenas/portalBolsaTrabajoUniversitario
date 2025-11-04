<?php
include '../layout/header.php';
require_once __DIR__ . '/../../models/EmpresaDAO.php';
require_once __DIR__ . '/../../models/OfertaDAO.php';
require_once __DIR__ . '/../../models/PostulacionDAO.php';

$empDAO = new EmpresaDAO();
$ofDAO = new OfertaDAO();
$postDAO = new PostulacionDAO();

$total_empresas = count($empDAO->listar());
$total_ofertas = count($ofDAO->listar());
$total_postulaciones = count($postDAO->listar());
?>

<div class="container mt-4">
  <h2 class="fw-bold text-primary mb-4">Panel de Administración</h2>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card text-center p-4 border-0">
        <div class="mb-2">
          <i class="bi bi-buildings fs-1 text-primary"></i>
        </div>
        <h5>Empresas Registradas</h5>
        <h3 class="fw-bold text-primary"><?= $total_empresas ?></h3>
        <a href="empresas.php" class="btn btn-primario mt-2 w-75 mx-auto">Ver empresas</a>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card text-center p-4 border-0">
        <div class="mb-2">
          <i class="bi bi-briefcase fs-1 text-primary"></i>
        </div>
        <h5>Ofertas Publicadas</h5>
        <h3 class="fw-bold text-primary"><?= $total_ofertas ?></h3>
        <a href="ofertas.php" class="btn btn-primario mt-2 w-75 mx-auto">Ver ofertas</a>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card text-center p-4 border-0">
        <div class="mb-2">
          <i class="bi bi-person-lines-fill fs-1 text-primary"></i>
        </div>
        <h5>Postulaciones Totales</h5>
        <h3 class="fw-bold text-primary"><?= $total_postulaciones ?></h3>
        <a href="reportes.php" class="btn btn-primario mt-2 w-75 mx-auto">Ver reportes</a>
      </div>
    </div>
  </div>
</div>

<?php include '../layout/footer.php'; ?>
