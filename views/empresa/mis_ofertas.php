<?php
include '../layout/header.php';
require_once __DIR__ . '/../../models/OfertaDAO.php';
require_once __DIR__ . '/../../models/EmpresaDAO.php';

$dao = new OfertaDAO();
$empresaDAO = new EmpresaDAO();

$usuario_id = $_SESSION['id_usuario'];
$empresa = $empresaDAO->obtenerPorUsuario($usuario_id);
$ofertas = $dao->listarPorEmpresa($empresa->id_empresa);
?>

<div class="container mt-4">
  <h3 class="fw-bold text-primary mb-4">Mis Ofertas Publicadas</h3>
  <a href="nueva_oferta.php" class="btn btn-primario mb-3"><i class="bi bi-plus-circle"></i> Nueva Oferta</a>

  <div class="card p-3">
    <table class="table table-hover align-middle">
      <thead>
        <tr>
          <th>Título</th>
          <th>Tipo</th>
          <th>Modalidad</th>
          <th>Estado</th>
          <th class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($ofertas as $o): ?>
          <tr>
            <td><?= htmlspecialchars($o['titulo']) ?></td>
            <td><?= ucfirst($o['tipo']) ?></td>
            <td><?= ucfirst($o['modalidad']) ?></td>
            <td>
              <span class="badge bg-<?= $o['estado_oferta']=='activa'?'success':($o['estado_oferta']=='cerrada'?'danger':'warning') ?>">
                <?= ucfirst($o['estado_oferta']) ?>
              </span>
            </td>
            <td class="text-center">
              <a href="editar_oferta.php?id=<?= $o['id_oferta'] ?>" class="btn btn-outline-primary btn-sm me-2"><i class="bi bi-pencil"></i></a>
              <a href="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/ofertaControlador.php?action=eliminar&id=<?= $o['id_oferta'] ?>" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../layout/footer.php'; ?>
