<?php
// vista/empresa/mis_ofertas.php
require_once __DIR__ . '/../../vista/layout/header.php';
require_once __DIR__ . '/../../modelo/EmpresaDAO.php';
require_once __DIR__ . '/../../modelo/OfertaDAO.php';

$empresaDAO = new EmpresaDAO();
$ofertaDAO = new OfertaDAO();

$usuario_id = $_SESSION['id_usuario'];
$empresas = $empresaDAO->listar();
$empresa = null;
foreach($empresas as $e) if ($e['usuario_id'] == $usuario_id) { $empresa = $e; break; }

if (!$empresa) {
    echo '<div class="alert alert-warning">Tu empresa está pendiente de aprobación o no existe.</div>';
    require_once __DIR__ . '/../../vista/layout/footer.php';
    exit;
}
$ofertas = $ofertaDAO->listarPorEmpresa($empresa['id_empresa']);
?>
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <h5>Mis ofertas</h5>
    <a href="nueva_oferta.php" class="btn btn-sm btn-primary">Nueva oferta</a>
  </div>
  <div class="card-body">
    <table class="table">
      <thead><tr><th>Título</th><th>Modalidad</th><th>Estado</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php foreach($ofertas as $o): ?>
        <tr>
          <td><?= htmlentities($o['titulo']) ?></td>
          <td><?= $o['modalidad'] ?></td>
          <td><?= $o['estado_oferta'] ?></td>
          <td>
            <a class="btn btn-sm btn-secondary" href="editar_oferta.php?id=<?= $o['id_oferta'] ?>">Editar</a>
            <a class="btn btn-sm btn-danger" href="/controlador/empresaControlador.php?action=eliminar_oferta&id=<?= $o['id_oferta'] ?>" onclick="return confirm('Eliminar?')">Eliminar</a>
            <a class="btn btn-sm btn-info" href="postulaciones_recibidas.php?oferta=<?= $o['id_oferta'] ?>">Ver postulaciones</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/../../vista/layout/footer.php'; ?>
