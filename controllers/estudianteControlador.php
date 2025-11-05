<?php
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

    $codigo = $_POST['codigo_estudiante'];
    $carrera = $_POST['carrera'];
    $ciclo = $_POST['ciclo'];
    $resumen = $_POST['resumen_perfil'];

    $carpeta = __DIR__ . '/../uploads/cv/';
    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0777, true);
    }

    // Mantener CV actual si no se sube uno nuevo
    $cv_url = $est ? $est->cv_url : null; 

    // ============================
    // VALIDACIÓN DEL ARCHIVO CV
    // ============================
    if (!empty($_FILES['cv']['name'])) {

        $archivo = $_FILES['cv'];
        $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $tamaño = $archivo['size'];
        $tipo = mime_content_type($archivo['tmp_name']);

        // Solo PDF
        if ($ext !== 'pdf' || $tipo !== 'application/pdf') {
            $_SESSION['error_cv'] = '❌ Solo se permiten archivos PDF válidos.';
            header("Location: ../views/estudiante/perfil.php");
            exit;
        }

        // 5 MB máximo
        if ($tamaño > 5 * 1024 * 1024) {
            $_SESSION['error_cv'] = '❌ El archivo excede el tamaño máximo permitido (5MB).';
            header("Location: ../views/estudiante/perfil.php");
            exit;
        }

        // Nombre único
        $nombreArchivo = 'cv_' . $usuario_id . '_' . time() . '.pdf';
        $rutaDestino = $carpeta . $nombreArchivo;

        // Eliminar archivo anterior si existe
        if ($est && !empty($est->cv_url) && file_exists(__DIR__ . '/..' . $est->cv_url)) {
            unlink(__DIR__ . '/..' . $est->cv_url);
        }

        move_uploaded_file($archivo['tmp_name'], $rutaDestino);

        $cv_url = '/uploads/cv/' . $nombreArchivo;
    }

    // ============================
    // GUARDAR O ACTUALIZAR REGISTRO
    // ============================

    if ($est) {
        // Editar estudiante
        $est->codigo_estudiante = $codigo;
        $est->carrera = $carrera;
        $est->ciclo = $ciclo;
        $est->resumen_perfil = $resumen;
        $est->cv_url = $cv_url;
        $estDAO->actualizar($est);
    } else {
        // Crear nuevo estudiante
        $nuevo = new Estudiante([
            'codigo_estudiante' => $codigo,
            'carrera' => $carrera,
            'ciclo' => $ciclo,
            'cv_url' => $cv_url,
            'resumen_perfil' => $resumen
        ]);
        $nuevo->usuario_id = $usuario_id;
        $estDAO->crear($nuevo);
    }

    // ✅ Mensaje de éxito
    $_SESSION['success_msg'] = '✅ Perfil actualizado correctamente.';

    header("Location: ../views/estudiante/perfil.php");
    exit;
}

// Si no hay acción válida, redirige al dashboard
header("Location: ../views/estudiante/dashboard.php");
exit;
