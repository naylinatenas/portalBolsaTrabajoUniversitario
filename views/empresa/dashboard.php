<?php
require_once __DIR__ . '/../../config/auth.php';
verificarRol('empresa');  

include '../layout/header.php';
?>

<link href="../css/styles.css" rel="stylesheet">

<?php
require_once __DIR__ . '/../../models/OfertaDAO.php';
require_once __DIR__ . '/../../models/PostulacionDAO.php';
require_once __DIR__ . '/../../models/EmpresaDAO.php';

$ofertaDAO = new OfertaDAO();
$postDAO = new PostulacionDAO();
$empDAO = new EmpresaDAO();

$usuario_id = $_SESSION['id_usuario'];
$empresa = $empDAO->obtenerPorUsuario($usuario_id);

$total_ofertas = count($ofertaDAO->listarPorEmpresa($empresa->id_empresa));
$total_postulaciones = $postDAO->contarPorEmpresa($empresa->id_empresa);
?>


<!-- ✅ Botón modo oscuro (manteniendo tus íconos originales) -->
<button id="btnModoOscuro" class="btn-modo-oscuro" onclick="toggleModoOscuro()" title="Cambiar tema">
  <svg id="iconoSol" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
    <path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/>
  </svg>
  <svg id="iconoLuna" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" style="display:none;">
    <path d="M6 .278a.768.768 0 0 1 .08.858A7.208 7.208 0 0 0 5.202 4.6C5.202 8.62 8.48 11.877 12.52 11.877c.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"/>
  </svg>
</button>

<!-- Contenido principal -->
<div class="container my-5">
  <div class="panel-header">
      <h1>Panel de Empresa</h1>
  </div>

  <div class="row g-4">
      <div class="col-md-6">
          <div class="stats-card card">
              <div class="card-body">
                  <h5>Mis Ofertas Publicadas</h5>
                  <span class="number"><?= $total_ofertas ?></span>
                  <a href="mis_ofertas.php" class="btn-ver">Ver Ofertas</a>
              </div>
          </div>
      </div>

      <div class="col-md-6">
          <div class="stats-card card">
              <div class="card-body">
                  <h5>Postulaciones Recibidas</h5>
                  <span class="number"><?= $total_postulaciones ?></span>
                  <a href="postulaciones_recibidas.php" class="btn-ver">Ver Postulaciones</a>
              </div>
          </div>
      </div>
  </div>
</div>

<script>
// Mantiene tu modo oscuro funcional
document.addEventListener('DOMContentLoaded', function() {
  const modoOscuro = localStorage.getItem('modoOscuro') === 'true';
  if (modoOscuro) {
    document.body.classList.add('modo-oscuro');
    document.getElementById('iconoSol').style.display = 'none';
    document.getElementById('iconoLuna').style.display = 'block';
  }
});

function toggleModoOscuro() {
  const body = document.body;
  const iconoSol = document.getElementById('iconoSol');
  const iconoLuna = document.getElementById('iconoLuna');
  
  body.classList.toggle('modo-oscuro');
  
  if (body.classList.contains('modo-oscuro')) {
    iconoSol.style.display = 'none';
    iconoLuna.style.display = 'block';
    localStorage.setItem('modoOscuro', 'true');
  } else {
    iconoSol.style.display = 'block';
    iconoLuna.style.display = 'none';
    localStorage.setItem('modoOscuro', 'false');
  }
}
</script>

<?php include '../layout/footer.php'; ?>
