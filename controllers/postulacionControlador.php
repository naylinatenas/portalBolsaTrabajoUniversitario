<?php
// controlador/postulacionControlador.php
session_start();
require_once __DIR__ . '/../modelo/PostulacionDAO.php';
require_once __DIR__ . '/../modelo/EstudianteDAO.php';

$action = $_POST['action'] ?? $_GET['action'] ?? null;
$pdao = new PostulacionDAO();
$estDao = new EstudianteDAO();

if ($action === 'postular') {
    if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'estudiante') {
        header("Location: ../vista/errores/acceso_denegado.php"); exit;
    }
    $usuario_id = $_SESSION['id_usuario'];
    $est = $estDao->obtenerPorUsuarioId($usuario_id);
    if (!$est) { $_SESSION['error'] = "Completa tu perfil de estudiante."; header("Location: ../vista/estudiante/perfil.php"); exit; }
    $oferta_id = $_POST['oferta_id'];
    if ($pdao->existePostulacion($oferta_id, $est->id_estudiante)) {
        $_SESSION['error'] = "Ya postulaste a esta oferta."; header("Location: ../vista/estudiante/ofertas.php"); exit;
    }
    $p = new stdClass();
    $p->oferta_id = $oferta_id;
    $p->estudiante_id = $est->id_estudiante;
    $p->comentario_empresa = "";
    $pdao->crear($p);
    $_SESSION['success'] = "Postulación enviada.";
    header("Location: ../vista/estudiante/historial_postulaciones.php"); exit;
}

if ($action === 'cambiar_estado' && isset($_POST['id'])) {
    // admin/empresa cambiar estado de la postulacion
    $id = $_POST['id'];
    $estado = $_POST['estado'];
    $comentario = $_POST['comentario'] ?? null;
    $pdao->cambiarEstado($id, $estado, $comentario);
    header("Location: " . ($_POST['return'] ?? "../vista/admin/reportes.php"));
    exit;
}
