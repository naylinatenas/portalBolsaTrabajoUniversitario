<?php
include '../layout/header.php';
require_once __DIR__ . '/../../models/OfertaDAO.php';
$dao = new OfertaDAO();
$ofertas = $dao->listarActivas();
?>

<div class="container mt-4">
  <h3 class="fw-bold text-primary mb-4">Ofertas Laborales Disponibles</h3>

  <div class="row g-4">
    <?php foreach ($ofertas as $o): ?>
      <div class="col-md-4">
        <div class="card h-100 p-3 shadow-sm">
          <div class="card-body">
            <h5 class="text-primary fw-bold"><?= htmlspecialchars($o['titulo']) ?></h5>
            <p class="mb-1"><strong>Empresa:</strong> <?= htmlspecialchars($o['razon_social']) ?></p>
            <p class="mb-1"><strong>Tipo:</strong> <?= ucfirst($o['tipo']) ?> | <strong>Modalidad:</strong> <?= ucfirst($o['modalidad']) ?></p>
            <p class="text-muted small"><?= substr($o['descripcion'], 0, 100) ?>...</p>
          </div>
          <div class="card-footer bg-transparent border-0 text-center">
            <a href="detalle_oferta.php?id=<?= $o['id_oferta'] ?>" class="btn btn-primario btn-sm w-75">Ver Detalle</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php include '../layout/footer.php'; ?>
