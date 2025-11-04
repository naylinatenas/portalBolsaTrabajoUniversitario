<?php include '../layout/header.php'; ?>
<div class="container mt-4">
  <h3 class="fw-bold text-primary mb-4">Registrar Nueva Empresa</h3>

  <div class="card p-4 shadow-sm">
    <form action="/PORTALBOLSATRABAJOUNIVERSITARIO/controllers/empresaControlador.php" method="POST">
      <input type="hidden" name="action" value="crear_empresa">

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Razón Social</label>
          <input type="text" name="razon_social" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">RUC</label>
          <input type="text" name="ruc" class="form-control">
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Dirección</label>
          <input type="text" name="direccion" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Teléfono</label>
          <input type="text" name="telefono" class="form-control">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Correo de Contacto</label>
        <input type="email" name="correo_contacto" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-primario w-100">Guardar Empresa</button>
    </form>
  </div>
</div>

<?php include '../layout/footer.php'; ?>
