<?php
require_once __DIR__ . '/../../config/auth.php';
verificarRol('empresa');

require_once __DIR__ . '/../../models/PostulacionDAO.php';
require_once __DIR__ . '/../../models/EmpresaDAO.php';
include '../layout/header.php';

$postDAO = new PostulacionDAO();
$empresaDAO = new EmpresaDAO();

$usuario_id = $_SESSION['id_usuario'];
$empresa = $empresaDAO->obtenerPorUsuario($usuario_id);

if (!$empresa) {
    echo '<div class="container mt-5"><div class="alert alert-danger">No se encontró tu perfil de empresa. Por favor, contacta al administrador.</div></div>';
    include '../layout/footer.php';
    exit;
}

$postulaciones = $postDAO->listarPorEmpresa($empresa->id_empresa);
?>

<!-- ================== CSS INTERNO ================== -->
<style>
body {
  background: #f5f7fb;
  font-family: "Segoe UI", sans-serif;
  color: #333;
  transition: background 0.3s ease, color 0.3s ease;
}

/* ===== ENCABEZADO ===== */
.dashboard-header {
  background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
  color: white;
  text-align: center;
  border-radius: 14px;
  padding: 2rem 1rem;
  box-shadow: 0 4px 15px rgba(30,60,114,0.2);
  margin-bottom: 2.5rem;
}
.dashboard-header h1 {
  font-weight: 700;
  margin-bottom: .5rem;
}
.dashboard-header p {
  color: #e0e7ff;
}

/* ===== CARDS ===== */
.card {
  background: white;
  border-radius: 14px;
  border: 2px solid transparent;
  box-shadow: 0 3px 8px rgba(0,0,0,0.05);
  transition: all 0.3s ease;
}
.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 18px rgba(0,0,0,0.12);
}

/* ===== BOTONES ===== */
.btn {
  border-radius: 30px;
  font-weight: 600;
  padding: 0.5rem 1rem;
  transition: all 0.3s;
}
.btn-outline-primary:hover {
  background: #1e3c72;
  color: white;
}

/* ===== MODO OSCURO ===== */
body.bg-dark {
  background: #121212 !important;
  color: #e8eaed !important;
}
body.bg-dark .card {
  background: #1f1f1f;
  border-color: #2c2c2c;
  color: #e8eaed;
}
body.bg-dark .btn-outline-primary {
  color: #8ab4f8;
  border-color: #8ab4f8;
}
body.bg-dark .btn-outline-primary:hover {
  background: #8ab4f8;
  color: #202124;
}
body.bg-dark .alert-info {
  background: #1e1e1e;
  color: #cfcfcf;
}
</style>

<!-- ================== CONTENIDO ================== -->
<div class="container mt-4">
  <!-- Encabezado -->
  <div class="dashboard-header">
    <h1><i class="bi bi-people-fill"></i> Postulaciones Recibidas</h1>
    <p>Consulta los estudiantes que han aplicado a tus ofertas laborales.</p>
  </div>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <a href="mis_ofertas.php" class="btn btn-outline-primary">
      <i class="bi bi-arrow-left"></i> Volver a Mis Ofertas
    </a>
  </div>

  <!-- Mensajes de sesión -->
  <?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?? 'info' ?> alert-dismissible fade show shadow-sm" role="alert">
      <?= htmlspecialchars($_SESSION['mensaje']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php 
      unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
    ?>
  <?php endif; ?>

  <!-- Contenido principal -->
  <?php if (empty($postulaciones)): ?>
    <div class="alert alert-info text-center py-5 shadow-sm">
      <i class="bi bi-inbox display-5 d-block mb-3 text-primary"></i>
      <h5 class="fw-semibold">Aún no has recibido postulaciones</h5>
      <p class="text-muted mb-0">Las postulaciones aparecerán aquí cuando los estudiantes se inscriban en tus ofertas.</p>
    </div>
  <?php else: ?>
    <div class="row g-4">
      <?php foreach ($postulaciones as $p): ?>
        <div class="col-md-6 col-lg-4">
          <div class="card h-100 p-3">
            
            <!-- Cabecera -->
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h5 class="text-primary fw-semibold mb-0">
                <?= htmlspecialchars($p['nombre_estudiante']) ?>
              </h5>
              <span class="badge bg-<?= 
                $p['estado_postulacion']=='aceptada' ? 'success' : 
                ($p['estado_postulacion']=='rechazada' ? 'danger' : 'warning text-dark') 
              ?>">
                <?= ucfirst($p['estado_postulacion']) ?>
              </span>
            </div>

            <!-- Detalles -->
            <p class="mb-1 text-secondary">
              <i class="bi bi-briefcase"></i>
              <strong>Oferta:</strong> <?= htmlspecialchars($p['titulo_oferta']) ?>
            </p>
            <p class="mb-1 text-secondary">
              <i class="bi bi-envelope"></i>
              <?= htmlspecialchars($p['correo_estudiante']) ?>
            </p>
            <p class="small text-muted mb-3">
              <i class="bi bi-calendar"></i>
              <?= date('d/m/Y H:i', strtotime($p['fecha_postulacion'])) ?>
            </p>

            <!-- Botón Ver CV -->
            <?php if (!empty($p['cv_path'])): ?>
              <a href="/PORTALBOLSATRABAJOUNIVERSITARIO/<?= htmlspecialchars($p['cv_path']) ?>" 
                 class="btn btn-outline-primary btn-sm w-100 mb-2" 
                 target="_blank">
                <i class="bi bi-file-earmark-pdf"></i> Ver CV
              </a>
            <?php endif; ?>

            <!-- Acciones (solo si pendiente) -->
            <?php if ($p['estado_postulacion'] === 'pendiente'): ?>
              <form action="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/postulacionControlador.php" method="POST">
                <input type="hidden" name="id_postulacion" value="<?= $p['id_postulacion'] ?>">
                <div class="d-grid gap-2">
                  <button name="action" value="aceptar" class="btn btn-success btn-sm">
                    <i class="bi bi-check-circle"></i> Aceptar
                  </button>
                  <button name="action" value="rechazar" class="btn btn-danger btn-sm">
                    <i class="bi bi-x-circle"></i> Rechazar
                  </button>
                </div>
              </form>
            <?php endif; ?>

            <!-- Comentario -->
            <?php if (!empty($p['comentario_empresa'])): ?>
              <div class="mt-3 p-2 bg-light rounded small text-muted border">
                <strong>Comentario:</strong><br>
                <?= nl2br(htmlspecialchars($p['comentario_empresa'])) ?>
              </div>
            <?php endif; ?>

          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php include '../layout/footer.php'; ?>
