<?php
// vista/login.php
session_start();
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
$correo_recordado = $_COOKIE['correo_recordado'] ?? '';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login - Bolsa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/public/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container">
  <div class="row justify-content-center mt-5">
    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h4 class="card-title text-primary">Iniciar sesión</h4>
          <?php if($error): ?><div class="alert alert-danger"><?= htmlentities($error) ?></div><?php endif; ?>
          <form action="/controlador/loginControlador.php" method="post">
            <input type="hidden" name="action" value="login">
            <div class="mb-3">
              <label class="form-label">Correo</label>
              <input required name="correo" type="email" class="form-control" value="<?= htmlentities($correo_recordado) ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Contraseña</label>
              <input required name="clave" type="password" class="form-control">
            </div>
            <div class="mb-3 form-check">
              <input name="recordar" type="checkbox" class="form-check-input" id="recordar">
              <label class="form-check-label" for="recordar">Recordar mi correo</label>
            </div>
            <button class="btn btn-primary w-100">Entrar</button>
          </form>
        </div>
      </div>
      <p class="text-muted mt-2">Usuario de prueba: admin@uni.edu / admin123</p>
    </div>
  </div>
</div>
</body>
</html>
