<?php
include '../layout/header.php';
require_once __DIR__ . '/../../models/EstudianteDAO.php';
$dao = new EstudianteDAO();
$usuario_id = $_SESSION['id_usuario'] ?? null;
$est = $dao->obtenerPorUsuario($usuario_id);
?>

<div class="container mt-4">
  <h3 class="fw-bold text-primary mb-4">Mi Perfil</h3>

  <div class="card p-4">
    <form action="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/estudianteControlador.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="action" value="guardar_perfil">

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Código de Estudiante</label>
          <input type="text" name="codigo_estudiante" class="form-control" value="<?= $est->codigo_estudiante ?? '' ?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Carrera</label>
          <input type="text" name="carrera" class="form-control" value="<?= $est->carrera ?? '' ?>">
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Ciclo</label>
          <input type="text" name="ciclo" class="form-control" value="<?= $est->ciclo ?? '' ?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Subir CV (PDF)</label>
          <input type="file" name="cv" accept="application/pdf" class="form-control">
          <?php if (!empty($est->cv_url)): ?>
            <small class="text-muted">CV actual: <a href="<?= $est->cv_url ?>" target="_blank">Ver</a></small>
          <?php endif; ?>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Resumen del Perfil</label>
        <textarea name="resumen_perfil" class="form-control" rows="4"><?= $est->resumen_perfil ?? '' ?></textarea>
      </div>

      <button type="submit" class="btn btn-primario w-100">Guardar Cambios</button>
    </form>
  </div>
</div>

<?php include '../layout/footer.php'; ?>
