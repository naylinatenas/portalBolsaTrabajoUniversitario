<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../views/layout/login.php");
    exit;
}

function verificarRol($rolEsperado) {
    if ($_SESSION['rol'] !== $rolEsperado) {
        header("Location: ../views/layout/login.php");
        exit;
    }
}
