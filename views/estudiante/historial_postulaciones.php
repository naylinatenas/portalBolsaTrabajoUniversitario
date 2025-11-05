<?php
include '../layout/header.php';
require_once __DIR__ . '/../../models/PostulacionDAO.php';
require_once __DIR__ . '/../../models/EstudianteDAO.php';

$pdao = new PostulacionDAO();
$edao = new EstudianteDAO();

$usuario_id = $_SESSION['id_usuario'] ?? null;
$est = $edao->obtenerPorUsuario($usuario_id);
$postulaciones = $est ? $pdao->listarPorEstudiante($est->id_estudiante) : [];

if (!$est) {
  $postulaciones = [];
  $total_postulaciones = 0;
} else {
  // PAGINACIÓN
  $por_pagina = 10;
  $pagina = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
  $offset = ($pagina - 1) * $por_pagina;

  $total_postulaciones = $pdao->contarPorEstudiante($est->id_estudiante);
  $total_paginas = ceil($total_postulaciones / $por_pagina);

  $postulaciones = $pdao->listarPaginado($est->id_estudiante, $por_pagina, $offset);
}

?>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-primary m-0">Historial de Postulaciones</h3>
    <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">
      ← Volver
    </a>
  </div>

  <?php if ($total_postulaciones == 0): ?>
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
            <th>Comentario</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($postulaciones as $p): ?>
            <tr>
              <td><?= htmlspecialchars($p['titulo_oferta']) ?></td>
              <td><?= htmlspecialchars($p['razon_social']) ?></td>
              <td><?= date('d/m/Y', strtotime($p['fecha_postulacion'])) ?></td>
              <td>
                <span class="badge bg-<?= $p['estado_postulacion'] == 'aceptada' ? 'success' : ($p['estado_postulacion'] == 'rechazada' ? 'danger' : 'warning') ?>">
                  <?= ucfirst($p['estado_postulacion']) ?>
                </span>
              </td>
              <td class="text-center">
                <?php if (!empty($p['comentario_empresa'])): ?>
                  <button class="btn btn-sm btn-outline-secondary"
                    data-bs-toggle="modal"
                    data-bs-target="#comentarioModal<?= $p['id_postulacion'] ?>">
                    <i class="bi bi-chat-left-text"></i>
                  </button>
                <?php else: ?>
                  —
                <?php endif; ?>
              </td>
              <td class="text-center">
                <?php if (!in_array($p['estado_postulacion'], ['aceptada', 'rechazada'])): ?>
                  <button class="btn btn-sm btn-outline-danger"
                    onclick="anularPostulacion(<?= $p['id_postulacion'] ?>)">
                    <i class="bi bi-x-circle"></i>
                  </button>
                <?php else: ?>
                  —
                <?php endif; ?>
              </td>


            </tr>
            <?php if (!empty($p['comentario_empresa'])): ?>
              <!-- Modal Comentario -->
              <div class="modal fade" id="comentarioModal<?= $p['id_postulacion'] ?>" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">
                        Comentario sobre: <?= htmlspecialchars($p['titulo_oferta']) ?>
                      </h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <p><?= nl2br(htmlspecialchars($p['comentario_empresa'])) ?></p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>

          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- PAGINACIÓN -->
    <nav class="mt-3">
      <ul class="pagination justify-content-center">

        <!-- Prev -->
        <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
          <a class="page-link" href="?page=<?= $pagina - 1 ?>">&lt;</a>
        </li>

        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
          <?php if ($i == 1 || $i == $total_paginas || abs($i - $pagina) <= 2): ?>
            <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
              <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
          <?php elseif ($i == 2 || $i == $total_paginas - 1): ?>
            <li class="page-item disabled"><span class="page-link">...</span></li>
          <?php endif; ?>
        <?php endfor; ?>

        <!-- Next -->
        <li class="page-item <?= $pagina >= $total_paginas ? 'disabled' : '' ?>">
          <a class="page-link" href="?page=<?= $pagina + 1 ?>">&gt;</a>
        </li>

      </ul>
    </nav>

  <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  function anularPostulacion(id) {
    Swal.fire({
      title: "¿Anular postulación?",
      text: "Esta acción eliminará tu postulación y no podrás recuperarla.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, anular",
      cancelButtonText: "Cancelar"
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = "../../controllers/postulacionControlador.php?action=anular&id=" + id;
      }
    });
  }
</script>


<?php include '../layout/footer.php'; ?>