<?php
require_once __DIR__ . '/../../config/auth.php';
verificarRol('estudiante');

include '../layout/header.php';
require_once __DIR__ . '/../../models/OfertaDAO.php';
require_once __DIR__ . '/../../models/PostulacionDAO.php';
require_once __DIR__ . '/../../models/EstudianteDAO.php';

$ofertaDAO = new OfertaDAO();
$postDAO = new PostulacionDAO();
$estDAO = new EstudianteDAO();

// Obtener estudiante logueado
$usuario_id = $_SESSION['id_usuario'];
$estudiante = $estDAO->obtenerPorUsuario($usuario_id);
$id_est = $estudiante->id_estudiante;

// Filtros GET
$f_tipo = $_GET['tipo'] ?? '';
$f_modalidad = $_GET['modalidad'] ?? '';
$f_empresa = $_GET['empresa'] ?? '';

$ofertas = $ofertaDAO->listarActivasFiltradas($f_tipo, $f_modalidad, $f_empresa);
?>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">

    <h3 class="fw-bold text-primary m-0">Ofertas Laborales Disponibles</h3>
    <a href="dashboard.php" class="text-decoration-none text-secondary small d-inline-flex align-items-center hover-elevate-sm">
      <i class="bi bi-arrow-left me-1"></i> Volver al dashboard
    </a>


  </div>


  <!-- FILTROS -->
  <form method="GET" class="row g-3 mb-4">
    <div class="col-md-4">
      <select name="tipo" class="form-select">
        <option value="">-- Tipo de oferta --</option>
        <option value="practicas" <?= $f_tipo === 'practicas' ? 'selected' : '' ?>>Prácticas</option>
        <option value="part-time" <?= $f_tipo === 'part-time' ? 'selected' : '' ?>>Part-Time</option>
        <option value="full-time" <?= $f_tipo === 'full-time' ? 'selected' : '' ?>>Full-Time</option>
      </select>
    </div>

    <div class="col-md-4">
      <select name="modalidad" class="form-select">
        <option value="">-- Modalidad --</option>
        <option value="presencial" <?= $f_modalidad === 'presencial' ? 'selected' : '' ?>>Presencial</option>
        <option value="remoto" <?= $f_modalidad === 'remoto' ? 'selected' : '' ?>>Remoto</option>
        <option value="mixto" <?= $f_modalidad === 'mixto' ? 'selected' : '' ?>>Mixto</option>
      </select>
    </div>

    <div class="col-md-4 d-flex gap-2">
      <input type="text" name="empresa" class="form-control" placeholder="Buscar por empresa..." value="<?= htmlspecialchars($f_empresa) ?>">
      <button class="btn btn-primary"><i class="bi bi-search"></i></button>
      <a href="ofertas.php" class="btn btn-outline-secondary">
        <i class="bi bi-x-circle"></i>
      </a>
    </div>
  </form>

  <p class="text-muted mb-3">
    Resultados encontrados: <strong><?= count($ofertas) ?></strong>
  </p>


  <!-- TARJETAS -->
  <div class="row g-4">
    <?php foreach ($ofertas as $o):
      $ya_postulo = $postDAO->existe($o['id_oferta'], $id_est);
    ?>
      <div class="col-md-4">
        <div class="card h-100 p-3 shadow-sm hover-elevate">
          <div class="card-body">
            <h5 class="text-primary fw-bold"><?= htmlspecialchars($o['titulo']) ?></h5>
            <p class="mb-1"><strong>Empresa:</strong> <?= htmlspecialchars($o['razon_social']) ?></p>
            <p class="mb-1"><strong>Tipo:</strong> <?= ucfirst($o['tipo']) ?> | <strong>Modalidad:</strong> <?= ucfirst($o['modalidad']) ?></p>
            <p class="text-muted small"><?= substr($o['descripcion'], 0, 100) ?>...</p>
          </div>

          <div class="card-footer bg-transparent border-0 text-center">

            <?php if ($ya_postulo): ?>
              <span class="badge bg-secondary px-3 py-2">Ya postulaste</span>

            <?php else: ?>
              <form action="../../controllers/postulacionControlador.php" method="POST" class="form-postular">
                <input type="hidden" name="action" value="postular">
                <input type="hidden" name="oferta_id" value="<?= $o['id_oferta'] ?>">
                <button type="submit" class="btn btn-primario btn-sm w-75 btn-postular">
                  <i class="bi bi-send-fill"></i> Postular
                </button>
              </form>
            <?php endif; ?>

          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <?php if (count($ofertas) === 0): ?>
    <div class="text-center py-5">
      <i class="bi bi-search text-secondary fs-1 mb-2"></i>
      <h5 class="text-muted mb-1">No se encontraron resultados</h5>
      <p class="text-muted small">Prueba cambiando los filtros o restableciendo la búsqueda.</p>
      <a href="ofertas.php" class="btn btn-outline-primary btn-sm mt-2">
        Restablecer filtros
      </a>
    </div>
  <?php endif; ?>

</div>

<?php if (isset($_GET['ok'])): ?>
  <script>
    Swal.fire({
      icon: 'success',
      title: 'Postulación enviada ✅',
      text: 'Tu postulación fue registrada correctamente.',
      confirmButtonColor: '#0d6efd'
    });
  </script>
<?php endif; ?>


<script>
  document.querySelectorAll('.form-postular').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      Swal.fire({
        title: '¿Confirmar postulación?',
        text: 'Se enviará tu postulación a la empresa.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, postular'
      }).then((result) => {
        if (result.isConfirmed) form.submit();
      });
    });
  });
</script>


<?php include '../layout/footer.php'; ?>