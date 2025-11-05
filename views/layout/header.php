<?php
// vista/layout/header.php
if (session_status() === PHP_SESSION_NONE) session_start();

$theme = $_COOKIE['tema_portal'] ?? 'light';
$bodyClass = ($theme === 'dark') ? 'bg-dark text-white' : '';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Portal Bolsa de Trabajo</title>
  <!-- Bootstrap CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/public/css/style.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="<?= $bodyClass ?>">
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
  <div class="container">
    <a class="navbar-brand text-primary" href="../../index.php">Bolsa Universitaria</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <?php if(isset($_SESSION['id_usuario'])): ?>
          <li class="nav-item"><span class="nav-link">Hola, <?= htmlentities($_SESSION['nombre']) ?></span></li>
          <a class="nav-link" href="../../controllers/logout.php">Cerrar sesión</a>
          <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/views/login.php">Iniciar sesión</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-4">
