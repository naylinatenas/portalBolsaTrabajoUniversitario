<?php
// controllers/estudianteControlador.php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'estudiante') {
    header("Location: ../views/errores/acceso_denegado.php");
    exit;
}

require_once __DIR__ . '/../models/EstudianteDAO.php';
$estDAO = new EstudianteDAO();

$action = $_POST['action'] ?? null;
$usuario_id = $_SESSION['id_usuario'];

if ($action === 'guardar_perfil') {
    $est = $estDAO->obtenerPorUsuario($usuario_id);
    if ($est) {
        $est->codigo_estudiante = $_POST['codigo_estudiante'];
        $est->carrera = $_POST['carrera'];
        $est->ciclo = $_POST['ciclo'];
        $est->resumen_perfil = $_POST['resumen_perfil'];
        if (!empty($_FILES['cv']['name'])) {
            $ruta = __DIR__ . '/../public/uploads/';
            if (!is_dir($ruta)) mkdir($ruta, 0755, true);
            $destino = $ruta . basename($_FILES['cv']['name']);
            move_uploaded_file($_FILES['cv']['tmp_name'], $destino);
            $est->cv_url = '/public/uploads/' . basename($_FILES['cv']['name']);
        }
        $estDAO->actualizar($est);
    } else {
        $nuevo = new Estudiante($_POST);
        $nuevo->usuario_id = $usuario_id;
        $estDAO->crear($nuevo);
    }
    header("Location: ../views/estudiante/perfil.php");
    exit;
}

header("Location: ../views/estudiante/dashboard.php");
exit;
