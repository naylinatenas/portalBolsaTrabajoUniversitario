<?php
require_once __DIR__ . '/../../config/auth.php';
verificarRol('empresa');

include '../layout/header.php';
require_once __DIR__ . '/../../models/OfertaDAO.php';  // ✅ Corregir esta línea
require_once __DIR__ . '/../../models/EmpresaDAO.php';
$empresaDAO = new EmpresaDAO();
$usuario_id = $_SESSION['id_usuario'] ?? null;
$empresa = $empresaDAO->obtenerPorUsuario($usuario_id);
?>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
/* ========================= */
/* ESTILO GENERAL             */
/* ========================= */
body {
  background: #f4f6fb;
  font-family: "Segoe UI", sans-serif;
  color: #333;
  transition: background 0.3s, color 0.3s;
}

/* ENCABEZADO AZUL */
.header-section {
  background: linear-gradient(135deg, #1e3c72, #2a5298);
  border-radius: 16px;
  padding: 2rem 2.5rem;
  color: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 4px 15px rgba(30, 60, 114, 0.25);
}
.header-section .info h2 {
  font-weight: 700;
  margin-bottom: 0.3rem;
}
.header-section .info p {
  font-size: 0.95rem;
  opacity: 0.9;
}
.header-section .actions a {
  background: rgba(255, 255, 255, 0.12);
  color: #fff;
  border: 1px solid rgba(255,255,255,0.25);
  border-radius: 10px;
  padding: 8px 16px;
  font-weight: 600;
  margin-left: 10px;
  transition: 0.3s;
  text-decoration: none;
}
.header-section .actions a:hover {
  background: white;
  color: #1e3c72;
}

/* CARD PRINCIPAL */
.card {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
  padding: 2rem;
}
.card h4 {
  font-weight: 700;
  color: #1e3c72;
  margin-bottom: 1rem;
}

/* CAMPOS DEL FORMULARIO */
.form-label {
  font-weight: 600;
  color: #1e3c72;
}
.form-control, .form-select {
  border-radius: 10px;
  border: 1px solid #ced4da;
  transition: all 0.2s;
}
.form-control:focus, .form-select:focus {
  border-color: #1e88e5;
  box-shadow: 0 0 0 0.2rem rgba(30, 136, 229, 0.2);
}

/* BOTONES */
.btn-primario {
  background: #1e88e5;
  color: white;
  border-radius: 10px;
  border: none;
  font-weight: 600;
  transition: all 0.3s;
}
.btn-primario:hover {
  background: #1565c0;
}
.btn-secondary {
  border-radius: 10px;
}

/* MODO OSCURO */
body.modo-oscuro {
  background-color: #121212;
  color: #e8eaed;
}
body.modo-oscuro .header-section {
  background: linear-gradient(135deg, #202124, #3c4043);
}
body.modo-oscuro .card {
  background: #1f1f1f;
  color: #e8eaed;
}
body.modo-oscuro .form-control, 
body.modo-oscuro .form-select {
  background: #2a2a2a;
  color: #e8eaed;
  border: 1px solid #444;
}
body.modo-oscuro .form-control:focus, 
body.modo-oscuro .form-select:focus {
  border-color: #64b5f6;
}

/* BOTÓN MODO OSCURO */
#btnModoOscuro {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: #1e88e5;
  color: white;
  border: none;
  border-radius: 50%;
  width: 52px;
  height: 52px;
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
</style>

<div class="container">
  <!-- ENCABEZADO AZUL -->
  <div class="header-section">
    <div class="info">
      <h2><i class="bi bi-plus-circle"></i> Publicar Nueva Oferta</h2>
      <p>Crea una nueva oportunidad laboral para los estudiantes y egresados</p>
    </div>
    <div class="actions">
      <a href="mis_ofertas.php"><i class="bi bi-arrow-left"></i> Volver</a>
    </div>
  </div>

  <!-- FORMULARIO DE NUEVA OFERTA -->
  <div class="card">
    <h4><i class="bi bi-file-earmark-text"></i> Datos de la Oferta</h4>

    <form action="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/ofertaControlador.php" method="POST">
      <input type="hidden" name="action" value="crear">
      <input type="hidden" name="empresa_id" value="<?= $empresa->id_empresa ?? '' ?>">

      <div class="mb-3">
        <label class="form-label">Título de la Oferta</label>
        <input type="text" name="titulo" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" rows="5" class="form-control" required></textarea>
      </div>

      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label">Tipo</label>
          <select name="tipo" class="form-select">
            <option value="prácticas">Prácticas</option>
            <option value="part-time">Part-time</option>
            <option value="full-time">Full-time</option>
          </select>
        </div>

        <div class="col-md-4 mb-3">
          <label class="form-label">Modalidad</label>
          <select name="modalidad" class="form-select">
            <option value="presencial">Presencial</option>
            <option value="remoto">Remoto</option>
            <option value="mixto">Mixto</option>
          </select>
        </div>

        <div class="col-md-4 mb-3">
          <label class="form-label">Salario Referencial</label>
          <input type="number" name="salario_referencial" class="form-control" step="0.01">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Fecha de Cierre</label>
        <input type="date" name="fecha_cierre" class="form-control" required>
      </div>

      <div class="text-end mt-4">
        <a href="dashboard.php" class="btn btn-secondary me-2">
          <i class="bi bi-x-circle"></i> Cancelar
        </a>
        <button type="submit" class="btn btn-primario">
          <i class="bi bi-check-circle"></i> Publicar Oferta
        </button>
      </div>
    </form>
  </div>
</div>

<!-- 🌙 BOTÓN MODO OSCURO -->
<button id="btnModoOscuro"><i id="iconoModo" class="bi bi-moon-fill"></i></button>

<script>
// 🌙 Modo oscuro persistente
const btnModo = document.getElementById('btnModoOscuro');
const iconoModo = document.getElementById('iconoModo');
const body = document.body;

if (localStorage.getItem('modoOscuro') === 'true') {
  body.classList.add('modo-oscuro');
  iconoModo.classList.replace('bi-moon-fill', 'bi-sun-fill');
}

btnModo.addEventListener('click', () => {
  body.classList.toggle('modo-oscuro');
  const activo = body.classList.contains('modo-oscuro');
  iconoModo.classList.replace(
    activo ? 'bi-moon-fill' : 'bi-sun-fill',
    activo ? 'bi-sun-fill' : 'bi-moon-fill'
  );
  localStorage.setItem('modoOscuro', activo);
});
</script>

<?php include '../layout/footer.php'; ?>
