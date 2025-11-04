<?php
// controlador/loginControlador.php
session_start();
require_once __DIR__ . '/../modelo/UsuarioDAO.php';

$action = $_POST['action'] ?? null;

if ($action === 'login') {
    $correo = $_POST['correo'] ?? '';
    $clave = $_POST['clave'] ?? '';
    $recordar = isset($_POST['recordar']);

    $udao = new UsuarioDAO();
    $user = $udao->obtenerPorCorreo($correo);
    if ($user && password_verify($clave, $user->clave)) {
        if ($user->estado == 0) {
            $_SESSION['error'] = "Usuario inactivo.";
            header("Location: ../vista/login.php"); exit;
        }
        $_SESSION['id_usuario'] = $user->id_usuario;
        $_SESSION['rol'] = $user->rol;
        $_SESSION['nombre'] = $user->nombre_completo;

        if ($recordar) {
            setcookie('correo_recordado', $correo, time() + 7*24*3600, "/");
        } else {
            setcookie('correo_recordado', '', time() - 3600, "/");
        }

        // redirigir por rol
        if ($user->rol === 'admin') header("Location: ../vista/admin/dashboard.php");
        elseif ($user->rol === 'empresa') header("Location: ../vista/empresa/dashboard.php");
        else header("Location: ../vista/estudiante/dashboard.php");
        exit;
    } else {
        $_SESSION['error'] = "Credenciales inválidas.";
        header("Location: ../vista/login.php"); exit;
    }
}
