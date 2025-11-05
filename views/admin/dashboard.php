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

$empresas = $empDAO->listar();
$ofertas = $ofDAO->listar();
$postulaciones = $postDAO->listarPostulacionesLaborales();

// Estadísticas principales
$total_empresas = count($empresas);
$ofertas_activas = count(array_filter($ofertas, function($o) {
    return $o['estado_oferta'] === 'activa';
}));

$empresas_pendientes = count(array_filter($empresas, function($e) {
    return $e['estado'] === 'pendiente';
}));

$fecha_limite = date('Y-m-d', strtotime('-7 days'));
$postulaciones_semana = array_filter($postulaciones, function($p) use ($fecha_limite) {
    return isset($p['fecha_postulacion']) && $p['fecha_postulacion'] >= $fecha_limite;
});
$count_postulaciones_semana = count($postulaciones_semana);

// Últimas postulaciones
usort($postulaciones, function($a, $b) {
    return strtotime($b['fecha_postulacion']) - strtotime($a['fecha_postulacion']);
});
$ultimas_postulaciones = array_slice($postulaciones, 0, 10);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin | Bolsa de Trabajo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="../css/boton.css" rel="stylesheet"> 
</head>

<body>
  <!-- Theme Toggle Button -->
  <button id="themeToggle" class="theme-toggle" aria-label="Cambiar tema">
    <i class="bi bi-moon-fill"></i>
  </button>

  <div class="admin-wrapper">
    <!-- Header -->
    <div class="dashboard-header">
      <h1>
        <i class="bi bi-speedometer2"></i> 
        Panel de Administración
      </h1>
      <p>Gestión Integral - Bolsa de Trabajo Universitaria</p>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-header">
          <div class="stat-content">
            <div class="stat-label">Ofertas Activas</div>
            <div class="stat-value"><?= $ofertas_activas ?></div>
            <div class="stat-desc">Disponibles ahora</div>
          </div>
          <div class="stat-icon">
            <i class="bi bi-briefcase-fill"></i>
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-header">
          <div class="stat-content">
            <div class="stat-label">Postulaciones (7d)</div>
            <div class="stat-value"><?= $count_postulaciones_semana ?></div>
            <div class="stat-desc">Esta semana</div>
          </div>
          <div class="stat-icon">
            <i class="bi bi-people-fill"></i>
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-header">
          <div class="stat-content">
            <div class="stat-label">Empresas Pendientes</div>
            <div class="stat-value"><?= $empresas_pendientes ?></div>
            <div class="stat-desc">Requieren aprobación</div>
          </div>
          <div class="stat-icon">
            <i class="bi bi-hourglass-split"></i>
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-header">
          <div class="stat-content">
            <div class="stat-label">Total Empresas</div>
            <div class="stat-value"><?= $total_empresas ?></div>
            <div class="stat-desc">Registradas</div>
          </div>
          <div class="stat-icon">
            <i class="bi bi-building-fill"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Gestión Principal -->
    <div class="management-grid">
      <!-- Gestión de Empresas -->
      <div class="management-card">
        <div class="management-header">
          <div class="management-icon">
            <i class="bi bi-building"></i>
          </div>
          <h3>Gestión de Empresas</h3>
        </div>
        <div class="actions-list">
          <a href="lista_empresas.php" class="action-link">
            <i class="bi bi-eye"></i>
            Ver Empresas Registradas
          </a>
          <a href="empresas.php" class="action-link">
            <i class="bi bi-gear"></i>
            Administrar CRUD
          </a>
        </div>
      </div>

      <!-- Gestión de Ofertas -->
      <div class="management-card">
        <div class="management-header">
          <div class="management-icon">
            <i class="bi bi-briefcase"></i>
          </div>
          <h3>Gestión de Ofertas</h3>
        </div>
        <div class="actions-list">
          <a href="lista_ofertas.php" class="action-link">
            <i class="bi bi-list-ul"></i>
            Ver Todas las Ofertas
          </a>
          <a href="ofertas.php" class="action-link">
            <i class="bi bi-pencil"></i>
            Administrar CRUD
          </a>
        </div>
      </div>

      <!-- Gestión de Reportes -->
      <div class="management-card">
        <div class="management-header">
          <div class="management-icon">
            <i class="bi bi-file-earmark-bar-graph"></i>
          </div>
          <h3>Reportes y Estadísticas</h3>
        </div>
        <div class="actions-list">
          <a href="reportes.php" class="action-link">
            <i class="bi bi-graph-up"></i>
            Ver Reportes Generales
          </a>
        </div>
      </div>
    </div>

    <!-- Últimas Postulaciones -->
    <?php if (!empty($ultimas_postulaciones)): ?>
    <div class="content-section">
      <h2 class="section-title">
        <i class="bi bi-clock-history"></i>
        Últimas Postulaciones
      </h2>
      
      <div class="postulaciones-list">
        <?php foreach ($ultimas_postulaciones as $post): ?>
        <div class="postulacion-item">
          <div class="post-icon">
            <i class="bi bi-person-check"></i>
          </div>
          <div class="post-content">
            <h4><?= htmlspecialchars($post['nombre_estudiante'] ?? 'N/A') ?></h4>
            <p>
              <strong><?= htmlspecialchars($post['titulo_oferta'] ?? 'N/A') ?></strong>
              - <?= htmlspecialchars($post['nombre_empresa'] ?? 'N/A') ?>
            </p>
            <div class="post-meta">
              <span class="post-date">
                <i class="bi bi-calendar3"></i>
                <?= date('d/m/Y H:i', strtotime($post['fecha_postulacion'])) ?>
              </span>
              <?php
              $estado = $post['estado'] ?? 'pendiente';
              $badgeClass = 'secondary';
              if ($estado === 'aceptada') $badgeClass = 'success';
              elseif ($estado === 'rechazada') $badgeClass = 'danger';
              elseif ($estado === 'en_revision') $badgeClass = 'warning';
              ?>
              <span class="post-badge bg-<?= $badgeClass ?>">
                <?= ucfirst(str_replace('_', ' ', $estado)) ?>
              </span>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <?php include '../layout/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Theme Toggle Functionality
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;
    const icon = themeToggle.querySelector('i');
    
    // Check for saved theme preference or default to 'light'
    const currentTheme = localStorage.getItem('theme') || 'light';
    html.setAttribute('data-theme', currentTheme);
    updateIcon(currentTheme);
    
    themeToggle.addEventListener('click', () => {
      const currentTheme = html.getAttribute('data-theme');
      const newTheme = currentTheme === 'light' ? 'dark' : 'light';
      
      html.setAttribute('data-theme', newTheme);
      localStorage.setItem('theme', newTheme);
      updateIcon(newTheme);
    });
    
    function updateIcon(theme) {
      if (theme === 'dark') {
        icon.classList.remove('bi-moon-fill');
        icon.classList.add('bi-sun-fill');
      } else {
        icon.classList.remove('bi-sun-fill');
        icon.classList.add('bi-moon-fill');
      }
    }
  </script>
</body>
</html>