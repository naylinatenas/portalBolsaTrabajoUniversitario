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
    if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'estudiante') {
        header("Location: ../views/errores/acceso_denegado.php");
        exit;
    }
    $usuario_id = $_SESSION['id_usuario'];
    $est = $edao->obtenerPorUsuario($usuario_id);
    if (!$est) {
        $_SESSION['error'] = "Completa tu perfil antes de postular.";
        header("Location: ../views/estudiante/perfil.php");
        exit;
    }
    $oferta_id = intval($_POST['oferta_id']);
    if ($pdao->existe($oferta_id, $est->id_estudiante)) {
        $_SESSION['error'] = "Ya postulaste a esta oferta.";
        header("Location: ../views/estudiante/ofertas.php");
        exit;
    }
    $p = new Postulacion([
        'oferta_id' => $oferta_id,
        'estudiante_id' => $est->id_estudiante,
        'comentario_empresa' => null
    ]);
    $pdao->crear($p);
    $_SESSION['success'] = "Postulación enviada.";
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
