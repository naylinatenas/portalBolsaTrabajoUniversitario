<?php
include '../layout/header.php';
require_once __DIR__ . '/../../models/EmpresaDAO.php';
$dao = new EmpresaDAO();
$empresas = $dao->listar();
?>

<div class="container mt-4">
  <h3 class="fw-bold text-primary mb-4">Gestión de Empresas</h3>

  <div class="card p-3">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr>
          <th>ID</th>
          <th>Razón Social</th>
          <th>Correo</th>
          <th>Estado</th>
          <th class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($empresas as $e): ?>
          <tr>
            <td><?= $e['id_empresa'] ?></td>
            <td><?= htmlspecialchars($e['razon_social']) ?></td>
            <td><?= htmlspecialchars($e['correo_contacto']) ?></td>
            <td>
              <span class="badge bg-<?= $e['estado']=='aprobada'?'success':($e['estado']=='rechazada'?'danger':'warning') ?>">
                <?= ucfirst($e['estado']) ?>
              </span>
            </td>
            <td class="text-center">
              <?php if ($e['estado'] === 'pendiente'): ?>
                <a href="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/adminControlador.php?action=aprobar&id=<?= $e['id_empresa'] ?>" class="btn btn-success btn-sm me-1">
                  <i class="bi bi-check-circle"></i> Aprobar
                </a>
                <a href="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/adminControlador.php?action=rechazar&id=<?= $e['id_empresa'] ?>" class="btn btn-danger btn-sm me-1">
                  <i class="bi bi-x-circle"></i> Rechazar
                </a>
              <?php endif; ?>
              <a href="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/adminControlador.php?action=eliminar_empresa&id=<?= $e['id_empresa'] ?>" class="btn btn-outline-danger btn-sm">
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
