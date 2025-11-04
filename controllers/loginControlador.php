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

if (!$user) {
    $_SESSION['error'] = "Usuario no encontrado.";
    header("Location: ../views/login.php");
    exit;
}

$claveOk = false;

if (!empty($user['clave'])) {
    if (password_verify($clave, $user['clave'])) {
        $claveOk = true;
    } elseif (hash('sha256', $clave) === strtolower($user['clave'])) { 
        $claveOk = true;
    }
}

// DEBUG TEMPORAL
// file_put_contents(__DIR__ . '/../debug_login.txt', 
//     "Ingresado: " . $clave . PHP_EOL .
//     "SHA256(ingresado): " . hash('sha256', $clave) . PHP_EOL .
//     "Clave BD: " . $user['clave'] . PHP_EOL,
// FILE_APPEND);


if (!$claveOk) {
    $_SESSION['error'] = "Credenciales inválidas.";
    header("Location: ../views/login.php");
    exit;
}


// Crear sesión
$_SESSION['id_usuario'] = $user['id_usuario'];
$_SESSION['rol'] = $user['rol'];
$_SESSION['nombre'] = $user['nombre_completo'];

// Cookie de recordar correo
if ($recordar) {
    setcookie('correo_recordado', $correo, time() + 7*24*3600, "/");
} else {
    setcookie('correo_recordado', '', time() - 3600, "/");
}

// Redirección según rol
switch ($user['rol']) {
    case 'admin':
        header("Location: ../views/admin/dashboard.php");
        break;
    case 'empresa':
        header("Location: ../views/empresa/dashboard.php");
        break;
    case 'estudiante':
        header("Location: ../views/estudiante/dashboard.php");
        break;
    default:
        header("Location: ../views/login.php");
        break;
}

exit;
