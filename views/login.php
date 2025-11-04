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
  <link href="/public/css/style.css" rel="stylesheet">
  <style>
    .login-wrapper {
      min-height: 100vh;
      background: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      position: relative;
      overflow: hidden;
    }
    
    .login-wrapper::before {
      content: '';
      position: absolute;
      width: 500px;
      height: 500px;
      background: rgba(13, 71, 161, 0.03);
      border-radius: 50%;
      top: -250px;
      right: -250px;
    }
    
    .login-wrapper::after {
      content: '';
      position: absolute;
      width: 400px;
      height: 400px;
      background: rgba(21, 101, 192, 0.02);
      border-radius: 50%;
      bottom: -200px;
      left: -200px;
    }
    
    .login-card {
      background: #ffffff;
      border: 1px solid #e0e0e0;
      border-radius: 24px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
      width: 100%;
      max-width: 440px;
      padding: 3rem 2.5rem;
      position: relative;
      z-index: 1;
      animation: slideUp 0.5s ease-out;
    }
    
    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .login-header {
      text-align: center;
      margin-bottom: 2.5rem;
    }
    
    .login-icon {
      width: 70px;
      height: 70px;
      background: linear-gradient(135deg, #0d47a1, #1565c0);
      border-radius: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.5rem;
      box-shadow: 0 8px 16px rgba(13, 71, 161, 0.3);
    }
    
    .login-icon svg {
      width: 36px;
      height: 36px;
      color: white;
    }
    
    .login-header h4 {
      font-weight: 700;
      color: #0d47a1;
      margin-bottom: 0.5rem;
      font-size: 1.75rem;
    }
    
    .login-header p {
      color: #6c757d;
      font-size: 0.95rem;
      margin: 0;
    }
    
    .form-floating {
      margin-bottom: 1.25rem;
    }
    
    .form-floating > .form-control {
      border: 2px solid #e0e0e0;
      border-radius: 12px;
      padding: 1rem 1rem;
      height: 58px;
      transition: all 0.3s ease;
      font-size: 0.95rem;
    }
    
    .form-floating > .form-control:focus {
      border-color: #0d47a1;
      box-shadow: 0 0 0 4px rgba(13, 71, 161, 0.1);
    }
    
    .form-floating > label {
      padding: 1rem 1rem;
      color: #6c757d;
    }
    
    .form-check {
      margin-bottom: 1.5rem;
    }
    
    .form-check-input {
      width: 20px;
      height: 20px;
      border: 2px solid #d0d0d0;
      border-radius: 6px;
      cursor: pointer;
    }
    
    .form-check-input:checked {
      background-color: #0d47a1;
      border-color: #0d47a1;
    }
    
    .form-check-label {
      margin-left: 0.5rem;
      color: #495057;
      font-size: 0.95rem;
      cursor: pointer;
    }
    
    .btn-login {
      background: linear-gradient(135deg, #0d47a1, #1565c0);
      border: none;
      border-radius: 12px;
      color: white;
      padding: 14px;
      font-weight: 600;
      font-size: 1rem;
      width: 100%;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(13, 71, 161, 0.3);
    }
    
    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(13, 71, 161, 0.4);
      background: linear-gradient(135deg, #0a3a82, #0d47a1);
    }
    
    .btn-login:active {
      transform: translateY(0);
    }
    
    .alert {
      border: none;
      border-radius: 12px;
      padding: 1rem;
      margin-bottom: 1.5rem;
      animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .alert-danger {
      background-color: #fee;
      color: #c62828;
      border-left: 4px solid #dc3545;
    }
    
    .demo-info {
      background: linear-gradient(135deg, #e3f2fd, #bbdefb);
      border-radius: 12px;
      padding: 1rem;
      margin-top: 1.5rem;
      text-align: center;
      border: 1px solid rgba(13, 71, 161, 0.1);
    }
    
    .demo-info p {
      margin: 0;
      color: #0d47a1;
      font-size: 0.9rem;
      font-weight: 500;
    }
    
    .demo-info small {
      color: #1565c0;
      font-size: 0.85rem;
    }
  </style>
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
    
    <div class="demo-info">
      <p>🔐 Cuenta de prueba</p>
      <small>admin@ucv.edu.pe / admin123</small>
    </div>
  </div>
</div>
</body>
</html>