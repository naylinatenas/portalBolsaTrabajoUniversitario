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
      background: #f8f9fa; 
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .reportes-wrapper { 
      max-width: 1400px; 
      margin: 0 auto; 
      padding: 2rem 1rem; 
    }
    
    /* Header */
    .page-header {
      background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
      border-radius: 12px;
      padding: 2.5rem;
      margin-bottom: 2rem;
      color: white;
      box-shadow: 0 4px 20px rgba(30, 58, 138, 0.25);
    }
    
    .page-header h1 { 
      font-size: 2rem; 
      font-weight: 700; 
      margin: 0 0 0.5rem 0; 
    }
    
    .page-header p { 
      font-size: 1rem; 
      opacity: 0.95; 
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
      padding: 0.6rem 1.2rem;
      background: rgba(255,255,255,0.15);
      border-radius: 8px;
      transition: all 0.3s;
      font-weight: 500;
    }

    .back-link:hover {
      background: rgba(255,255,255,0.25);
      color: white;
      transform: translateX(-3px);
    }
    
    /* Content Sections */
    .content-section {
      background: white;
      border-radius: 12px;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 2px 12px rgba(0,0,0,0.08);
      border: 1px solid #e5e7eb;
    }
    
    .section-title {
      font-size: 1.35rem;
      font-weight: 700;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      color: #1e3a8a;
      padding-bottom: 1rem;
      border-bottom: 2px solid #e5e7eb;
    }
    
    .section-title i { 
      color: #1e40af; 
      font-size: 1.4rem;
    }
    
    /* Chart Mejorado */
    .chart-container { 
      height: 380px; 
      margin-top: 1.5rem; 
      position: relative;
      padding: 1.5rem;
      background: #f9fafb;
      border-radius: 10px;
      border: 1px solid #e5e7eb;
    }

    .charts-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
      gap: 2rem;
    }
    
    @media (max-width: 992px) {
      .charts-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      .reportes-wrapper { 
        padding: 1rem; 
      }

      .page-header {
        padding: 1.5rem;
      }
      
      .page-header h1 { 
        font-size: 1.5rem; 
      }

      .content-section {
        padding: 1.5rem;
      }

      .section-title {
        font-size: 1.2rem;
      }

      .chart-container {
        height: 320px;
        padding: 1rem;
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
      <p>Análisis detallado del rendimiento y actividad del sistema</p>
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
          Distribución de Ofertas Laborales
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
        Tendencia de Postulaciones (Últimos 6 Meses)
      </h2>
      <div class="chart-container">
        <canvas id="postulacionesMesChart"></canvas>
      </div>
    </div>
  </div>

  <?php include '../layout/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Configuración global de Chart.js
    Chart.defaults.font.family = "'Segoe UI', sans-serif";
    Chart.defaults.color = '#6b7280';

    // Gráfico Top Empresas - Barras Horizontales Mejoradas
    <?php if (!empty($top_empresas)): ?>
    new Chart(document.getElementById('empresasChart'), {
      type: 'bar',
      data: {
        labels: <?= json_encode(array_column($top_empresas, 'nombre')) ?>,
        datasets: [{
          label: 'Ofertas Publicadas',
          data: <?= json_encode(array_column($top_empresas, 'count')) ?>,
          backgroundColor: function(context) {
            const chart = context.chart;
            const {ctx, chartArea} = chart;
            if (!chartArea) return '#1e40af';
            
            const gradient = ctx.createLinearGradient(chartArea.left, 0, chartArea.right, 0);
            gradient.addColorStop(0, '#1e40af');
            gradient.addColorStop(1, '#3b82f6');
            return gradient;
          },
          borderRadius: 8,
          borderWidth: 0,
          barThickness: 45,
          maxBarThickness: 50
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
          legend: { 
            display: false 
          },
          tooltip: {
            backgroundColor: 'rgba(30, 58, 138, 0.95)',
            padding: 14,
            cornerRadius: 8,
            titleFont: { size: 14, weight: 'bold' },
            bodyFont: { size: 13 },
            displayColors: false,
            callbacks: {
              label: function(context) {
                return 'Total: ' + context.parsed.x + ' ofertas';
              }
            }
          }
        },
        scales: { 
          x: { 
            beginAtZero: true, 
            ticks: { 
              stepSize: 1,
              font: { size: 12 },
              color: '#6b7280'
            },
            grid: {
              color: 'rgba(0, 0, 0, 0.06)',
              drawBorder: false
            },
            border: {
              display: false
            }
          },
          y: {
            ticks: {
              font: { size: 12, weight: '500' },
              color: '#374151'
            },
            grid: {
              display: false
            },
            border: {
              display: false
            }
          }
        }
      }
    });
    <?php endif; ?>

    // Gráfico Estado de Ofertas - Dona Mejorada
    new Chart(document.getElementById('ofertasChart'), {
      type: 'doughnut',
      data: {
        labels: ['Activas', 'Inactivas', 'Cerradas'],
        datasets: [{
          data: [<?= $ofertas_activas ?>, <?= $ofertas_inactivas ?>, <?= $ofertas_cerradas ?>],
          backgroundColor: [
            '#059669',
            '#d97706',
            '#dc2626'
          ],
          borderWidth: 4,
          borderColor: '#fff',
          hoverOffset: 12,
          hoverBorderWidth: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 20,
              font: { size: 13, weight: '600' },
              color: '#374151',
              usePointStyle: true,
              pointStyle: 'circle',
              boxWidth: 10,
              boxHeight: 10
            }
          },
          tooltip: {
            backgroundColor: 'rgba(30, 58, 138, 0.95)',
            padding: 14,
            cornerRadius: 8,
            titleFont: { size: 14, weight: 'bold' },
            bodyFont: { size: 13 },
            callbacks: {
              label: function(context) {
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = ((context.parsed / total) * 100).toFixed(1);
                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
              }
            }
          }
        },
        cutout: '68%'
      }
    });

    // Gráfico Postulaciones por Mes - Línea con Área Mejorada
    new Chart(document.getElementById('postulacionesMesChart'), {
      type: 'line',
      data: {
        labels: <?= json_encode(array_column(array_values($meses_data), 'label')) ?>,
        datasets: [{
          label: 'Postulaciones',
          data: <?= json_encode(array_column(array_values($meses_data), 'count')) ?>,
          borderColor: '#1e40af',
          backgroundColor: function(context) {
            const chart = context.chart;
            const {ctx, chartArea} = chart;
            if (!chartArea) return 'rgba(30, 64, 175, 0.1)';
            
            const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
            gradient.addColorStop(0, 'rgba(30, 64, 175, 0.25)');
            gradient.addColorStop(1, 'rgba(30, 64, 175, 0.02)');
            return gradient;
          },
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointRadius: 5,
          pointHoverRadius: 8,
          pointBackgroundColor: '#1e40af',
          pointBorderColor: '#fff',
          pointBorderWidth: 3,
          pointHoverBackgroundColor: '#1e3a8a',
          pointHoverBorderColor: '#fff',
          pointHoverBorderWidth: 3
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          intersect: false,
          mode: 'index'
        },
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: 'rgba(30, 58, 138, 0.95)',
            padding: 14,
            cornerRadius: 8,
            titleFont: { size: 14, weight: 'bold' },
            bodyFont: { size: 13 },
            displayColors: false,
            callbacks: {
              label: function(context) {
                return 'Total: ' + context.parsed.y + ' postulaciones';
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1,
              font: { size: 12 },
              color: '#6b7280'
            },
            grid: {
              color: 'rgba(0, 0, 0, 0.06)',
              drawBorder: false
            },
            border: {
              display: false
            }
          },
          x: {
            ticks: {
              font: { size: 12, weight: '500' },
              color: '#374151'
            },
            grid: {
              display: false
            },
            border: {
              display: false
            }
          }
        }
      }
    });
  </script>
</body>
</html>