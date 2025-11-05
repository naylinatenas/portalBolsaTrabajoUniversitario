<?php
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
  <link href="css/styles.css" rel="stylesheet">

</head>
<body>
<div class="login-wrapper">
  <div class="login-card">
    <div class="login-header">
      <div class="login-icon">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
      </div>
      <h4>Bienvenido de nuevo</h4>
      <p>Ingresa tus credenciales para continuar</p>
    </div>
    
    <?php if($error): ?>
      <div class="alert alert-danger" role="alert">
        <strong>⚠ Error:</strong> <?= htmlentities($error) ?>
      </div>
    <?php endif; ?>
    
    <form action="../controllers/loginControlador.php" method="post">
      <input type="hidden" name="action" value="login">
      
      <div class="form-floating">
        <input 
          required 
          name="correo" 
          type="email" 
          class="form-control" 
          id="correo"
          placeholder="correo@ejemplo.com"
          value="<?= htmlentities($correo_recordado) ?>"
          autocomplete="email">
        <label for="correo">Correo electrónico</label>
      </div>
      
      <div class="form-floating">
        <input 
          required 
          name="clave" 
          type="password" 
          class="form-control" 
          id="clave"
          placeholder="Contraseña"
          autocomplete="current-password">
        <label for="clave">Contraseña</label>
      </div>
      
      <div class="form-check">
        <input 
          name="recordar" 
          type="checkbox" 
          class="form-check-input" 
          id="recordar">
        <label class="form-check-label" for="recordar">
          Recordar mi correo
        </label>
      </div>
      
      <button type="submit" class="btn btn-login">
        Iniciar Sesión
      </button>
    </form>
    
    <div class="demo-info" align="left">
      <p>🔐 Cuenta de prueba</p>
      <small>Administrador: admin@ucv.edu.pe / admin123</small><br>
      <small>Empresa: contacto@techcorp.com / tech123</small><br>
      <small>Estudiante: maria.lopez@ucv.edu.pe / maria123</small>
    </div>
  </div>
</div>
</body>
</html>