<?php
// vista/errores/acceso_denegado.php
http_response_code(403);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Acceso denegado</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="alert alert-danger">
    <h4>Acceso denegado</h4>
    <p>No tienes permisos para ver esta página.</p>
    <a href="/index.php" class="btn btn-primary">Ir al inicio</a>
  </div>
</div>
</body>
</html>
