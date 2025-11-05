<?php
require_once __DIR__ . '/../../config/auth.php';
verificarRol('empresa');  

include '../layout/header.php';
require_once __DIR__ . '/../models/OfertaDAO.php';

$dao = new OfertaDAO();
$id = $_GET['id'] ?? null;
$oferta = $dao->obtenerPorId($id);
?>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
  body {
    background: #f5f7fa;
    font-family: "Segoe UI", sans-serif;
  }

  .main-container {
    max-width: 900px;
    margin: 3rem auto;
    padding: 1rem;
  }

  .page-header {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    color: white;
    box-shadow: 0 4px 15px rgba(30,60,114,0.2);
  }

  .page-header h1 {
    font-size: 1.6rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
  }

  .page-header p {
    font-size: 0.95rem;
    opacity: 0.9;
    margin: 0;
  }

  .edit-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 2rem;
    border-top: 4px solid #1e88e5;
  }

  .edit-card label {
    font-weight: 600;
    color: #495057;
  }

  .btn-save {
    background: #1e88e5;
    color: white;
    font-weight: 600;
    border: none;
    padding: 0.75rem;
    border-radius: 8px;
    transition: all 0.3s;
  }

  .btn-save:hover {
    background: #1565c0;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(30,136,229,0.3);
  }

  .btn-cancel {
    background: #e9ecef;
    color: #495057;
    font-weight: 600;
    border: none;
    padding: 0.75rem;
    border-radius: 8px;
    transition: all 0.3s;
  }

  .btn-cancel:hover {
    background: #dee2e6;
    color: #1e3c72;
    transform: translateY(-2px);
  }

  /* Modo oscuro */
  body.modo-oscuro {
    background-color: #3c4043;
    color: #e8eaed;
  }

  body.modo-oscuro .page-header {
    background: linear-gradient(135deg, #202124, #3c4043);
    color: #e8eaed;
    box-shadow: none;
  }

  body.modo-oscuro .edit-card {
    background: #3c4043;
    color: #e8eaed;
    border-top: 4px solid #8ab4f8;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  }

  body.modo-oscuro .form-control,
  body.modo-oscuro .form-select {
    background-color: #3c4043;
    color: #e8eaed;
    border: 1px solid #5f6368;
  }

  body.modo-oscuro .btn-save {
    background: #8ab4f8;
    color: #202124;
  }

  body.modo-oscuro .btn-cancel {
    background: #5f6368;
    color: #fff;
  }
</style>

<!-- ✅ Botón modo oscuro -->
<button id="btnModoOscuro" class="btn-modo-oscuro" onclick="toggleModoOscuro()" title="Cambiar tema">
  <svg id="iconoSol" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
    <path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/>
  </svg>
  <svg id="iconoLuna" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" style="display:none;">
    <path d="M6 .278a.768.768 0 0 1 .08.858A7.208 7.208 0 0 0 5.202 4.6C5.202 8.62 8.48 11.877 12.52 11.877c.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"/>
  </svg>
</button>

<!-- Contenido principal -->
<div class="main-container">
  <div class="page-header">
    <h1><i class="bi bi-pencil-square"></i> Editar Oferta</h1>
    <p>Actualiza la información de tu oferta publicada</p>
  </div>

  <?php if (!$oferta): ?>
    <div class="alert alert-danger shadow-sm rounded-3">
      <i class="bi bi-exclamation-triangle me-2"></i> Oferta no encontrada.
    </div>
  <?php else: ?>
    <div class="edit-card">
      <form action="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/ofertaControlador.php" method="POST">
        <input type="hidden" name="action" value="actualizar">
        <input type="hidden" name="id_oferta" value="<?= $oferta['id_oferta'] ?>">

        <div class="mb-3">
          <label class="form-label">Título</label>
          <input type="text" name="titulo" class="form-control" required value="<?= htmlspecialchars($oferta['titulo']) ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" rows="5" class="form-control" required><?= htmlspecialchars($oferta['descripcion']) ?></textarea>
        </div>

        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">Tipo</label>
            <select name="tipo" class="form-select">
              <option value="prácticas" <?= $oferta['tipo']=='prácticas'?'selected':'' ?>>Prácticas</option>
              <option value="part-time" <?= $oferta['tipo']=='part-time'?'selected':'' ?>>Part-time</option>
              <option value="full-time" <?= $oferta['tipo']=='full-time'?'selected':'' ?>>Full-time</option>
            </select>
          </div>

          <div class="col-md-4 mb-3">
            <label class="form-label">Modalidad</label>
            <select name="modalidad" class="form-select">
              <option value="presencial" <?= $oferta['modalidad']=='presencial'?'selected':'' ?>>Presencial</option>
              <option value="remoto" <?= $oferta['modalidad']=='remoto'?'selected':'' ?>>Remoto</option>
              <option value="mixto" <?= $oferta['modalidad']=='mixto'?'selected':'' ?>>Mixto</option>
            </select>
          </div>

          <div class="col-md-4 mb-3">
            <label class="form-label">Salario Referencial (S/)</label>
            <input type="number" name="salario_referencial" step="0.01" class="form-control" value="<?= $oferta['salario_referencial'] ?>">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Fecha de Cierre</label>
          <input type="date" name="fecha_cierre" class="form-control" value="<?= $oferta['fecha_cierre'] ?>">
        </div>

        <div class="d-flex justify-content-between mt-4">
          <a href="mis_ofertas.php" class="btn-cancel">
            <i class="bi bi-arrow-left-circle"></i> Volver
          </a>
          <button type="submit" class="btn-save">
            <i class="bi bi-check-circle"></i> Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  <?php endif; ?>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const modoOscuro = localStorage.getItem('modoOscuro') === 'true';
    if (modoOscuro) {
      document.body.classList.add('modo-oscuro');
      document.getElementById('iconoSol').style.display = 'none';
      document.getElementById('iconoLuna').style.display = 'block';
    }
  });

  function toggleModoOscuro() {
    const body = document.body;
    const iconoSol = document.getElementById('iconoSol');
    const iconoLuna = document.getElementById('iconoLuna');

    body.classList.toggle('modo-oscuro');

    if (body.classList.contains('modo-oscuro')) {
      iconoSol.style.display = 'none';
      iconoLuna.style.display = 'block';
      localStorage.setItem('modoOscuro', 'true');
    } else {
      iconoSol.style.display = 'block';
      iconoLuna.style.display = 'none';
      localStorage.setItem('modoOscuro', 'false');
    }
  }
</script>

<?php include '../layout/footer.php'; ?>
