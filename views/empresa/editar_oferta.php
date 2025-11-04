<?php
include '../layout/header.php';
require_once __DIR__ . '/../../models/OfertaDAO.php';
$dao = new OfertaDAO();
$id = $_GET['id'] ?? null;
$oferta = $dao->obtenerPorId($id);
?>

<div class="container mt-4">
  <h3 class="fw-bold text-primary mb-4">Editar Oferta</h3>

  <?php if (!$oferta): ?>
    <div class="alert alert-danger">Oferta no encontrada.</div>
  <?php else: ?>
    <div class="card p-4">
      <form action="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/ofertaControlador.php" method="POST">
        <input type="hidden" name="action" value="actualizar">
        <input type="hidden" name="id_oferta" value="<?= $oferta['id_oferta'] ?>">

        <div class="mb-3">
          <label class="form-label">Título</label>
          <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($oferta['titulo']) ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" rows="5" class="form-control"><?= htmlspecialchars($oferta['descripcion']) ?></textarea>
        </div>

        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">Tipo</label>
            <select name="tipo" class="form-select">
              <option value="prácticas" <?= $oferta['tipo']=='prácticas'?'selected':'' ?>>Prácticas</option>
              <option value="part-time" <?= $oferta['tipo']=='part-time'?'selected':'' ?>>Part-time</option>
              <option value="full-time" <?= $oferta['tipo']=='full-time'?'selected':'' ?>>Full-time</option>
            </select>
          </div>

          <div class="col-md-4 mb-3">
            <label class="form-label">Modalidad</label>
            <select name="modalidad" class="form-select">
              <option value="presencial" <?= $oferta['modalidad']=='presencial'?'selected':'' ?>>Presencial</option>
              <option value="remoto" <?= $oferta['modalidad']=='remoto'?'selected':'' ?>>Remoto</option>
              <option value="mixto" <?= $oferta['modalidad']=='mixto'?'selected':'' ?>>Mixto</option>
            </select>
          </div>

          <div class="col-md-4 mb-3">
            <label class="form-label">Salario Referencial</label>
            <input type="number" name="salario_referencial" class="form-control" step="0.01" value="<?= $oferta['salario_referencial'] ?>">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Fecha de Cierre</label>
          <input type="date" name="fecha_cierre" class="form-control" value="<?= $oferta['fecha_cierre'] ?>">
        </div>

        <button type="submit" class="btn btn-primario w-100">Guardar Cambios</button>
      </form>
    </div>
  <?php endif; ?>
</div>

<?php include '../layout/footer.php'; ?>
