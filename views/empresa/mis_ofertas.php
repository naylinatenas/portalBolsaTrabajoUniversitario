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
<link href="../css/styles.css" rel="stylesheet">

<style>
/* --- Corrección principal de z-index --- */
.modal-backdrop {
  z-index: 1040 !important;
}

.modal {
  z-index: 1050 !important;
}

.btn-modo-oscuro {
  z-index: 1030 !important; /* Debajo del modal */
}

/* --- Ajustes modo oscuro --- */
body.modo-oscuro .modal-content {
  background-color: #2c2c2c !important;
  color: #f5f5f5 !important;
  border-color: #444 !important;
}

body.modo-oscuro .modal-header,
body.modo-oscuro .modal-footer {
  background-color: #222 !important;
}

body.modo-oscuro input,
body.modo-oscuro textarea,
body.modo-oscuro select {
  background-color: #333 !important;
  color: #fff !important;
  border: 1px solid #555 !important;
}

footer {
  position: static; /* <- importante para que no bloquee el modal */
}
</style>

<div class="container mt-4 position-relative">
  <h3 class="fw-bold text-primary mb-4">Mis Ofertas Publicadas</h3>

  <!-- Botón modo oscuro -->
  <button id="btnModoOscuro" class="btn btn-modo-oscuro position-fixed bottom-0 end-0 m-4 rounded-circle shadow">
    <i id="iconoModo" class="bi bi-moon-fill fs-4"></i>
  </button>

  <!-- Botón Nueva Oferta -->
  <button class="btn btn-primario mb-3" data-bs-toggle="modal" data-bs-target="#modalNuevaOferta">
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
<!-- MODAL NUEVA OFERTA -->
<!-- ======================== -->
<div class="modal fade" id="modalNuevaOferta" tabindex="-1" aria-labelledby="tituloModalOferta" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold text-primary" id="tituloModalOferta">Publicar Nueva Oferta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <form id="formNuevaOferta" action="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/ofertaControlador.php?action=guardar" method="POST">
          
          <div class="mb-3">
            <label for="titulo" class="form-label">Título de la Oferta</label>
            <input type="text" name="titulo" id="titulo" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control" rows="4" required></textarea>
          </div>

          <div class="row">
            <div class="col-md-4 mb-3">
              <label for="tipo" class="form-label">Tipo</label>
              <select name="tipo" id="tipo" class="form-select" required>
                <option value="prácticas">Prácticas</option>
                <option value="empleo">Empleo</option>
              </select>
            </div>

            <div class="col-md-4 mb-3">
              <label for="modalidad" class="form-label">Modalidad</label>
              <select name="modalidad" id="modalidad" class="form-select" required>
                <option value="presencial">Presencial</option>
                <option value="mixto">Mixto</option>
                <option value="remoto">Remoto</option>
              </select>
            </div>

            <div class="col-md-4 mb-3">
              <label for="salario" class="form-label">Salario Referencial</label>
              <input type="text" name="salario" id="salario" class="form-control">
            </div>
          </div>

          <div class="mb-3">
            <label for="fecha_cierre" class="form-label">Fecha de Cierre</label>
            <input type="date" name="fecha_cierre" id="fecha_cierre" class="form-control" required>
          </div>

          <div class="text-end">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primario">Publicar Oferta</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<!-- ======================== -->
<!-- JS: MODO OSCURO + AJAX -->
<!-- ======================== -->
<script>
const btnModo = document.getElementById('btnModoOscuro');
const iconoModo = document.getElementById('iconoModo');
const body = document.body;

// Cargar modo oscuro
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

// Envío del formulario por AJAX
document.getElementById('formNuevaOferta').addEventListener('submit', async function(e) {
  e.preventDefault();

  const form = e.target;
  const data = new FormData(form);

  try {
    const res = await fetch(form.action, { method: 'POST', body: data });
    const result = await res.text();

    // Cierra el modal correctamente
    const modalEl = document.getElementById('modalNuevaOferta');
    const modal = bootstrap.Modal.getInstance(modalEl);
    modal.hide();

    form.reset();
    location.reload();
  } catch (err) {
    console.error(err);
    alert('Hubo un error al guardar la oferta.');
  }
});
</script>

<?php include '../layout/footer.php'; ?>
