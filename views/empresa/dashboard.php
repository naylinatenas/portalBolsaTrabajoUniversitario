<?php
require_once __DIR__ . '/../../config/auth.php';
verificarRol('empresa');  

include '../layout/header.php';
require_once __DIR__ . '/../../models/OfertaDAO.php';
require_once __DIR__ . '/../../models/PostulacionDAO.php';
require_once __DIR__ . '/../../models/EmpresaDAO.php';

$ofertaDAO = new OfertaDAO();
$postDAO = new PostulacionDAO();
$empDAO = new EmpresaDAO();

$usuario_id = $_SESSION['id_usuario'] ?? null;
$empresa = $empDAO->obtenerPorUsuario($usuario_id);

$total_ofertas = $empresa ? count($ofertaDAO->listarPorEmpresa($empresa->id_empresa)) : 0;
$total_postulaciones = $empresa ? $postDAO->contarPorEmpresa($empresa->id_empresa) : 0;
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
/* ====== Estilos del Dashboard ====== */
body {
  background: #f5f7fb;
  font-family: "Segoe UI", sans-serif;
  color: #333;
  transition: background 0.3s ease, color 0.3s ease;
}

/* ===== Encabezado ===== */
.dashboard-header {
  background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
  color: white;
  text-align: center;
  border-radius: 14px;
  padding: 2rem 1rem;
  box-shadow: 0 4px 15px rgba(30,60,114,0.2);
  margin-bottom: 2.5rem;
}
.dashboard-header h1 {
  font-weight: 700;
  margin-bottom: .5rem;
}
.dashboard-header p {
  color: #e0e7ff;
}

/* ===== Cards ===== */
.card-dashboard {
  background: white;
  border-radius: 16px;
  padding: 2.5rem 1.5rem 2rem;
  text-align: center;
  transition: all 0.3s ease;
  border: 2px solid transparent;
  box-shadow: 0 3px 8px rgba(0,0,0,0.05);
  position: relative;
}

.card-dashboard h5 {
  font-weight: 600;
  margin-bottom: .5rem;
  color: #333;
}

.card-dashboard h2 {
  font-size: 2.4rem;
  font-weight: 700;
  color: #1e3c72;
  margin: .5rem 0;
}

.card-dashboard p {
  color: #6c757d;
  font-size: 0.9rem;
  margin-bottom: 1.5rem;
}

.card-dashboard.azul { border-top: 4px solid #1e88e5; }
.card-dashboard.verde { border-top: 4px solid #43a047; }

.card-dashboard:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 18px rgba(0,0,0,0.1);
}

/* ===== Iconos circulares ===== */
.card-icon {
  width: 60px;
  height: 60px;
  margin: 0 auto 1rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.8rem;
  color: white;
}

.card-dashboard.azul .card-icon { background: #1e88e5; }
.card-dashboard.verde .card-icon { background: #43a047; }

/* ===== Botones ===== */
.card-dashboard .btn {
  border-radius: 30px;
  font-weight: 600;
  padding: 0.6rem 1.2rem;
  transition: all 0.3s;
}
.card-dashboard .btn-primary:hover { background: #0d47a1; }
.card-dashboard .btn-success:hover { background: #2e7d32; }

/* ===== Botón modo oscuro ===== */
#btnModoOscuro {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: #1e88e5;
  color: white;
  border: none;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
  box-shadow: 0 4px 8px rgba(0,0,0,0.3);
  cursor: pointer;
  transition: all 0.3s;
  z-index: 1050;
}
#btnModoOscuro:hover {
  background: #1565c0;
  transform: scale(1.05);
}

/* ====== 🌙 Modo oscuro ====== */
body.modo-oscuro {
  background: #121212;
  color: #e8eaed;
}
body.modo-oscuro .dashboard-header {
  background: linear-gradient(135deg, #0d1b2a, #1b263b);
}
body.modo-oscuro .card-dashboard {
  background: #1f1f1f;
  color: #e8eaed;
  border-color: #333;
}
body.modo-oscuro .card-dashboard h2 {
  color: #8ab4f8;
}
body.modo-oscuro .btn-primary {
  background: #8ab4f8;
  color: #202124;
}
body.modo-oscuro .btn-success {
  background: #81c995;
  color: #202124;
}
</style>

<div class="container my-5">
  <div class="dashboard-header">
    <h1><i class="bi bi-building"></i> Panel de Empresa</h1>
    <p>Gestiona tus ofertas laborales y postulaciones recibidas</p>
  </div>

  <div class="row g-4 justify-content-center">
    <!-- Card: Ofertas -->
    <div class="col-md-5">
      <div class="card-dashboard azul">
        <div class="card-icon">
          <i class="bi bi-briefcase-fill"></i>
        </div>
        <h5>Mis Ofertas Publicadas</h5>
        <h2><?= $total_ofertas ?></h2>
        <p>Ofertas laborales activas o cerradas</p>
        <a href="mis_ofertas.php" class="btn btn-primary w-100">
          <i class="bi bi-list-task"></i> Ver Ofertas
        </a>
      </div>
    </div>

    <!-- Card: Postulaciones -->
    <div class="col-md-5">
      <div class="card-dashboard verde">
        <div class="card-icon">
          <i class="bi bi-people-fill"></i>
        </div>
        <h5>Postulaciones Recibidas</h5>
        <h2><?= $total_postulaciones ?></h2>
        <p>Candidatos interesados en tus ofertas</p>
        <a href="postulaciones_recibidas.php" class="btn btn-success w-100">
          <i class="bi bi-person-lines-fill"></i> Ver Postulaciones
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Botón modo oscuro -->
<button id="btnModoOscuro" title="Cambiar tema">
  <i id="iconoModo" class="bi bi-moon-fill"></i>
</button>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const btn = document.getElementById("btnModoOscuro");
  const icono = document.getElementById("iconoModo");
  const body = document.body;

  if (localStorage.getItem("modoOscuro") === "true") {
    body.classList.add("modo-oscuro");
    icono.classList.replace("bi-moon-fill", "bi-sun-fill");
  }

  btn.addEventListener("click", () => {
    body.classList.toggle("modo-oscuro");
    const activo = body.classList.contains("modo-oscuro");
    icono.classList.replace(
      activo ? "bi-moon-fill" : "bi-sun-fill",
      activo ? "bi-sun-fill" : "bi-moon-fill"
    );
    localStorage.setItem("modoOscuro", activo);
  });
});
</script>

<?php include '../layout/footer.php'; ?>
