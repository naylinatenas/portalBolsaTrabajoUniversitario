<?php
include '../layout/header.php';
require_once __DIR__ . '/../../models/PostulacionDAO.php';
require_once __DIR__ . '/../../models/EmpresaDAO.php';

$postDAO = new PostulacionDAO();
$empresaDAO = new EmpresaDAO();

$usuario_id = $_SESSION['id_usuario'];
$empresa = $empresaDAO->obtenerPorUsuario($usuario_id);
$postulaciones = $postDAO->listarPorEmpresa($empresa->id_empresa);
?>
<link href="../css/styles.css" rel="stylesheet">
<div class="container mt-4">
  <h3 class="fw-bold text-primary mb-4">Postulaciones Recibidas</h3>

  <?php if (empty($postulaciones)): ?>
    <div class="alert alert-info">Aún no has recibido postulaciones.</div>
  <?php else: ?>
    <div class="row g-3">
      <?php foreach ($postulaciones as $p): ?>
        <div class="col-md-6 col-lg-4">
          <div class="card p-3 h-100">
            <h5 class="text-primary mb-1"><?= htmlspecialchars($p['nombre_estudiante']) ?></h5>
            <p class="mb-1"><strong>Oferta:</strong> <?= htmlspecialchars($p['titulo_oferta']) ?></p>
            <p class="text-muted small mb-2"><?= date('d/m/Y', strtotime($p['fecha_postulacion'])) ?></p>
            <span class="badge bg-<?= $p['estado_postulacion']=='aceptada'?'success':($p['estado_postulacion']=='rechazada'?'danger':'warning') ?>">
              <?= ucfirst($p['estado_postulacion']) ?>
            </span>
            <div class="mt-3">
              <form action="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/postulacionControlador.php" method="POST">
                <input type="hidden" name="id_postulacion" value="<?= $p['id_postulacion'] ?>">
                <div class="btn-group w-100">
                  <button name="action" value="aceptar" class="btn btn-success btn-sm"><i class="bi bi-check"></i> Aceptar</button>
                  <button name="action" value="rechazar" class="btn btn-danger btn-sm"><i class="bi bi-x"></i> Rechazar</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
        
<?php include '../layout/footer.php'; ?>

