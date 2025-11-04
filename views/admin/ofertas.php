<?php
include '../layout/header.php';
require_once __DIR__ . '/../../models/OfertaDAO.php';
$dao = new OfertaDAO();
$ofertas = $dao->listar();
?>

<div class="container mt-4">
  <h3 class="fw-bold text-primary mb-4">Ofertas Laborales Publicadas</h3>

  <div class="card p-3">
    <table class="table table-hover align-middle">
      <thead>
        <tr>
          <th>Título</th>
          <th>Empresa</th>
          <th>Tipo</th>
          <th>Modalidad</th>
          <th>Salario</th>
          <th>Estado</th>
          <th class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($ofertas as $o): ?>
          <tr>
            <td><?= htmlspecialchars($o['titulo']) ?></td>
            <td><?= htmlspecialchars($o['razon_social']) ?></td>
            <td><?= ucfirst($o['tipo']) ?></td>
            <td><?= ucfirst($o['modalidad']) ?></td>
            <td>S/<?= number_format($o['salario_referencial'], 2) ?></td>
            <td>
              <span class="badge bg-<?= $o['estado_oferta']=='activa'?'success':($o['estado_oferta']=='cerrada'?'danger':'warning') ?>">
                <?= ucfirst($o['estado_oferta']) ?>
              </span>
            </td>
            <td class="text-center">
              <a href="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/adminControlador.php?action=eliminar_oferta&id=<?= $o['id_oferta'] ?>" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash"></i> Eliminar
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../layout/footer.php'; ?>
