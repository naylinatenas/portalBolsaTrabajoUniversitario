<?php
include '../layout/header.php';
require_once __DIR__ . '/../../models/OfertaDAO.php';
$dao = new OfertaDAO();
$id = $_GET['id'] ?? null;
$oferta = $dao->obtenerPorId($id);

if (!$oferta): ?>
  <div class="alert alert-danger mt-4 container">Oferta no encontrada.</div>
<?php include '../layout/footer.php'; exit; endif; ?>

<div class="container mt-4">
  <div class="card p-4 shadow-sm">
    <h3 class="fw-bold text-primary mb-2"><?= htmlspecialchars($oferta['titulo']) ?></h3>
    <p><strong>Empresa:</strong> <?= htmlspecialchars($oferta['razon_social']) ?></p>
    <p><strong>Tipo:</strong> <?= ucfirst($oferta['tipo']) ?> | <strong>Modalidad:</strong> <?= ucfirst($oferta['modalidad']) ?></p>
    <p><strong>Salario Referencial:</strong> S/<?= number_format($oferta['salario_referencial'], 2) ?></p>
    <p><strong>Fecha de Cierre:</strong> <?= htmlspecialchars($oferta['fecha_cierre']) ?></p>
    <hr>
    <p class="mb-4"><?= nl2br(htmlspecialchars($oferta['descripcion'])) ?></p>

    <form action="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/postulacionControlador.php" method="POST">
      <input type="hidden" name="action" value="postular">
      <input type="hidden" name="oferta_id" value="<?= $oferta['id_oferta'] ?>">
      <button type="submit" class="btn btn-primario w-100">
        <i class="bi bi-send-fill"></i> Postular a esta oferta
      </button>
    </form>
  </div>
</div>

<?php include '../layout/footer.php'; ?>
