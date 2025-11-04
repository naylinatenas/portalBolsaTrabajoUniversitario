<?php
include '../layout/header.php';
require_once __DIR__ . '/../../models/PostulacionDAO.php';
$dao = new PostulacionDAO();
$postulaciones = $dao->listarConDetalles();
?>

<div class="container mt-4">
  <h3 class="fw-bold text-primary mb-4">Reporte de Postulaciones</h3>

  <div class="card p-3">
    <table class="table table-hover align-middle">
      <thead>
        <tr>
          <th>Estudiante</th>
          <th>Oferta</th>
          <th>Empresa</th>
          <th>Fecha</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($postulaciones as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['nombre_estudiante']) ?></td>
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
</div>

<?php include '../layout/footer.php'; ?>
