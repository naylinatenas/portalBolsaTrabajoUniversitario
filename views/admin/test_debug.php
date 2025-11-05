<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Session</title>
    <style>
        body { font-family: monospace; padding: 20px; }
        .ok { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Debug de Sesión</h2>
    
    <h3>Contenido de $_SESSION:</h3>
    <pre><?php print_r($_SESSION); ?></pre>
    
    <h3>Verificaciones:</h3>
    <ul>
        <li>
            Sesión iniciada: 
            <span class="<?php echo isset($_SESSION['id_usuario']) ? 'ok' : 'error'; ?>">
                <?php echo isset($_SESSION['id_usuario']) ? 'SÍ ✓' : 'NO ✗'; ?>
            </span>
        </li>
        <li>
            ID Usuario: 
            <strong><?php echo $_SESSION['id_usuario'] ?? 'NO EXISTE'; ?></strong>
        </li>
        <li>
            Rol: 
            <strong><?php echo $_SESSION['rol'] ?? 'NO EXISTE'; ?></strong>
        </li>
        <li>
            Es admin: 
            <span class="<?php echo (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') ? 'ok' : 'error'; ?>">
                <?php echo (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') ? 'SÍ ✓' : 'NO ✗'; ?>
            </span>
        </li>
    </ul>
    
    <h3>Rutas actuales:</h3>
    <ul>
        <li>Directorio actual: <code><?php echo __DIR__; ?></code></li>
        <li>URL actual: <code><?php echo $_SERVER['REQUEST_URI']; ?></code></li>
    </ul>
    
    <?php if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin'): ?>
        <div style="background: #ffcccc; padding: 10px; margin: 20px 0; border-left: 4px solid red;">
            <strong>⚠️ PROBLEMA DETECTADO:</strong><br>
            <?php if (!isset($_SESSION['id_usuario'])): ?>
                - No hay sesión iniciada. <a href="/portalBolsaTrabajoUniversitario/views/login.php">Ir al login</a><br>
            <?php endif; ?>
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] !== 'admin'): ?>
                - Tu rol es "<?php echo $_SESSION['rol']; ?>" pero se requiere "admin"<br>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div style="background: #ccffcc; padding: 10px; margin: 20px 0; border-left: 4px solid green;">
            <strong>✓ TODO CORRECTO:</strong> Puedes acceder a las páginas de admin.<br>
            <a href="/portalBolsaTrabajoUniversitario/views/admin/empresas.php">Ir a Gestión de Empresas</a>
        </div>
    <?php endif; ?>
</body>
</html>