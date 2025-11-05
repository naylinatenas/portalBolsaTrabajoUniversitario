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
$postulaciones = $postDAO->listar();

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

// Top empresas
$publicaciones_por_empresa = [];
foreach ($ofertas as $oferta) {
    $empresa_id = $oferta['empresa_id'];
    if (!isset($publicaciones_por_empresa[$empresa_id])) {
        $publicaciones_por_empresa[$empresa_id] = [
            'nombre' => $oferta['razon_social'] ?? 'Sin nombre',
            'count' => 0
        ];
    }
    $publicaciones_por_empresa[$empresa_id]['count']++;
}

usort($publicaciones_por_empresa, function($a, $b) {
    return $b['count'] - $a['count'];
});
$top_empresas = array_slice($publicaciones_por_empresa, 0, 5);

// Últimas postulaciones
usort($postulaciones, function($a, $b) {
    return strtotime($b['fecha_postulacion']) - strtotime($a['fecha_postulacion']);
});
$ultimas_postulaciones = array_slice($postulaciones, 0, 6);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin | Bolsa de Trabajo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { 
      background: #f5f7fa; 
      font-family: "Segoe UI", sans-serif; 
    }
    
    .admin-wrapper { 
      max-width: 1400px; 
      margin: 0 auto; 
      padding: 2rem 1rem; 
    }
    
    /* Header */
    .dashboard-header {
      background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
      border-radius: 12px;
      padding: 2rem;
      margin-bottom: 2rem;
      color: white;
      box-shadow: 0 4px 15px rgba(30,60,114,0.2);
    }
    
    .dashboard-header h1 { 
      font-size: 1.75rem; 
      font-weight: 700; 
      margin: 0 0 0.5rem 0; 
    }
    
    .dashboard-header p { 
      font-size: 0.95rem; 
      opacity: 0.9; 
      margin: 0; 
    }
    
    /* Stats Grid */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }
    
    .stat-card {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      border-left: 4px solid var(--color);
      transition: all 0.3s ease;
    }
    
    .stat-card:hover { 
      transform: translateY(-4px); 
      box-shadow: 0 4px 16px rgba(0,0,0,0.12); 
    }
    
    .stat-card.blue { --color: #1e88e5; }
    .stat-card.green { --color: #43a047; }
    .stat-card.orange { --color: #fb8c00; }
    .stat-card.purple { --color: #8e24aa; }
    
    .stat-header { 
      display: flex; 
      justify-content: space-between; 
      align-items: center; 
    }
    
    .stat-icon {
      width: 50px;
      height: 50px;
      background: var(--color);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.4rem;
      flex-shrink: 0;
    }
    
    .stat-content h3 {
      font-size: 0.8rem;
      color: #6c757d;
      text-transform: uppercase;
      font-weight: 600;
      letter-spacing: 0.5px;
      margin-bottom: 0.5rem;
    }
    
    .stat-value {
      font-size: 2.25rem;
      font-weight: 700;
      color: #212529;
      line-height: 1;
    }
    
    .stat-desc { 
      font-size: 0.85rem; 
      color: #6c757d; 
      margin-top: 0.4rem; 
    }
    
    /* Content Sections */
    .content-section {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .section-title {
      font-size: 1.25rem;
      font-weight: 700;
      margin-bottom: 1.25rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: #212529;
    }
    
    .section-title i { 
      color: #1e88e5; 
      font-size: 1.3rem;
    }
    
    /* Management Grid */
    .management-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 1.5rem;
      margin-bottom: 1.5rem;
    }
    
    .management-card {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      border-top: 4px solid var(--color);
    }
    
    .management-card.empresas { --color: #1e88e5; }
    .management-card.ofertas { --color: #43a047; }
    .management-card.reportes { --color: #fb8c00; }
    
    .management-header {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin-bottom: 1rem;
    }
    
    .management-icon {
      width: 45px;
      height: 45px;
      background: var(--color);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.2rem;
    }
    
    .management-header h3 {
      font-size: 1.1rem;
      font-weight: 700;
      margin: 0;
      color: #212529;
    }
    
    .actions-list {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }
    
    .action-link {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      padding: 0.75rem 1rem;
      background: #f8f9fa;
      color: #212529;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
      font-size: 0.9rem;
      transition: all 0.3s;
      border: 2px solid transparent;
    }
    
    .action-link:hover {
      background: var(--color);
      color: white;
      transform: translateX(5px);
    }
    
    .action-link i {
      font-size: 1.1rem;
    }
    
    /* Postulaciones */
    .postulaciones-list {
      display: grid;
      gap: 0.75rem;
    }
    
    .postulacion-item {
      display: flex;
      gap: 1rem;
      padding: 1rem;
      background: #f8f9fa;
      border-radius: 8px;
      border-left: 3px solid #1e88e5;
      transition: background 0.2s;
    }
    
    .postulacion-item:hover { 
      background: #e9ecef; 
    }
    
    .post-icon {
      width: 45px;
      height: 45px;
      background: #1e88e5;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      flex-shrink: 0;
      font-size: 1.2rem;
    }
    
    .post-content {
      flex: 1;
      min-width: 0;
    }
    
    .post-content h4 { 
      font-size: 0.95rem; 
      font-weight: 600; 
      margin: 0 0 0.3rem 0;
      color: #212529;
    }
    
    .post-content p { 
      font-size: 0.85rem; 
      color: #6c757d; 
      margin: 0 0 0.3rem 0;
    }
    
    .post-meta {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      flex-wrap: wrap;
    }
    
    .post-date { 
      font-size: 0.8rem; 
      color: #adb5bd; 
    }
    
    .post-badge {
      padding: 0.25rem 0.6rem;
      border-radius: 6px;
      font-size: 0.75rem;
      font-weight: 600;
    }
    
    /* Chart */
    .chart-container { 
      height: 320px; 
      margin-top: 1rem; 
    }
    
    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 3rem 1rem;
      color: #6c757d;
    }
    
    .empty-state i {
      font-size: 3rem;
      color: #dee2e6;
      margin-bottom: 1rem;
    }
    
    @media (max-width: 768px) {
      .admin-wrapper { 
        padding: 1rem; 
      }
      
      .stats-grid, 
      .management-grid { 
        grid-template-columns: 1fr; 
      }
      
      .stat-value { 
        font-size: 2rem; 
      }
      
      .dashboard-header h1 { 
        font-size: 1.5rem; 
      }
    }
  </style>
</head>

<body>
  <div class="admin-wrapper">
    <!-- Header -->
    <div class="dashboard-header">
      <h1><i class="bi bi-speedometer2"></i> Panel de Administración</h1>
      <p>Gestión Integral - Bolsa de Trabajo Universitaria</p>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid">
      <div class="stat-card blue">
        <div class="stat-header">
          <div class="stat-content">
            <h3>Ofertas Activas</h3>
            <div class="stat-value"><?= $ofertas_activas ?></div>
            <div class="stat-desc">Disponibles ahora</div>
          </div>
          <div class="stat-icon">
            <i class="bi bi-briefcase-fill"></i>
          </div>
        </div>
      </div>

      <div class="stat-card green">
        <div class="stat-header">
          <div class="stat-content">
            <h3>Postulaciones (7d)</h3>
            <div class="stat-value"><?= $count_postulaciones_semana ?></div>
            <div class="stat-desc">Esta semana</div>
          </div>
          <div class="stat-icon">
            <i class="bi bi-people-fill"></i>
          </div>
        </div>
      </div>

      <div class="stat-card orange">
        <div class="stat-header">
          <div class="stat-content">
            <h3>Empresas Pendientes</h3>
            <div class="stat-value"><?= $empresas_pendientes ?></div>
            <div class="stat-desc">Requieren aprobación</div>
          </div>
          <div class="stat-icon">
            <i class="bi bi-hourglass-split"></i>
          </div>
        </div>
      </div>

      <div class="stat-card purple">
        <div class="stat-header">
          <div class="stat-content">
            <h3>Total Empresas</h3>
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
      <div class="management-card empresas">
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
      <div class="management-card ofertas">
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
      <div class="management-card reportes">
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
          <a href="exportar.php" class="action-link">
            <i class="bi bi-download"></i>
            Exportar Datos
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

    <!-- Gráfico Top Empresas -->
    <?php if (!empty($top_empresas)): ?>
    <div class="content-section">
      <h2 class="section-title">
        <i class="bi bi-bar-chart"></i>
        Top 5 Empresas con Más Ofertas
      </h2>
      <div class="chart-container">
        <canvas id="empresasChart"></canvas>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <?php include '../layout/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <?php if (!empty($top_empresas)): ?>
  <script>
    new Chart(document.getElementById('empresasChart'), {
      type: 'bar',
      data: {
        labels: <?= json_encode(array_column($top_empresas, 'nombre')) ?>,
        datasets: [{
          label: 'Ofertas Publicadas',
          data: <?= json_encode(array_column($top_empresas, 'count')) ?>,
          backgroundColor: ['#1e88e5', '#42a5f5', '#64b5f6', '#90caf9', '#bbdefb'],
          borderRadius: 8,
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(0,0,0,0.8)',
            padding: 12,
            cornerRadius: 8,
            titleFont: { size: 14, weight: 'bold' },
            bodyFont: { size: 13 }
          }
        },
        scales: { 
          y: { 
            beginAtZero: true, 
            ticks: { 
              stepSize: 1,
              font: { size: 12 }
            },
            grid: {
              color: 'rgba(0,0,0,0.05)'
            }
          },
          x: {
            ticks: {
              font: { size: 12 }
            },
            grid: {
              display: false
            }
          }
        }
      }
    });
  </script>
  <?php endif; ?>
</body>
</html>