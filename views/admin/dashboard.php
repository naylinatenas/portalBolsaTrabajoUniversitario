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
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

    :root {
      --primary-blue: #3b82f6;
      --primary-blue-dark: #2563eb;
      --primary-blue-light: #60a5fa;
      --primary-blue-subtle: #93c5fd;
      --accent-blue: #06b6d4;
      
      /* Light mode */
      --bg-primary: #fafbfc;
      --bg-secondary: #f8fafc;
      --bg-card: #ffffff;
      --bg-elevated: #ffffff;
      --text-primary: #0f172a;
      --text-secondary: #475569;
      --text-tertiary: #94a3b8;
      --border-color: #e2e8f0;
      --border-subtle: #f1f5f9;
      --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
      --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -1px rgba(0, 0, 0, 0.05);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
      --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    [data-theme="dark"] {
      --primary-blue: #60a5fa;
      --primary-blue-dark: #3b82f6;
      --primary-blue-light: #93c5fd;
      --primary-blue-subtle: #3b82f6;
      --accent-blue: #22d3ee;
      
      /* Dark mode */
      --bg-primary: #0f172a;
      --bg-secondary: #1e293b;
      --bg-card: #1e293b;
      --bg-elevated: #334155;
      --text-primary: #f1f5f9;
      --text-secondary: #cbd5e1;
      --text-tertiary: #94a3b8;
      --border-color: #334155;
      --border-subtle: #1e293b;
      --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
      --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -2px rgba(0, 0, 0, 0.4);
      --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.6), 0 10px 10px -5px rgba(0, 0, 0, 0.5);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body { 
      background: var(--bg-primary); 
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      color: var(--text-primary);
      transition: background-color 0.3s ease, color 0.3s ease;
      line-height: 1.5;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }
    
    .admin-wrapper { 
      max-width: 1340px; 
      margin: 0 auto; 
      padding: 1.5rem 1rem; 
    }
    
    /* Theme Toggle Button */
    .theme-toggle {
      position: fixed;
      top: 20px;
      right: 20px;
      width: 42px;
      height: 42px;
      border-radius: 10px;
      background: var(--bg-elevated);
      border: 1px solid var(--border-color);
      color: var(--text-primary);
      font-size: 1.1rem;
      cursor: pointer;
      box-shadow: var(--shadow-lg);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: center;
      backdrop-filter: blur(10px);
    }
    
    .theme-toggle:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-xl);
      background: var(--primary-blue);
      color: white;
      border-color: var(--primary-blue);
    }
    
    .theme-toggle:active {
      transform: translateY(0);
    }
    
    /* Header */
    .dashboard-header {
      background: linear-gradient(135deg, var(--primary-blue-dark) 0%, var(--primary-blue) 50%, var(--accent-blue) 100%);
      border-radius: 14px;
      padding: 1.75rem 2rem;
      margin-bottom: 1.75rem;
      color: white;
      box-shadow: var(--shadow-lg);
      position: relative;
      overflow: hidden;
    }
    
    .dashboard-header::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -10%;
      width: 300px;
      height: 300px;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
      border-radius: 50%;
    }
    
    .dashboard-header h1 { 
      font-size: 1.5rem; 
      font-weight: 700; 
      margin: 0 0 0.5rem 0; 
      display: flex;
      align-items: center;
      gap: 0.75rem;
      position: relative;
      letter-spacing: -0.025em;
    }
    
    .dashboard-header p { 
      font-size: 0.9rem; 
      opacity: 0.95; 
      margin: 0; 
      font-weight: 400;
      position: relative;
    }
    
    /* Stats Grid */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 1.25rem;
      margin-bottom: 1.75rem;
    }
    
    .stat-card {
      background: var(--bg-card);
      border-radius: 12px;
      padding: 1.25rem;
      box-shadow: var(--shadow-sm);
      border: 1px solid var(--border-color);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }
    
    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 3px;
      background: linear-gradient(90deg, var(--primary-blue), var(--accent-blue));
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    
    .stat-card:hover {
      transform: translateY(-3px);
      box-shadow: var(--shadow-lg);
      border-color: var(--primary-blue-subtle);
    }
    
    .stat-card:hover::before {
      opacity: 1;
    }
    
    .stat-header { 
      display: flex; 
      justify-content: space-between; 
      align-items: flex-start;
      margin-bottom: 0.25rem;
    }
    
    .stat-icon {
      width: 44px;
      height: 44px;
      background: linear-gradient(135deg, var(--primary-blue), var(--primary-blue-light));
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.2rem;
      flex-shrink: 0;
      box-shadow: 0 6px 12px -3px rgba(59, 130, 246, 0.3);
    }
    
    .stat-content {
      flex: 1;
    }
    
    .stat-label {
      font-size: 0.75rem;
      color: var(--text-tertiary);
      text-transform: uppercase;
      font-weight: 600;
      letter-spacing: 0.05em;
      margin-bottom: 0.35rem;
    }
    
    .stat-value {
      font-size: 2rem;
      font-weight: 800;
      color: var(--text-primary);
      line-height: 1;
      margin-bottom: 0.35rem;
      letter-spacing: -0.02em;
    }
    
    .stat-desc { 
      font-size: 0.8rem; 
      color: var(--text-secondary); 
      font-weight: 500;
    }
    
    /* Management Grid */
    .management-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 1.25rem;
      margin-bottom: 1.75rem;
    }
    
    .management-card {
      background: var(--bg-card);
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: var(--shadow-sm);
      border: 1px solid var(--border-color);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .management-card:hover {
      box-shadow: var(--shadow-md);
      border-color: var(--primary-blue-subtle);
    }
    
    .management-header {
      display: flex;
      align-items: center;
      gap: 0.875rem;
      margin-bottom: 1.25rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid var(--border-subtle);
    }
    
    .management-icon {
      width: 44px;
      height: 44px;
      background: linear-gradient(135deg, var(--primary-blue), var(--primary-blue-light));
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.2rem;
      flex-shrink: 0;
      box-shadow: 0 4px 10px -2px rgba(59, 130, 246, 0.3);
    }
    
    .management-header h3 {
      font-size: 1rem;
      font-weight: 700;
      margin: 0;
      color: var(--text-primary);
      letter-spacing: -0.01em;
    }
    
    .actions-list {
      display: flex;
      flex-direction: column;
      gap: 0.625rem;
    }
    
    .action-link {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.875rem 1rem;
      background: var(--bg-secondary);
      color: var(--text-primary);
      text-decoration: none;
      border-radius: 10px;
      font-weight: 600;
      font-size: 0.875rem;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      border: 1px solid transparent;
      position: relative;
      overflow: hidden;
    }
    
    .action-link::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      height: 100%;
      width: 3px;
      background: var(--primary-blue);
      transform: scaleY(0);
      transition: transform 0.3s ease;
    }
    
    .action-link:hover {
      background: var(--primary-blue);
      color: white;
      border-color: var(--primary-blue);
      transform: translateX(3px);
      box-shadow: 0 4px 10px -2px rgba(59, 130, 246, 0.4);
    }
    
    .action-link:hover::before {
      transform: scaleY(1);
    }
    
    .action-link i {
      font-size: 1rem;
      transition: transform 0.3s ease;
    }
    
    .action-link:hover i {
      transform: scale(1.1);
    }
    
    /* Content Section */
    .content-section {
      background: var(--bg-card);
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: var(--shadow-sm);
      border: 1px solid var(--border-color);
    }
    
    .section-title {
      font-size: 1.25rem;
      font-weight: 700;
      margin-bottom: 1.25rem;
      display: flex;
      align-items: center;
      gap: 0.625rem;
      color: var(--text-primary);
      letter-spacing: -0.02em;
    }
    
    .section-title i { 
      color: var(--primary-blue); 
      font-size: 1.25rem;
    }
    
    /* Postulaciones */
    .postulaciones-list {
      display: grid;
      gap: 0.875rem;
    }
    
    .postulacion-item {
      display: flex;
      gap: 1rem;
      padding: 1.25rem;
      background: var(--bg-secondary);
      border-radius: 12px;
      border: 1px solid var(--border-color);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
    }
    
    .postulacion-item::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      height: 100%;
      width: 3px;
      background: var(--primary-blue);
      border-radius: 12px 0 0 12px;
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    
    .postulacion-item:hover { 
      background: var(--bg-elevated);
      transform: translateX(3px);
      box-shadow: var(--shadow-md);
      border-color: var(--primary-blue-subtle);
    }
    
    .postulacion-item:hover::before {
      opacity: 1;
    }
    
    .post-icon {
      width: 44px;
      height: 44px;
      background: linear-gradient(135deg, var(--primary-blue), var(--primary-blue-light));
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      flex-shrink: 0;
      font-size: 1.1rem;
      box-shadow: 0 4px 10px -2px rgba(59, 130, 246, 0.3);
    }
    
    .post-content {
      flex: 1;
      min-width: 0;
    }
    
    .post-content h4 { 
      font-size: 0.9375rem; 
      font-weight: 700; 
      margin: 0 0 0.375rem 0;
      color: var(--text-primary);
      letter-spacing: -0.01em;
    }
    
    .post-content p { 
      font-size: 0.8125rem; 
      color: var(--text-secondary); 
      margin: 0 0 0.625rem 0;
      line-height: 1.5;
    }
    
    .post-meta {
      display: flex;
      align-items: center;
      gap: 0.875rem;
      flex-wrap: wrap;
    }
    
    .post-date { 
      font-size: 0.75rem; 
      color: var(--text-tertiary);
      display: flex;
      align-items: center;
      gap: 0.375rem;
      font-weight: 500;
    }
    
    .post-badge {
      padding: 0.3rem 0.75rem;
      border-radius: 6px;
      font-size: 0.6875rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    
    .bg-secondary {
      background: var(--text-tertiary);
      color: white;
    }
    
    .bg-success {
      background: linear-gradient(135deg, var(--primary-blue), var(--primary-blue-light));
      color: white;
    }
    
    .bg-danger {
      background: var(--text-primary);
      color: white;
    }
    
    .bg-warning {
      background: var(--accent-blue);
      color: white;
    }
    
    /* Responsive */
    @media (max-width: 1024px) {
      .admin-wrapper { 
        padding: 1.25rem 1rem; 
      }
      
      .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      }
    }
    
    @media (max-width: 768px) {
      .admin-wrapper { 
        padding: 1rem; 
      }
      
      .theme-toggle {
        top: 16px;
        right: 16px;
        width: 40px;
        height: 40px;
      }
      
      .stats-grid, 
      .management-grid { 
        grid-template-columns: 1fr;
        gap: 1rem;
      }
      
      .stat-value { 
        font-size: 1.75rem; 
      }
      
      .dashboard-header {
        padding: 1.5rem;
        border-radius: 12px;
      }
      
      .dashboard-header h1 { 
        font-size: 1.25rem; 
      }
      
      .dashboard-header p {
        font-size: 0.85rem;
      }
      
      .content-section {
        padding: 1.25rem;
        border-radius: 12px;
      }
      
      .management-card {
        padding: 1.25rem;
      }
    }
    
    @media (max-width: 480px) {
      .stat-header {
        flex-direction: column-reverse;
        align-items: flex-start;
        gap: 0.75rem;
      }
      
      .stat-icon {
        align-self: flex-end;
      }
      
      .postulacion-item {
        flex-direction: column;
        gap: 0.875rem;
      }
      
      .stat-value {
        font-size: 1.625rem;
      }
    }

    /* Smooth scrollbar */
    ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }

    ::-webkit-scrollbar-track {
      background: var(--bg-secondary);
    }

    ::-webkit-scrollbar-thumb {
      background: var(--border-color);
      border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: var(--primary-blue-subtle);
    }
  </style>
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