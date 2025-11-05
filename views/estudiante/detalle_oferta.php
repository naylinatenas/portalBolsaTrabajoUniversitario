<?php
include '../layout/header.php';
require_once __DIR__ . '/../../models/OfertaDAO.php';

// 1. AÑADIMOS EL ENLACE A NUESTRO CSS EXISTENTE
echo '<link rel="stylesheet" href="../css/estudiante.css">';

$dao = new OfertaDAO();
$id = $_GET['id'] ?? null;
$oferta = $dao->obtenerPorId($id);

if (!$oferta): ?>
  <div class="alert alert-danger mt-4 container">Oferta no encontrada.</div>
<?php include '../layout/footer.php'; exit; endif; ?>

<div class="container mt-4 mb-5">

  <div class="card offer-card p-4 p-md-5"> 
    
    <h3 class="fw-bold text-primary mb-3"><?= htmlspecialchars($oferta['titulo']) ?></h3>
    
    <div class="offer-info mb-3">
      <p><strong>Empresa:</strong> <?= htmlspecialchars($oferta['razon_social']) ?></p>
      <p><strong>Tipo:</strong> <?= ucfirst($oferta['tipo']) ?> | <strong>Modalidad:</strong> <?= ucfirst($oferta['modalidad']) ?></p>
      <p><strong>Salario Referencial:</strong> S/<?= number_format($oferta['salario_referencial'], 2) ?></p>
      <p>
        <strong>Fecha de Cierre:</strong> 
        <?= htmlspecialchars(date('d/m/Y', strtotime($oferta['fecha_cierre']))) ?>
      </p>
    </div>

    <hr class="offer-divider">

    <div class="offer-description my-3">
      <?= nl2br(htmlspecialchars($oferta['descripcion'])) ?>
    </div>
    
    <hr class="offer-divider">

    <div class="text-center mt-4">
      <form action="../../controllers/postulacionControlador.php" method="POST" class="d-inline-block">
        <input type="hidden" name="action" value="postular">
        <input type="hidden" name="oferta_id" value="<?= $oferta['id_oferta'] ?>">
        
        <button type="submit" class="btn btn-primary btn-lg shadow-sm">
          <i class="bi bi-send-fill me-2"></i>Postular a esta oferta
        </button>
      </form>
    </div>
    
  </div>
</div>

<?php include '../layout/footer.php'; ?>