<?php
// vista/admin/dashboard.php
require_once __DIR__ . '/../../vista/layout/header.php';
require_once __DIR__ . '/../../modelo/OfertaDAO.php';
$ofDao = new OfertaDAO();
$ofertas = $ofDao->listarActivas();
?>
<div class="row">
  <div class="col-md-4">
    <div class="card text-center p-3">
      <h5>Ofertas activas</h5>
      <h2 class="text-primary"><?= count($ofertas) ?></h2>
    </div>
  </div>
  <div class="col-md-8">
    <div class="card p-3">
      <h5>Últimas ofertas</h5>
      <ul class="list-group">
        <?php foreach(array_slice($ofertas,0,5) as $o): ?>
          <li class="list-group-item"><?= htmlentities($o['titulo']) ?> - <?= htmlentities($o['razon_social']) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../../vista/layout/footer.php'; ?>
