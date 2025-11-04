<?php
include '../layout/header.php';
require_once __DIR__ . '/../../models/PostulacionDAO.php';
require_once __DIR__ . '/../../models/EstudianteDAO.php';

$pdao = new PostulacionDAO();
$edao = new EstudianteDAO();

$usuario_id = $_SESSION['id_usuario'] ?? null;
$est = $edao->obtenerPorUsuario($usuario_id);
$postulaciones = $est ? $pdao->listarPorEstudiante($est->id_estudiante) : [];
?>

<div class="container mt-4">
  <h3 class="fw-bold text-primary mb-4">Historial de Postulaciones</h3>

  <?php if (empty($postulaciones)): ?>
    <div class="alert alert-info">Aún no has postulado a ninguna oferta.</div>
  <?php else: ?>
    <div class="card p-3">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>Oferta</th>
            <th>Empresa</th>
            <th>Fecha</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($postulaciones as $p): ?>
            <tr>
              <td><?= htmlspecialchars($p['titulo_oferta']) ?></td>
              <td><?= htmlspecialchars($p['razon_social']) ?></td>
              <td><?= date('d/m/Y', strtotime($p['fecha_postulacion'])) ?></td>
              <td>
                <span class="badge bg-<?= $p['estado_postulacion']=='aceptada'?'success':($p['estado_postulacion']=='rechazada'?'danger':'warning') ?>">
                  <?= ucfirst($p['estado_postulacion']) ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php include '../layout/footer.php'; ?>
