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

// Estadísticas adicionales para reportes
$total_postulaciones = count($postulaciones);
$postulaciones_aceptadas = count(array_filter($postulaciones, fn($p) => ($p['estado'] ?? '') === 'aceptada'));
$postulaciones_rechazadas = count(array_filter($postulaciones, fn($p) => ($p['estado'] ?? '') === 'rechazada'));
$postulaciones_pendientes = count(array_filter($postulaciones, fn($p) => ($p['estado'] ?? '') === 'pendiente'));

// Postulaciones por mes (últimos 6 meses)
$meses_data = [];
for ($i = 5; $i >= 0; $i--) {
    $mes = date('Y-m', strtotime("-$i months"));
    $meses_data[$mes] = [
        'label' => date('M Y', strtotime("-$i months")),
        'count' => 0
    ];
}

foreach ($postulaciones as $post) {
    if (isset($post['fecha_postulacion'])) {
        $mes = date('Y-m', strtotime($post['fecha_postulacion']));
        if (isset($meses_data[$mes])) {
            $meses_data[$mes]['count']++;
        }
    }
}

// Ofertas por estado
$ofertas_activas = count(array_filter($ofertas, fn($o) => ($o['estado_oferta'] ?? '') === 'activa'));
$ofertas_inactivas = count(array_filter($ofertas, fn($o) => ($o['estado_oferta'] ?? '') === 'inactiva'));
$ofertas_cerradas = count($ofertas) - $ofertas_activas - $ofertas_inactivas;
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reportes y Estadísticas | Bolsa de Trabajo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { 
      background: #f5f7fa; 
      font-family: "Segoe UI", sans-serif; 
    }
    
    .reportes-wrapper { 
      max-width: 1400px; 
      margin: 0 auto; 
      padding: 2rem 1rem; 
    }
    
    /* Header */
    .page-header {
      background: linear-gradient(135deg, #fb8c00 0%, #f57c00 100%);
      border-radius: 12px;
      padding: 2rem;
      margin-bottom: 2rem;
      color: white;
      box-shadow: 0 4px 15px rgba(251,140,0,0.2);
    }
    
    .page-header h1 { 
      font-size: 1.75rem; 
      font-weight: 700; 
      margin: 0 0 0.5rem 0; 
    }
    
    .page-header p { 
      font-size: 0.95rem; 
      opacity: 0.9; 
      margin: 0; 
    }

    .back-link {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      color: white;
      text-decoration: none;
      font-size: 0.9rem;
      margin-bottom: 1rem;
      padding: 0.5rem 1rem;
      background: rgba(255,255,255,0.2);
      border-radius: 8px;
      transition: background 0.3s;
    }

    .back-link:hover {
      background: rgba(255,255,255,0.3);
      color: white;
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
      color: #fb8c00; 
      font-size: 1.3rem;
    }
    
    /* Stats Grid */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-bottom: 2rem;
    }
    
    .stat-box {
      background: white;
      border-radius: 10px;
      padding: 1.25rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      border-top: 3px solid var(--color);
      text-align: center;
    }
    
    .stat-box.primary { --color: #1e88e5; }
    .stat-box.success { --color: #43a047; }
    .stat-box.danger { --color: #e53935; }
    .stat-box.warning { --color: #fb8c00; }
    
    .stat-box h3 {
      font-size: 0.85rem;
      color: #6c757d;
      text-transform: uppercase;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }
    
    .stat-box .value {
      font-size: 2rem;
      font-weight: 700;
      color: var(--color);
      margin-bottom: 0.25rem;
    }
    
    .stat-box .label {
      font-size: 0.8rem;
      color: #6c757d;
    }
    
    /* Chart */
    .chart-container { 
      height: 350px; 
      margin-top: 1rem; 
      position: relative;
    }

    .charts-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
      gap: 1.5rem;
      margin-bottom: 1.5rem;
    }
    
    /* Action Buttons */
    .action-buttons {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
      margin-top: 1.5rem;
    }
    
    .btn-export {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.75rem 1.5rem;
      background: #fb8c00;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.3s;
      border: none;
    }
    
    .btn-export:hover {
      background: #f57c00;
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(251,140,0,0.3);
    }
    
    @media (max-width: 768px) {
      .reportes-wrapper { 
        padding: 1rem; 
      }
      
      .stats-grid { 
        grid-template-columns: 1fr; 
      }

      .charts-grid {
        grid-template-columns: 1fr;
      }
      
      .page-header h1 { 
        font-size: 1.5rem; 
      }
    }
  </style>
</head>

<body>
  <div class="reportes-wrapper">
    <!-- Header -->
    <div class="page-header">
      <a href="dashboard.php" class="back-link">
        <i class="bi bi-arrow-left"></i>
        Volver al Dashboard
      </a>
      <h1><i class="bi bi-file-earmark-bar-graph"></i> Reportes y Estadísticas</h1>
      <p>Análisis detallado del sistema de bolsa de trabajo</p>
    </div>

    <!-- Estadísticas de Postulaciones -->
    <div class="content-section">
      <h2 class="section-title">
        <i class="bi bi-graph-up"></i>
        Estado de Postulaciones
      </h2>
      
      <div class="stats-grid">
        <div class="stat-box primary">
          <h3>Total</h3>
          <div class="value"><?= $total_postulaciones ?></div>
          <div class="label">Postulaciones</div>
        </div>
        
        <div class="stat-box success">
          <h3>Aceptadas</h3>
          <div class="value"><?= $postulaciones_aceptadas ?></div>
          <div class="label">
            <?= $total_postulaciones > 0 ? round(($postulaciones_aceptadas/$total_postulaciones)*100, 1) : 0 ?>%
          </div>
        </div>
        
        <div class="stat-box danger">
          <h3>Rechazadas</h3>
          <div class="value"><?= $postulaciones_rechazadas ?></div>
          <div class="label">
            <?= $total_postulaciones > 0 ? round(($postulaciones_rechazadas/$total_postulaciones)*100, 1) : 0 ?>%
          </div>
        </div>
        
        <div class="stat-box warning">
          <h3>Pendientes</h3>
          <div class="value"><?= $postulaciones_pendientes ?></div>
          <div class="label">
            <?= $total_postulaciones > 0 ? round(($postulaciones_pendientes/$total_postulaciones)*100, 1) : 0 ?>%
          </div>
        </div>
      </div>
    </div>

    <!-- Gráficos -->
    <div class="charts-grid">
      <!-- Top Empresas -->
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

      <!-- Estado de Ofertas -->
      <div class="content-section">
        <h2 class="section-title">
          <i class="bi bi-pie-chart"></i>
          Estado de Ofertas Laborales
        </h2>
        <div class="chart-container">
          <canvas id="ofertasChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Postulaciones por Mes -->
    <div class="content-section">
      <h2 class="section-title">
        <i class="bi bi-calendar3"></i>
        Postulaciones por Mes (Últimos 6 Meses)
      </h2>
      <div class="chart-container">
        <canvas id="postulacionesMesChart"></canvas>
      </div>
    </div>

    <!-- Acciones de Exportación -->
    <div class="content-section">
      <h2 class="section-title">
        <i class="bi bi-download"></i>
        Exportar Datos
      </h2>
      <div class="action-buttons">
        <a href="exportar.php?tipo=empresas" class="btn-export">
          <i class="bi bi-file-earmark-excel"></i>
          Exportar Empresas
        </a>
        <a href="exportar.php?tipo=ofertas" class="btn-export">
          <i class="bi bi-file-earmark-excel"></i>
          Exportar Ofertas
        </a>
        <a href="exportar.php?tipo=postulaciones" class="btn-export">
          <i class="bi bi-file-earmark-excel"></i>
          Exportar Postulaciones
        </a>
        <a href="exportar.php?tipo=completo" class="btn-export">
          <i class="bi bi-file-earmark-zip"></i>
          Exportar Todo
        </a>
      </div>
    </div>
  </div>

  <?php include '../layout/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Gráfico Top Empresas
    <?php if (!empty($top_empresas)): ?>
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
    <?php endif; ?>

    // Gráfico Estado de Ofertas
    new Chart(document.getElementById('ofertasChart'), {
      type: 'doughnut',
      data: {
        labels: ['Activas', 'Inactivas', 'Cerradas'],
        datasets: [{
          data: [<?= $ofertas_activas ?>, <?= $ofertas_inactivas ?>, <?= $ofertas_cerradas ?>],
          backgroundColor: ['#43a047', '#fb8c00', '#e53935'],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 15,
              font: { size: 12 }
            }
          },
          tooltip: {
            backgroundColor: 'rgba(0,0,0,0.8)',
            padding: 12,
            cornerRadius: 8
          }
        }
      }
    });

    // Gráfico Postulaciones por Mes
    new Chart(document.getElementById('postulacionesMesChart'), {
      type: 'line',
      data: {
        labels: <?= json_encode(array_column(array_values($meses_data), 'label')) ?>,
        datasets: [{
          label: 'Postulaciones',
          data: <?= json_encode(array_column(array_values($meses_data), 'count')) ?>,
          borderColor: '#1e88e5',
          backgroundColor: 'rgba(30,136,229,0.1)',
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointRadius: 5,
          pointHoverRadius: 7,
          pointBackgroundColor: '#1e88e5',
          pointBorderColor: '#fff',
          pointBorderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: 'rgba(0,0,0,0.8)',
            padding: 12,
            cornerRadius: 8
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
</body>
</html>