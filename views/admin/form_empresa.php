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
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrar Empresa | Bolsa de Trabajo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

    :root {
      --primary-blue: #3b82f6;
      --primary-blue-dark: #2563eb;
      --primary-blue-light: #60a5fa;
      --primary-blue-subtle: #93c5fd;
      --accent-blue: #06b6d4;
      
      /* Light mode */
      --bg-primary: #fafbfc;
      --bg-secondary: #f8fafc;
      --bg-card: #ffffff;
      --bg-elevated: #ffffff;
      --text-primary: #0f172a;
      --text-secondary: #475569;
      --text-tertiary: #94a3b8;
      --border-color: #e2e8f0;
      --border-subtle: #f1f5f9;
      --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
      --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -1px rgba(0, 0, 0, 0.05);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
      --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    [data-theme="dark"] {
      --primary-blue: #60a5fa;
      --primary-blue-dark: #3b82f6;
      --primary-blue-light: #93c5fd;
      --primary-blue-subtle: #3b82f6;
      --accent-blue: #22d3ee;
      
      /* Dark mode */
      --bg-primary: #0f172a;
      --bg-secondary: #1e293b;
      --bg-card: #1e293b;
      --bg-elevated: #334155;
      --text-primary: #f1f5f9;
      --text-secondary: #cbd5e1;
      --text-tertiary: #94a3b8;
      --border-color: #334155;
      --border-subtle: #1e293b;
      --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
      --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -2px rgba(0, 0, 0, 0.4);
      --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.6), 0 10px 10px -5px rgba(0, 0, 0, 0.5);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body { 
      background: var(--bg-primary); 
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      color: var(--text-primary);
      transition: background-color 0.3s ease, color 0.3s ease;
      line-height: 1.5;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    /* Theme Toggle Button */
    .theme-toggle {
      position: fixed;
      top: 20px;
      right: 20px;
      width: 42px;
      height: 42px;
      border-radius: 10px;
      background: var(--bg-elevated);
      border: 1px solid var(--border-color);
      color: var(--text-primary);
      font-size: 1.1rem;
      cursor: pointer;
      box-shadow: var(--shadow-lg);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: center;
      backdrop-filter: blur(10px);
    }
    
    .theme-toggle:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-xl);
      background: var(--primary-blue);
      color: white;
      border-color: var(--primary-blue);
    }

    .container {
      max-width: 900px;
      margin: 0 auto;
      padding: 2rem 1rem;
    }

    /* Header Section */
    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .header-content h3 {
      font-size: 1.75rem;
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 0.25rem;
      letter-spacing: -0.025em;
    }

    .header-content p {
      color: var(--text-secondary);
      font-size: 0.9rem;
      margin: 0;
    }

    .btn-back {
      background: var(--bg-elevated);
      color: var(--text-primary);
      border: 1px solid var(--border-color);
      padding: 0.625rem 1.25rem;
      border-radius: 10px;
      text-decoration: none;
      font-weight: 600;
      font-size: 0.875rem;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-back:hover {
      background: var(--primary-blue);
      color: white;
      border-color: var(--primary-blue);
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
    }

    /* Card */
    .form-card {
      background: var(--bg-card);
      border-radius: 14px;
      padding: 2rem;
      box-shadow: var(--shadow-lg);
      border: 1px solid var(--border-color);
    }

    /* Form Elements */
    .form-label {
      color: var(--text-primary);
      font-weight: 600;
      font-size: 0.875rem;
      margin-bottom: 0.5rem;
      display: block;
    }

    .text-danger {
      color: #ef4444;
    }

    .form-control,
    .form-select {
      background: var(--bg-secondary);
      border: 1px solid var(--border-color);
      color: var(--text-primary);
      padding: 0.75rem 1rem;
      border-radius: 10px;
      font-size: 0.9375rem;
      transition: all 0.3s ease;
      width: 100%;
    }

    .form-control:focus,
    .form-select:focus {
      background: var(--bg-elevated);
      border-color: var(--primary-blue);
      outline: none;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-control::placeholder {
      color: var(--text-tertiary);
    }

    [data-theme="dark"] .form-control,
    [data-theme="dark"] .form-select {
      background: var(--bg-secondary);
      color: var(--text-primary);
    }

    [data-theme="dark"] .form-control:focus,
    [data-theme="dark"] .form-select:focus {
      background: var(--bg-elevated);
    }

    .form-select option {
      background: var(--bg-card);
      color: var(--text-primary);
    }

    small.text-muted {
      color: var(--text-tertiary);
      font-size: 0.8125rem;
      display: block;
      margin-top: 0.25rem;
    }

    /* Buttons */
    .btn-primary-custom {
      background: linear-gradient(135deg, var(--primary-blue-dark), var(--primary-blue));
      color: white;
      border: none;
      padding: 0.875rem 2rem;
      border-radius: 10px;
      font-weight: 700;
      font-size: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.625rem;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 4px 12px -2px rgba(59, 130, 246, 0.3);
    }

    .btn-primary-custom:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px -4px rgba(59, 130, 246, 0.4);
    }

    .btn-secondary-custom {
      background: var(--bg-secondary);
      color: var(--text-primary);
      border: 1px solid var(--border-color);
      padding: 0.875rem 2rem;
      border-radius: 10px;
      font-weight: 600;
      font-size: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-secondary-custom:hover {
      background: var(--bg-elevated);
      border-color: var(--primary-blue-subtle);
      color: var(--text-primary);
      transform: translateY(-1px);
    }

    .button-group {
      display: grid;
      gap: 0.875rem;
      margin-top: 2rem;
    }

    /* Grid Layout */
    .form-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.25rem;
      margin-bottom: 1.25rem;
    }

    .form-group {
      margin-bottom: 1.25rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .container {
        padding: 1.5rem 1rem;
      }

      .form-card {
        padding: 1.5rem;
        border-radius: 12px;
      }

      .header-content h3 {
        font-size: 1.5rem;
      }

      .page-header {
        flex-direction: column;
        align-items: flex-start;
      }

      .form-row {
        grid-template-columns: 1fr;
        gap: 1rem;
      }
    }

    /* Scrollbar */
    ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }

    ::-webkit-scrollbar-track {
      background: var(--bg-secondary);
    }

    ::-webkit-scrollbar-thumb {
      background: var(--border-color);
      border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: var(--primary-blue-subtle);
    }
  </style>
</head>

<body>
  <!-- Theme Toggle Button -->
  <button id="themeToggle" class="theme-toggle" aria-label="Cambiar tema">
    <i class="bi bi-moon-fill"></i>
  </button>

  <div class="container">
    <!-- Header -->
    <div class="page-header">
      <div class="header-content">
        <h3>Registrar Nueva Empresa</h3>
        <p>Complete los datos de la empresa</p>
      </div>
      <a href="/PORTALBOLSATRABAJOUNIVERSITARIO/views/admin/empresas.php" class="btn-back">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>

    <!-- Form Card -->
    <div class="form-card">
      <form action="/portalBolsaTrabajoUniversitario/controllers/adminControlador.php" method="POST" id="formEmpresa">
        <input type="hidden" name="action" value="crear_empresa">

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Razón Social <span class="text-danger">*</span></label>
            <input type="text" name="razon_social" class="form-control" placeholder="Ej: Empresa SAC" required>
          </div>
          <div class="form-group">
            <label class="form-label">RUC <span class="text-danger">*</span></label>
            <input type="text" name="ruc" class="form-control" placeholder="Ej: 20123456789" pattern="[0-9]{11}" maxlength="11" required>
            <small class="text-muted">11 dígitos</small>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Dirección</label>
            <input type="text" name="direccion" class="form-control" placeholder="Ej: Av. Principal 123">
          </div>
          <div class="form-group">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono" class="form-control" placeholder="Ej: 987654321" pattern="[0-9]{9}" maxlength="9">
            <small class="text-muted">9 dígitos</small>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Correo de Contacto <span class="text-danger">*</span></label>
          <input type="email" name="correo_contacto" class="form-control" placeholder="correo@empresa.com" required>
        </div>

        <div class="form-group">
          <label class="form-label">Estado Inicial</label>
          <select name="estado" class="form-select">
            <option value="pendiente">Pendiente de revisión</option>
            <option value="aprobada" selected>Aprobada</option>
            <option value="rechazada">Rechazada</option>
          </select>
        </div>

        <div class="button-group">
          <button type="submit" class="btn-primary-custom">
            <i class="bi bi-save"></i> Guardar Empresa
          </button>
          <a href="/PORTALBOLSATRABAJOUNIVERSITARIO/views/admin/empresas.php" class="btn-secondary-custom">
            Cancelar
          </a>
        </div>
      </form>
    </div>
  </div>

  <?php include '../layout/footer.php'; ?>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Theme Toggle Functionality
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;
    const icon = themeToggle.querySelector('i');
    
    // Check for saved theme preference or default to 'light'
    const currentTheme = localStorage.getItem('theme') || 'light';
    html.setAttribute('data-theme', currentTheme);
    updateIcon(currentTheme);
    
    themeToggle.addEventListener('click', () => {
      const currentTheme = html.getAttribute('data-theme');
      const newTheme = currentTheme === 'light' ? 'dark' : 'light';
      
      html.setAttribute('data-theme', newTheme);
      localStorage.setItem('theme', newTheme);
      updateIcon(newTheme);
    });
    
    function updateIcon(theme) {
      if (theme === 'dark') {
        icon.classList.remove('bi-moon-fill');
        icon.classList.add('bi-sun-fill');
      } else {
        icon.classList.remove('bi-sun-fill');
        icon.classList.add('bi-moon-fill');
      }
    }

    // Debug del formulario
    document.getElementById('formEmpresa').addEventListener('submit', function(e) {
        console.log('Formulario enviándose...');
        console.log('Action:', this.action);
        console.log('Method:', this.method);
        
        // Verificar que todos los campos requeridos tengan valor
        const razonSocial = document.querySelector('input[name="razon_social"]').value;
        const ruc = document.querySelector('input[name="ruc"]').value;
        const correo = document.querySelector('input[name="correo_contacto"]').value;
        
        console.log('Razón Social:', razonSocial);
        console.log('RUC:', ruc);
        console.log('Correo:', correo);
        
        if (!razonSocial || !ruc || !correo) {
            alert('Por favor complete todos los campos requeridos');
            e.preventDefault();
            return false;
        }
    });
    // Theme Toggle Functionality
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;
    const icon = themeToggle.querySelector('i');
    
    // Check for saved theme preference or default to 'light'
    const currentTheme = localStorage.getItem('theme') || 'light';
    html.setAttribute('data-theme', currentTheme);
    updateIcon(currentTheme);
    
    themeToggle.addEventListener('click', () => {
      const currentTheme = html.getAttribute('data-theme');
      const newTheme = currentTheme === 'light' ? 'dark' : 'light';
      
      html.setAttribute('data-theme', newTheme);
      localStorage.setItem('theme', newTheme);
      updateIcon(newTheme);
    });
    
    function updateIcon(theme) {
      if (theme === 'dark') {
        icon.classList.remove('bi-moon-fill');
        icon.classList.add('bi-sun-fill');
      } else {
        icon.classList.remove('bi-sun-fill');
        icon.classList.add('bi-moon-fill');
      }
    }
  </script>
</body>
</html>