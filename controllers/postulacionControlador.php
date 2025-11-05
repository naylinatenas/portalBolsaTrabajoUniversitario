<?php
// controllers/postulacionControlador.php
session_start();
require_once __DIR__ . '/../models/PostulacionDAO.php';
require_once __DIR__ . '/../models/EstudianteDAO.php';

$pdao = new PostulacionDAO();
$edao = new EstudianteDAO();

$action = $_POST['action'] ?? $_GET['action'] ?? null;

// Postular a oferta
if ($action === 'postular' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validar sesión y rol
    if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'estudiante') {
        header("Location: ../views/errores/acceso_denegado.php");
        exit;
    }

    $usuario_id = $_SESSION['id_usuario'];
    $est = $edao->obtenerPorUsuario($usuario_id);

    // Validar que el estudiante tenga perfil creado
    if (!$est) {
        $_SESSION['error_msg'] = "⚠ Debes completar tu perfil antes de postular.";
        header("Location: ../views/estudiante/perfil.php");
        exit;
    }

    // Validar oferta
    $oferta_id = intval($_POST['oferta_id']);
    if ($oferta_id <= 0) {
        $_SESSION['error_msg'] = "❌ Oferta inválida.";
        header("Location: ../views/estudiante/ofertas.php");
        exit;
    }

    // Evitar doble postulación
    if ($pdao->existe($oferta_id, $est->id_estudiante)) {
        $_SESSION['error_msg'] = "⚠ Ya te has postulado a esta oferta.";
        header("Location: ../views/estudiante/oferta_detalle.php?id=$oferta_id");
        exit;
    }

    // Registrar postulación
    $p = new Postulacion([
        'oferta_id' => $oferta_id,
        'estudiante_id' => $est->id_estudiante,
        'comentario_empresa' => null
    ]);
    $pdao->crear($p);

    $_SESSION['success_msg'] = "✅ Tu postulación ha sido enviada correctamente.";
    header("Location: ../views/estudiante/historial_postulaciones.php");
    exit;
}

if ($action === 'anular' && isset($_GET['id'])) {
    if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'estudiante') {
        header("Location: ../views/errores/acceso_denegado.php");
        exit;
    }

    $id_postulacion = intval($_GET['id']);
    $pdao->eliminarPostulacion($id_postulacion);

    $_SESSION['success'] = "Postulación anulada correctamente.";
    header("Location: ../views/estudiante/historial_postulaciones.php");
    exit;
}



// Cambiar estado (empresa/admin)
if ($action === 'cambiar_estado' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id_postulacion']);
    $estado = $_POST['estado'];
    $coment = $_POST['comentario'] ?? null;
    $pdao->cambiarEstado($id, $estado, $coment);
    header("Location: " . ($_POST['return'] ?? '../views/admin/reportes.php'));
    exit;
}

header("Location: ../index.php");
exit;
