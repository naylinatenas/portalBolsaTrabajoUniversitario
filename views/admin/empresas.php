<?php
// vista/admin/empresas.php
require_once __DIR__ . '/../../modelo/EmpresaDAO.php';
require_once __DIR__ . '/../../vista/layout/header.php';
$dao = new EmpresaDAO();
$empresas = $dao->listar();
?>
<div class="card">
  <div class="card-header">
    <h5 class="card-title">Empresas registradas</h5>
  </div>
  <div class="card-body">
    <table class="table table-striped">
      <thead><tr><th>ID</th><th>Razón</th><th>Contacto</th><th>Estado</th><th>Acciones</th></tr></thead>
      <tbody>
      <?php foreach($empresas as $e): ?>
        <tr>
          <td><?= $e['id_empresa'] ?></td>
          <td><?= htmlentities($e['razon_social']) ?></td>
          <td><?= htmlentities($e['correo_contacto'] ?? $e['correo']) ?></td>
          <td><?= $e['estado'] ?></td>
          <td>
            <?php if($e['estado'] === 'pendiente'): ?>
              <a class="btn btn-sm btn-success" href="/controlador/adminControlador.php?action=aprobar&id=<?= $e['id_empresa'] ?>">Aprobar</a>
              <a class="btn btn-sm btn-warning" href="/controlador/adminControlador.php?action=rechazar&id=<?= $e['id_empresa'] ?>">Rechazar</a>
            <?php endif; ?>
            <a class="btn btn-sm btn-danger" href="/controlador/adminControlador.php?action=eliminar&id=<?= $e['id_empresa'] ?>" onclick="return confirm('Eliminar?')">Eliminar</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/../../vista/layout/footer.php'; ?>
