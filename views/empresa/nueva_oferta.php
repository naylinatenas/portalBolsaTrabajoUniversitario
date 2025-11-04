<?php include '../layout/header.php'; ?>

<div class="container mt-4">
  <h3 class="fw-bold text-primary mb-4">Publicar Nueva Oferta</h3>

  <div class="card p-4">
    <form action="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/ofertaControlador.php" method="POST">
      <input type="hidden" name="action" value="crear">

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
        <input type="date" name="fecha_cierre" class="form-control">
      </div>

      <button type="submit" class="btn btn-primario w-100">Publicar Oferta</button>
    </form>
  </div>
</div>

<?php include '../layout/footer.php'; ?>
