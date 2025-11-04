<?php
// controllers/loginControlador.php
session_start();
require_once __DIR__ . '/../models/UsuarioDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/layout/login.php");
    exit;
}

$correo = trim($_POST['correo'] ?? '');
$clave = $_POST['clave'] ?? '';
$recordar = isset($_POST['recordar']);

$ud = new UsuarioDAO();
$user = $ud->obtenerPorCorreo($correo);

if (!$user || !password_verify($clave, $user->clave)) {
    $_SESSION['error'] = "Credenciales inválidas.";
    header("Location: ../views/layout/login.php");
    exit;
}

if ($user->estado == 0) {
    $_SESSION['error'] = "Usuario inactivo.";
    header("Location: ../views/layout/login.php");
    exit;
}

// Crear sesión
$_SESSION['id_usuario'] = $user->id_usuario;
$_SESSION['rol'] = $user->rol;
$_SESSION['nombre'] = $user->nombre_completo;

// Cookie de recordar correo
if ($recordar) {
    setcookie('correo_recordado', $correo, time() + 7*24*3600, "/");
} else {
    setcookie('correo_recordado', '', time() - 3600, "/");
}

// Redirección según rol
switch ($user->rol) {
    case 'admin':
        header("Location: ../views/admin/dashboard.php"); break;
    case 'empresa':
        header("Location: ../views/empresa/dashboard.php"); break;
    case 'estudiante':
        header("Location: ../views/estudiante/dashboard.php"); break;
    default:
        header("Location: ../views/layout/login.php"); break;
}
exit;
