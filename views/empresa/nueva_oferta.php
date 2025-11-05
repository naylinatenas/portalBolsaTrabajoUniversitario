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

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<!-- Estilos principales -->
<link href="../css/styles.css" rel="stylesheet">

<div class="container mt-4 position-relative">
  <h3 class="fw-bold text-primary mb-4">Mis Ofertas Publicadas</h3>

  <!-- Botón modo oscuro -->
  <button id="btnModoOscuro" class="btn btn-modo-oscuro position-fixed bottom-0 end-0 m-4 rounded-circle shadow">
    <i id="iconoModo" class="bi bi-moon-fill fs-4"></i>
  </button>

  <!-- Botón Nueva Oferta (abre modal dinámico) -->
  <button id="btnAbrirNuevaOferta" class="btn btn-primario mb-3">
    <i class="bi bi-plus-circle"></i> Nueva Oferta
  </button>

  <!-- Tabla de ofertas -->
  <div class="card p-3">
    <table class="table table-hover align-middle" id="tablaOfertas">
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
              <a href="editar_oferta.php?id=<?= $o['id_oferta'] ?>" class="btn btn-outline-primary btn-sm me-2" title="Editar">
                <i class="bi bi-pencil-square"></i>
              </a>
              <a href="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/ofertaControlador.php?action=eliminar&id=<?= $o['id_oferta'] ?>" 
                 class="btn btn-outline-danger btn-sm" title="Eliminar">
                <i class="bi bi-trash"></i>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ======================== -->
<!-- MODAL CONTENEDOR -->
<!-- ======================== -->
<div class="modal fade" id="modalNuevaOferta" tabindex="-1" aria-labelledby="tituloModalOferta" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold text-primary" id="tituloModalOferta">Publicar Nueva Oferta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body" id="contenidoModalOferta">
        <!-- Aquí se cargará nueva_oferta.php dinámicamente -->
        <div class="text-center my-4 text-muted">
          <div class="spinner-border text-primary" role="status"></div>
          <p class="mt-2">Cargando formulario...</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ======================== -->
<!-- JS: MODO OSCURO + MODAL AJAX -->
<!-- ======================== -->
<script>
const btnModo = document.getElementById('btnModoOscuro');
const iconoModo = document.getElementById('iconoModo');
const body = document.body;

// === MODO OSCURO ===
if (localStorage.getItem('modoOscuro') === 'true') {
  body.classList.add('modo-oscuro');
  iconoModo.classList.replace('bi-moon-fill', 'bi-sun-fill');
}

btnModo.addEventListener('click', () => {
  body.classList.toggle('modo-oscuro');
  const activo = body.classList.contains('modo-oscuro');
  iconoModo.classList.replace(activo ? 'bi-moon-fill' : 'bi-sun-fill', activo ? 'bi-sun-fill' : 'bi-moon-fill');
  localStorage.setItem('modoOscuro', activo);
});

// === ABRIR MODAL Y CARGAR nueva_oferta.php ===
document.getElementById('btnAbrirNuevaOferta').addEventListener('click', async () => {
  const modal = new bootstrap.Modal(document.getElementById('modalNuevaOferta'));
  const contenedor = document.getElementById('contenidoModalOferta');

  // Mostrar loading temporal
  contenedor.innerHTML = `
    <div class="text-center my-4 text-muted">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="mt-2">Cargando formulario...</p>
    </div>
  `;

  // Cargar el contenido del archivo nueva_oferta.php
  try {
    const response = await fetch('nueva_oferta.php');
    const html = await response.text();
    contenedor.innerHTML = html;
  } catch (error) {
    contenedor.innerHTML = `<p class="text-danger text-center my-4">Error al cargar el formulario.</p>`;
  }

  modal.show();
});
</script>

<?php include '../layout/footer.php'; ?>
