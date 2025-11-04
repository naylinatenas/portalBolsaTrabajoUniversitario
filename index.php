<?php
// index.php
session_start();
if (isset($_SESSION['id_usuario'])) {
    $rol = $_SESSION['rol'];
    if ($rol === 'admin') header("Location: vista/admin/dashboard.php");
    elseif ($rol === 'empresa') header("Location: vista/empresa/dashboard.php");
    else header("Location: vista/estudiante/dashboard.php");
    exit;
} else {
    header("Location: vista/login.php");
    exit;
}
