<?php
// Si el usuario ya inició sesión, redirigir según su rol
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['rol'])) {
    switch ($_SESSION['rol']) {
        case 'admin':
            header('Location: views/admin/dashboard.php');
            exit;
        case 'empresa':
            header('Location: views/empresa/dashboard.php');
            exit;
        case 'estudiante':
            header('Location: views/estudiante/dashboard.php');
            exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Bolsa de Trabajo Universitario</title>

  <!-- Bootstrap y estilos -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="css/styles.css" rel="stylesheet">
</head>

<body>

<!-- HERO: cabecera principal -->
<section class="text-center text-white" style="background: linear-gradient(90deg, #0d47a1, #1565c0);">
  <div class="container py-5">
    <i class="bi bi-mortarboard-fill fs-1 mb-3"></i>
    <h1 class="fw-bold">Bolsa de Trabajo Universitario</h1>
    <p class="lead">Conecta el talento universitario con las mejores empresas del país.</p>
    <a href="login.php" class="btn btn-light btn-lg shadow-sm mt-2">
      <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
    </a>
  </div>
</section>

<!-- SECCIÓN DE INFORMACIÓN -->
<section class="container my-5">
  <div class="row g-4 text-center">
    <div class="col-md-4">
      <div class="card p-4 shadow-sm">
        <i class="bi bi-person-workspace fs-1 text-primary mb-2"></i>
        <h5 class="fw-bold">Empresas</h5>
        <p class="text-muted">Publica tus ofertas laborales y descubre nuevos talentos universitarios.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-4 shadow-sm">
        <i class="bi bi-mortarboard fs-1 text-primary mb-2"></i>
        <h5 class="fw-bold">Estudiantes</h5>
        <p class="text-muted">Explora oportunidades laborales y postula fácilmente desde tu cuenta.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-4 shadow-sm">
        <i class="bi bi-gear-wide-connected fs-1 text-primary mb-2"></i>
        <h5 class="fw-bold">Administradores</h5>
        <p class="text-muted">Gestiona usuarios, empresas y ofertas dentro del portal.</p>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="py-3 text-center shadow-sm bg-white">
  <p class="mb-1 fw-semibold text-primary">© <?= date('Y') ?> Bolsa de Trabajo Universitario</p>
  <p class="text-muted mb-0" style="font-size: 0.9rem;">Desarrollado por Ingeniería Web</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
