<?php
require_once __DIR__ . '/../../config/auth.php';
verificarRol('empresa');

include '../layout/header.php';
require_once __DIR__ . '/../../models/OfertaDAO.php';
require_once __DIR__ . '/../../models/EmpresaDAO.php';

$dao = new OfertaDAO();
$empresaDAO = new EmpresaDAO();

$usuario_id = $_SESSION['id_usuario'] ?? null;
$empresa = $empresaDAO->obtenerPorUsuario($usuario_id);

if ($empresa && isset($empresa->id_empresa)) {
  $ofertas = $dao->listarPorEmpresa($empresa->id_empresa);
} else {
  $ofertas = [];
}
?>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
/* ========================= */
/* ESTILO GENERAL             */
/* ========================= */
body {
  background: #f4f6fb;
  font-family: "Segoe UI", sans-serif;
  color: #333;
  transition: background 0.3s, color 0.3s;
}

/* ENCABEZADO AZUL */
.header-section {
  background: linear-gradient(135deg, #1e3c72, #2a5298);
  border-radius: 16px;
  padding: 2rem 2.5rem;
  color: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 4px 15px rgba(30, 60, 114, 0.25);
}
.header-section .info h2 {
  font-weight: 700;
  margin-bottom: 0.3rem;
}
.header-section .info p {
  font-size: 0.95rem;
  opacity: 0.9;
}
.header-section .actions button,
.header-section .actions a {
  background: rgba(255, 255, 255, 0.12);
  color: #fff;
  border: 1px solid rgba(255,255,255,0.25);
  border-radius: 10px;
  padding: 8px 16px;
  font-weight: 600;
  margin-left: 10px;
  transition: 0.3s;
  text-decoration: none;
}
.header-section .actions a:hover {
  background: white;
  color: #1e3c72;
}

/* CARD PRINCIPAL */
.card {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
  padding: 1.5rem;
}
.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: transparent;
  border-bottom: 2px solid #e9ecef;
  padding-bottom: 1rem;
  margin-bottom: 1rem;
}
.card-header h4 {
  font-weight: 700;
  color: #1e3c72;
}

/* TABLA */
.table thead {
  background: #f8f9fa;
  color: #1e3c72;
  font-weight: 600;
}
.table th, .table td {
  vertical-align: middle;
}

/* BOTONES DE ACCIÓN */
.btn-primario {
  background: #1e88e5;
  color: white;
  border-radius: 10px;
  border: none;
  font-weight: 600;
  transition: all 0.3s;
}
.btn-primario:hover {
  background: #1565c0;
}

.btn-outline-primary { color: #1565c0; border-color: #1565c0; }
.btn-outline-info { color: #0288d1; border-color: #0288d1; }
.btn-outline-warning { color: #fbc02d; border-color: #fbc02d; }
.btn-outline-danger { color: #d32f2f; border-color: #d32f2f; }
.btn-outline-success { color: #388e3c; border-color: #388e3c; }

.btn-outline-primary:hover { background: #1565c0; color: #fff; }
.btn-outline-info:hover { background: #0288d1; color: #fff; }
.btn-outline-warning:hover { background: #fbc02d; color: #fff; }
.btn-outline-danger:hover { background: #d32f2f; color: #fff; }
.btn-outline-success:hover { background: #388e3c; color: #fff; }

/* MODO OSCURO */
body.modo-oscuro {
  background-color: #121212;
  color: #e8eaed;
}
body.modo-oscuro .header-section {
  background: linear-gradient(135deg, #202124, #3c4043);
  box-shadow: none;
}
body.modo-oscuro .card {
  background: #1f1f1f;
  color: #e8eaed;
}
body.modo-oscuro .table thead {
  background: #2a2a2a;
  color: #e8eaed;
}

/* BOTÓN MODO OSCURO */
#btnModoOscuro {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: #1e88e5;
  color: white;
  border: none;
  border-radius: 50%;
  width: 52px;
  height: 52px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
  box-shadow: 0 4px 8px rgba(0,0,0,0.3);
  cursor: pointer;
  transition: all 0.3s;
  z-index: 1050;
}
#btnModoOscuro:hover {
  background: #1565c0;
  transform: scale(1.05);
}
</style>

<div class="container">
  <!-- ENCABEZADO AZUL -->
  <div class="header-section">
    <div class="info">
      <h2><i class="bi bi-briefcase-fill"></i> Mis Ofertas Publicadas</h2>
      <p>Administra tus ofertas laborales activas, pausadas o cerradas</p>
    </div>
    <div class="actions">
      <a href="dashboard.php"><i class="bi bi-arrow-left"></i> Volver</a>
    </div>
  </div>

  <!-- LISTADO DE OFERTAS -->
  <div class="card">
    <div class="card-header">
      <h4><i class="bi bi-list-task"></i> Listado de Ofertas</h4>
      <a href="nueva_oferta.php" class="btn btn-primario">
        <i class="bi bi-plus-circle"></i> Nueva Oferta
      </a>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
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
          <?php if (empty($ofertas)): ?>
            <tr>
              <td colspan="5" class="text-center text-muted py-4">
                <i class="bi bi-inbox fs-4"></i><br>No tienes ofertas registradas aún.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($ofertas as $o): ?>
              <?php $estado = strtolower($o['estado_oferta']); ?>
              <tr>
                <td><?= htmlspecialchars($o['titulo']) ?></td>
                <td><?= ucfirst($o['tipo']) ?></td>
                <td><?= ucfirst($o['modalidad']) ?></td>
                <td>
                  <span class="badge bg-<?= $estado=='activa'?'success':($estado=='pausada'?'warning':'danger') ?>">
                    <?= ucfirst($estado) ?>
                  </span>
                </td>
                <td class="text-center">
                  <!-- BOTÓN EDITAR (abre el modal) -->
                  <button 
                    class="btn btn-outline-primary btn-sm me-2 btnEditar"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEditar"
                    data-id="<?= $o['id_oferta'] ?>"
                    data-titulo="<?= htmlspecialchars($o['titulo']) ?>"
                    data-tipo="<?= $o['tipo'] ?>"
                    data-modalidad="<?= $o['modalidad'] ?>"
                    data-salario="<?= $o['salario_referencial'] ?>"
                    data-estado="<?= $o['estado_oferta'] ?>">
                    <i class="bi bi-pencil-square"></i>
                  </button>

                  <a href="postulaciones_recibidas.php?id=<?= $o['id_oferta'] ?>" class="btn btn-outline-info btn-sm me-2" title="Ver Postulaciones">
                    <i class="bi bi-people"></i>
                  </a>
                  <?php if ($estado == 'activa'): ?>
                    <a href="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/ofertaControlador.php?action=pausar&id=<?= $o['id_oferta'] ?>" 
                      class="btn btn-outline-warning btn-sm me-2" title="Pausar">
                      <i class="bi bi-pause-circle"></i>
                    </a>
                  <?php elseif ($estado == 'pausada'): ?>
                    <a href="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/ofertaControlador.php?action=reanudar&id=<?= $o['id_oferta'] ?>" 
                      class="btn btn-outline-success btn-sm me-2" title="Reanudar">
                      <i class="bi bi-play-circle"></i>
                    </a>
                  <?php endif; ?>
                  <a href="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/ofertaControlador.php?action=eliminar&id=<?= $o['id_oferta'] ?>" 
                    onclick="return confirm('¿Deseas eliminar esta oferta permanentemente?')" 
                    class="btn btn-outline-danger btn-sm" title="Eliminar">
                    <i class="bi bi-x-circle"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- MODAL EDITAR OFERTA -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/ofertaControlador.php?action=editar_oferta" method="POST">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalEditarLabel"><i class="bi bi-pencil-square"></i> Editar Oferta</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id_oferta" id="editar_id_oferta">
          <input type="hidden" name="empresa_id" value="<?= $empresa->id_empresa ?? '' ?>">

          <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" class="form-control" name="titulo" id="editar_titulo" required>
          </div>

          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">Tipo</label>
              <select name="tipo" id="editar_tipo" class="form-select">
                <option value="prácticas">Prácticas</option>
                <option value="part-time">Part-time</option>
                <option value="full-time">Full-time</option>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Modalidad</label>
              <select name="modalidad" id="editar_modalidad" class="form-select">
                <option value="presencial">Presencial</option>
                <option value="remoto">Remoto</option>
                <option value="mixto">Mixto</option>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Salario Referencial</label>
              <input type="number" step="0.01" class="form-control" name="salario_referencial" id="editar_salario">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Estado</label>
            <select name="estado_oferta" id="editar_estado" class="form-select">
              <option value="activa">Activa</option>
              <option value="pausada">Pausada</option>
              <option value="cerrada">Cerrada</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- 🌙 BOTÓN MODO OSCURO -->
<button id="btnModoOscuro"><i id="iconoModo" class="bi bi-moon-fill"></i></button>

<script>
// 🌙 Modo oscuro persistente
const btnModo = document.getElementById('btnModoOscuro');
const iconoModo = document.getElementById('iconoModo');
const body = document.body;

if (localStorage.getItem('modoOscuro') === 'true') {
  body.classList.add('modo-oscuro');
  iconoModo.classList.replace('bi-moon-fill', 'bi-sun-fill');
}

btnModo.addEventListener('click', () => {
  body.classList.toggle('modo-oscuro');
  const activo = body.classList.contains('modo-oscuro');
  iconoModo.classList.replace(
    activo ? 'bi-moon-fill' : 'bi-sun-fill',
    activo ? 'bi-sun-fill' : 'bi-moon-fill'
  );
  localStorage.setItem('modoOscuro', activo);
});

// 🛠️ Llenar datos del modal
document.querySelectorAll('.btnEditar').forEach(btn => {
  btn.addEventListener('click', () => {
    document.getElementById('editar_id_oferta').value = btn.dataset.id;
    document.getElementById('editar_titulo').value = btn.dataset.titulo;
    document.getElementById('editar_tipo').value = btn.dataset.tipo;
    document.getElementById('editar_modalidad').value = btn.dataset.modalidad;
    document.getElementById('editar_salario').value = btn.dataset.salario;
    document.getElementById('editar_estado').value = btn.dataset.estado;
  });
});
</script>

<?php include '../layout/footer.php'; ?>
