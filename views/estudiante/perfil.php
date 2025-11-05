<?php
include '../layout/header.php';
require_once __DIR__ . '/../../models/EstudianteDAO.php';
$dao = new EstudianteDAO();
$usuario_id = $_SESSION['id_usuario'] ?? null;
$est = $dao->obtenerPorUsuario($usuario_id);
?>

<div class="container mt-4">

  <?php if (!empty($_SESSION['success_msg'])): ?>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
      <div id="toastPerfil" class="toast align-items-center text-bg-success border-0" role="alert">
        <div class="d-flex">
          <div class="toast-body">
            <?= $_SESSION['success_msg']; ?>
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      </div>
    </div>
    <?php unset($_SESSION['success_msg']); ?>
  <?php endif; ?>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-primary m-0">Mi Perfil</h3>
    <a href="dashboard.php" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left-circle"></i> Volver
    </a>
  </div>

  <div class="card p-4">
    <form action="../../controllers/estudianteControlador.php" method="POST" enctype="multipart/form-data">
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

          <?php if (!empty($est->cv_url)): ?>
            <div class="d-flex align-items-center gap-2">
              <a href="/bolsatrabajouniversitario<?= $est->cv_url ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-file-earmark-pdf"></i> Ver CV actual
              </a>
              <button type="button" class="btn btn-sm btn-outline-secondary"
                onclick="document.getElementById('cvInput').classList.remove('d-none'); this.classList.add('d-none');">
                <i class="bi bi-upload"></i> Reemplazar CV
              </button>
            </div>

            <input type="file" name="cv" id="cvInput" accept="application/pdf" class="form-control mt-2 d-none">

          <?php else: ?>
            <input type="file" name="cv" accept="application/pdf" class="form-control">
            <div class="alert alert-warning mt-2 p-2">
              ⚠ Aún no has subido tu CV. (Opcional)
            </div>
          <?php endif; ?>

          <?php if (!empty($_SESSION['error_cv'])): ?>
            <div class="alert alert-danger p-2">
              <?= $_SESSION['error_cv']; ?>
              <?php unset($_SESSION['error_cv']); ?>
            </div>
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

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const toastEl = document.getElementById('toastPerfil');
    if (toastEl) {
      new bootstrap.Toast(toastEl, {
        delay: 3000
      }).show();
    }
  });
</script>

<?php include '../layout/footer.php'; ?>