<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario sea admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: /portalBolsaTrabajoUniversitario/views/errores/acceso_denegado.php");
    exit;
}

include '../layout/header.php';
require_once __DIR__ . '/../../models/OfertaDAO.php';

try {
    $dao = new OfertaDAO();
    $ofertas = $dao->listar();
} catch (Exception $e) {
    die("Error al cargar ofertas: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lista de Ofertas | Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { 
      background: #f5f7fa; 
      font-family: "Segoe UI", sans-serif; 
    }
    
    .main-container { 
      max-width: 1400px; 
      margin: 0 auto; 
      padding: 2rem 1rem; 
    }
    
    /* Header */
    .page-header {
      background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
      border-radius: 12px;
      padding: 2rem;
      margin-bottom: 2rem;
      color: white;
      box-shadow: 0 4px 15px rgba(30,60,114,0.2);
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
    
    .header-actions {
      display: flex;
      gap: 0.75rem;
      margin-top: 1rem;
    }
    
    .btn-header {
      background: rgba(255,255,255,0.2);
      color: white;
      border: 2px solid white;
      padding: 0.6rem 1.2rem;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      font-size: 0.9rem;
      transition: all 0.3s;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .btn-header:hover {
      background: white;
      color: #1e3c72;
      transform: translateY(-2px);
    }
    
    /* Table Section */
    .table-section {
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      overflow: hidden;
    }
    
    .table-header {
      background: #f8f9fa;
      padding: 1.25rem 1.5rem;
      border-bottom: 2px solid #e9ecef;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 1rem;
    }
    
    .table-header h2 {
      font-size: 1.25rem;
      font-weight: 700;
      margin: 0;
      color: #212529;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .table-header h2 i {
      color: #1e88e5;
    }
    
    .search-box {
      position: relative;
      width: 100%;
      max-width: 350px;
    }
    
    .search-box input {
      width: 100%;
      padding: 0.65rem 1rem 0.65rem 2.75rem;
      border: 2px solid #e9ecef;
      border-radius: 8px;
      font-size: 0.9rem;
      transition: all 0.3s;
    }
    
    .search-box input:focus {
      border-color: #1e88e5;
      box-shadow: 0 0 0 0.2rem rgba(30,136,229,0.1);
      outline: none;
    }
    
    .search-box i {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: #6c757d;
      font-size: 1.1rem;
    }
    
    /* Table */
    .table-responsive {
      max-height: 600px;
      overflow-y: auto;
    }
    
    .data-table {
      width: 100%;
      margin: 0;
    }
    
    .data-table thead {
      position: sticky;
      top: 0;
      z-index: 10;
    }
    
    .data-table thead th {
      background: #f8f9fa;
      border-bottom: 2px solid #dee2e6;
      font-weight: 600;
      font-size: 0.85rem;
      color: #495057;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 1rem 1.25rem;
      white-space: nowrap;
    }
    
    .data-table tbody td {
      padding: 1rem 1.25rem;
      vertical-align: middle;
      font-size: 0.9rem;
      border-bottom: 1px solid #f1f3f5;
    }
    
    .data-table tbody tr {
      transition: background-color 0.2s ease;
    }
    
    .data-table tbody tr:hover {
      background-color: #f8f9fa;
    }
    
    .oferta-info {
      display: flex;
      flex-direction: column;
      gap: 0.25rem;
    }
    
    .oferta-titulo {
      font-weight: 600;
      color: #212529;
      font-size: 0.95rem;
    }
    
    .oferta-detalle {
      font-size: 0.85rem;
      color: #6c757d;
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }
    
    .oferta-detalle i {
      font-size: 0.8rem;
    }
    
    /* Badges */
    .badge-custom {
      padding: 0.4rem 0.8rem;
      border-radius: 6px;
      font-size: 0.8rem;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
    }
    
    .badge-activa {
      background: #e8f5e9;
      color: #2e7d32;
    }
    
    .badge-pausada {
      background: #fff3e0;
      color: #ef6c00;
    }
    
    .badge-cerrada {
      background: #ffebee;
      color: #c62828;
    }
    
    .badge-tipo {
      background: #e3f2fd;
      color: #1565c0;
      padding: 0.3rem 0.6rem;
      font-size: 0.75rem;
    }
    
    .badge-modalidad {
      background: #f3e5f5;
      color: #7b1fa2;
      padding: 0.3rem 0.6rem;
      font-size: 0.75rem;
    }
    
    /* Action Buttons */
    .btn-view {
      padding: 0.5rem 1rem;
      background: #1e88e5;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 0.85rem;
      font-weight: 600;
      transition: all 0.3s;
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      text-decoration: none;
      cursor: pointer;
    }
    
    .btn-view:hover {
      background: #1565c0;
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(30,136,229,0.3);
    }
    
    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 4rem 2rem;
      color: #6c757d;
    }
    
    .empty-state i {
      font-size: 4rem;
      color: #dee2e6;
      margin-bottom: 1.5rem;
    }
    
    .empty-state h3 {
      font-size: 1.25rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
      color: #495057;
    }
    
    .empty-state p {
      font-size: 0.95rem;
      margin-bottom: 1.5rem;
    }
    
    /* ID Badge */
    .id-badge {
      background: #e3f2fd;
      color: #1565c0;
      padding: 0.3rem 0.6rem;
      border-radius: 6px;
      font-size: 0.85rem;
      font-weight: 700;
      font-family: 'Courier New', monospace;
    }
    
    /* Salario */
    .salario-badge {
      background: #e8f5e9;
      color: #2e7d32;
      padding: 0.4rem 0.7rem;
      border-radius: 6px;
      font-size: 0.85rem;
      font-weight: 700;
    }
    
    @media (max-width: 768px) {
      .main-container { 
        padding: 1rem; 
      }
      
      .page-header h1 { 
        font-size: 1.5rem; 
      }
      
      .header-actions {
        flex-direction: column;
        width: 100%;
      }
      
      .btn-header {
        justify-content: center;
      }
      
      .table-header {
        flex-direction: column;
        align-items: flex-start;
      }
      
      .search-box {
        max-width: 100%;
      }
      
      .data-table {
        font-size: 0.85rem;
      }
      
      .data-table thead th,
      .data-table tbody td {
        padding: 0.75rem;
      }
    }
  </style>
</head>

<body>
  <div class="main-container">
    <!-- Header -->
    <div class="page-header">
      <h1><i class="bi bi-briefcase-fill"></i> Ofertas Laborales</h1>
      <p>Visualiza y consulta todas las ofertas publicadas en el sistema</p>
      <div class="header-actions">
        <a href="dashboard.php" class="btn-header">
          <i class="bi bi-arrow-left"></i>
          Volver al Dashboard
        </a>
        <a href="ofertas.php" class="btn-header">
          <i class="bi bi-gear"></i>
          Gestionar CRUD
        </a>
      </div>
    </div>

    <!-- Tabla de Ofertas -->
    <div class="table-section">
      <div class="table-header">
        <h2>
          <i class="bi bi-list-ul"></i>
          Lista Completa
        </h2>
        <div class="search-box">
          <i class="bi bi-search"></i>
          <input 
            type="text" 
            id="searchInput" 
            placeholder="Buscar por título, empresa o tipo...">
        </div>
      </div>

      <?php if (empty($ofertas)): ?>
        <div class="empty-state">
          <i class="bi bi-inbox"></i>
          <h3>No hay ofertas registradas</h3>
          <p>Aún no se han publicado ofertas en el sistema</p>
          <a href="ofertas.php" class="btn-view">
            <i class="bi bi-plus-circle"></i>
            Agregar Primera Oferta
          </a>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="data-table" id="ofertasTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Oferta</th>
                <th>Empresa</th>
                <th>Tipo/Modalidad</th>
                <th>Salario</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($ofertas as $o): ?>
              <tr>
                <td>
                  <span class="id-badge">#<?= htmlspecialchars($o['id_oferta']) ?></span>
                </td>
                <td>
                  <div class="oferta-info">
                    <span class="oferta-titulo">
                      <?= htmlspecialchars($o['titulo']) ?>
                    </span>
                    <?php if (!empty($o['ubicacion'])): ?>
                    <span class="oferta-detalle">
                      <i class="bi bi-geo-alt"></i>
                      <?= htmlspecialchars($o['ubicacion']) ?>
                    </span>
                    <?php endif; ?>
                  </div>
                </td>
                <td>
                  <span class="oferta-detalle">
                    <i class="bi bi-building"></i>
                    <?= htmlspecialchars($o['razon_social'] ?? 'N/A') ?>
                  </span>
                </td>
                <td>
                  <div class="d-flex flex-column gap-1">
                    <span class="badge-tipo">
                      <?= ucfirst(htmlspecialchars($o['tipo'])) ?>
                    </span>
                    <span class="badge-modalidad">
                      <?= ucfirst(htmlspecialchars($o['modalidad'])) ?>
                    </span>
                  </div>
                </td>
                <td>
                  <span class="salario-badge">
                    S/ <?= number_format($o['salario_referencial'], 2) ?>
                  </span>
                </td>
                <td class="text-center">
                  <?php
                  $estado = strtolower($o['estado_oferta']);
                  $badgeClass = 'badge-pausada';
                  $icon = 'pause-circle-fill';
                  
                  if ($estado === 'activa') {
                      $badgeClass = 'badge-activa';
                      $icon = 'check-circle-fill';
                  } elseif ($estado === 'cerrada') {
                      $badgeClass = 'badge-cerrada';
                      $icon = 'x-circle-fill';
                  }
                  ?>
                  <span class="badge-custom <?= $badgeClass ?>">
                    <i class="bi bi-<?= $icon ?>"></i>
                    <?= ucfirst($estado) ?>
                  </span>
                </td>
                <td class="text-center">
                  <button class="btn-view" 
                          data-bs-toggle="modal" 
                          data-bs-target="#modalDetalle"
                          data-id="<?= htmlspecialchars($o['id_oferta']) ?>"
                          data-empresa="<?= htmlspecialchars($o['empresa_id']) ?>"
                          data-titulo="<?= htmlspecialchars($o['titulo']) ?>"
                          data-descripcion="<?= htmlspecialchars($o['descripcion'] ?? 'Sin descripción') ?>"
                          data-tipo="<?= htmlspecialchars($o['tipo']) ?>"
                          data-salario="<?= htmlspecialchars($o['salario_referencial']) ?>"
                          data-modalidad="<?= htmlspecialchars($o['modalidad']) ?>"
                          data-fecha-pub="<?= htmlspecialchars($o['fecha_publicacion'] ?? 'N/A') ?>"
                          data-fecha-cierre="<?= htmlspecialchars($o['fecha_cierre'] ?? 'N/A') ?>"
                          data-estado="<?= htmlspecialchars($o['estado_oferta']) ?>">
                    <i class="bi bi-eye"></i>
                    Ver Detalles
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <?php include '../layout/footer.php'; ?>
  
  <!-- MODAL DETALLE OFERTA -->
  <div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary bg-opacity-10 border-bottom border-primary">
          <h5 class="modal-title fw-bold text-primary">
            <i class="bi bi-briefcase me-2"></i>Detalles de Oferta
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-3">
          <div class="info-row">
            <span class="info-label">ID Oferta:</span>
            <span id="detailId" class="info-value"></span>
          </div>
          <div class="info-row">
            <span class="info-label">Empresa ID:</span>
            <span id="detailEmpresaId" class="info-value"></span>
          </div>
          <div class="info-row">
            <span class="info-label">Título:</span>
            <span id="detailTitulo" class="info-value"></span>
          </div>
          <div class="info-row">
            <span class="info-label">Descripción:</span>
            <span id="detailDescripcion" class="info-value"></span>
          </div>
          <div class="info-row">
            <span class="info-label">Tipo:</span>
            <span id="detailTipo" class="info-value"></span>
          </div>
          <div class="info-row">
            <span class="info-label">Salario Referencial:</span>
            <span id="detailSalario" class="info-value"></span>
          </div>
          <div class="info-row">
            <span class="info-label">Modalidad:</span>
            <span id="detailModalidad" class="info-value"></span>
          </div>
          <div class="info-row">
            <span class="info-label">Fecha Publicación:</span>
            <span id="detailFechaPub" class="info-value"></span>
          </div>
          <div class="info-row">
            <span class="info-label">Fecha Cierre:</span>
            <span id="detailFechaCierre" class="info-value"></span>
          </div>
          <div class="info-row">
            <span class="info-label">Estado Oferta:</span>
            <span id="detailEstado" class="info-value"></span>
          </div>
        </div>
        <div class="modal-footer bg-light border-top">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i>Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
  
  <style>
    .info-row {
      display: flex;
      padding: 0.6rem 0;
      border-bottom: 1px solid #f1f3f5;
    }
    
    .info-row:last-child {
      border-bottom: none;
    }
    
    .info-label {
      font-size: 0.85rem;
      font-weight: 600;
      color: #1e88e5;
      min-width: 130px;
      flex-shrink: 0;
    }
    
    .info-value {
      font-size: 0.9rem;
      color: #495057;
      flex: 1;
    }
  </style>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Búsqueda en tiempo real
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.getElementById('searchInput');
      
      if (searchInput) {
        searchInput.addEventListener('keyup', function() {
          const searchTerm = this.value.toLowerCase().trim();
          const tableRows = document.querySelectorAll('#ofertasTable tbody tr');
          let visibleRows = 0;
          
          tableRows.forEach(function(row) {
            const text = row.textContent.toLowerCase();
            const shouldShow = text.includes(searchTerm);
            
            row.style.display = shouldShow ? '' : 'none';
            if (shouldShow) visibleRows++;
          });
          
          // Mostrar mensaje si no hay resultados
          const tbody = document.querySelector('#ofertasTable tbody');
          let noResultsRow = document.getElementById('noResultsRow');
          
          if (visibleRows === 0 && searchTerm !== '') {
            if (!noResultsRow) {
              noResultsRow = document.createElement('tr');
              noResultsRow.id = 'noResultsRow';
              noResultsRow.innerHTML = `
                <td colspan="7" class="text-center py-5">
                  <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>
                  <p class="text-muted mb-0">No se encontraron resultados para "<strong>${searchTerm}</strong>"</p>
                </td>
              `;
              tbody.appendChild(noResultsRow);
            }
          } else if (noResultsRow) {
            noResultsRow.remove();
          }
        });
      }
      
      // Modal Detalle
      const modalDetalle = document.getElementById('modalDetalle');
      if (modalDetalle) {
        modalDetalle.addEventListener('show.bs.modal', function(event) {
          const btn = event.relatedTarget;
          const estado = btn.getAttribute('data-estado');
          
          // Llenar datos
          document.getElementById('detailId').textContent = '#' + btn.getAttribute('data-id');
          document.getElementById('detailEmpresaId').textContent = '#' + btn.getAttribute('data-empresa');
          document.getElementById('detailTitulo').textContent = btn.getAttribute('data-titulo');
          document.getElementById('detailDescripcion').textContent = btn.getAttribute('data-descripcion');
          document.getElementById('detailTipo').textContent = btn.getAttribute('data-tipo').charAt(0).toUpperCase() + btn.getAttribute('data-tipo').slice(1);
          document.getElementById('detailSalario').textContent = 'S/ ' + parseFloat(btn.getAttribute('data-salario')).toFixed(2);
          document.getElementById('detailModalidad').textContent = btn.getAttribute('data-modalidad').charAt(0).toUpperCase() + btn.getAttribute('data-modalidad').slice(1);
          document.getElementById('detailFechaPub').textContent = btn.getAttribute('data-fecha-pub');
          document.getElementById('detailFechaCierre').textContent = btn.getAttribute('data-fecha-cierre');
          
          // Estado simple
          const estadoElement = document.getElementById('detailEstado');
          let badgeClass = 'bg-warning';
          
          if (estado === 'activa') {
            badgeClass = 'bg-success';
          } else if (estado === 'cerrada') {
            badgeClass = 'bg-danger';
          }
          
          estadoElement.innerHTML = `<span class="badge ${badgeClass}">${estado.charAt(0).toUpperCase() + estado.slice(1)}</span>`;
        });
      }
    });
  </script>
</body>
</html>