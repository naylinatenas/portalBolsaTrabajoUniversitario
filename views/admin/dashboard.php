<?php
require_once __DIR__ . '/../../config/auth.php';
verificarRol('admin');

include '../layout/header.php';
require_once __DIR__ . '/../../models/EmpresaDAO.php';
require_once __DIR__ . '/../../models/OfertaDAO.php';
require_once __DIR__ . '/../../models/PostulacionDAO.php';

$empDAO = new EmpresaDAO();
$ofDAO = new OfertaDAO();
$postDAO = new PostulacionDAO();

$total_empresas = count($empDAO->listar());
$total_ofertas = count($ofDAO->listar());
$total_postulaciones = count($postDAO->listar());
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Administración | Bolsa de Trabajo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="../../css/styles.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #f3f6fa 0%, #e9eef7 100%);
      min-height: 100vh;
      font-family: "Poppins", sans-serif;
    }

    .card-dashboard {
      border: none;
      border-radius: 20px;
      background: #fff;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card-dashboard:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
    }

    .icon-circle {
      width: 70px;
      height: 70px;
      background-color: #e7f0ff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 15px;
    }

    .btn-primario {
      background-color: #0d47a1;
      color: #fff;
      border: none;
      border-radius: 10px;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }

    .btn-primario:hover {
      background-color: #1565c0;
    }

    h2.fw-bold.text-primary {
      color: #0d47a1 !important;
    }
  </style>
</head>

<body>
  <div class="container py-5">
    <h2 class="fw-bold text-primary text-center mb-5">
      <i class="bi bi-speedometer2"></i> Panel de Administración
    </h2>

    <div class="row g-4 justify-content-center">
      <!-- Empresas -->
      <div class="col-md-4">
        <div class="card-dashboard text-center p-4">
          <div class="icon-circle mb-3">
            <i class="bi bi-buildings fs-2 text-primary"></i>
          </div>
          <h5 class="text-secondary mb-1">Empresas Registradas</h5>
          <h3 class="fw-bold text-primary mb-3"><?= $total_empresas ?></h3>
          <a href="empresas.php" class="btn btn-primario w-75">
            <i class="bi bi-eye"></i> Ver empresas
          </a>
        </div>
      </div>

      <!-- Ofertas -->
      <div class="col-md-4">
        <div class="card-dashboard text-center p-4">
          <div class="icon-circle mb-3">
            <i class="bi bi-briefcase fs-2 text-primary"></i>
          </div>
          <h5 class="text-secondary mb-1">Ofertas Publicadas</h5>
          <h3 class="fw-bold text-primary mb-3"><?= $total_ofertas ?></h3>
          <a href="ofertas.php" class="btn btn-primario w-75">
            <i class="bi bi-briefcase-fill"></i> Ver ofertas
          </a>
        </div>
      </div>

      <!-- Postulaciones -->
      <div class="col-md-4">
        <div class="card-dashboard text-center p-4">
          <div class="icon-circle mb-3">
            <i class="bi bi-person-lines-fill fs-2 text-primary"></i>
          </div>
          <h5 class="text-secondary mb-1">Postulaciones Totales</h5>
          <h3 class="fw-bold text-primary mb-3"><?= $total_postulaciones ?></h3>
          <a href="reportes.php" class="btn btn-primario w-75">
            <i class="bi bi-bar-chart"></i> Ver reportes
          </a>
        </div>
      </div>
    </div>
  </div>

  <?php include '../layout/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
