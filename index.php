<?php
// index.php
session_start();
if (isset($_SESSION['id_usuario'])) {
    $rol = $_SESSION['rol'];
    if ($rol === 'admin') header("Location: views/admin/dashboard.php");
    elseif ($rol === 'empresa') header("Location: views/empresa/dashboard.php");
    else header("Location: views/estudiante/dashboard.php");
    exit;
} else {
    header("Location: views/login.php");
    exit;
}
