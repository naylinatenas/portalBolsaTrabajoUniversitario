<?php
// controllers/postulacionControlador.php
session_start();

// Habilitar errores para debugging (comentar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../models/PostulacionDAO.php';
require_once __DIR__ . '/../models/EstudianteDAO.php';
require_once __DIR__ . '/../models/Postulacion.php';

$pdao = new PostulacionDAO();
$edao = new EstudianteDAO();

$action = $_POST['action'] ?? $_GET['action'] ?? null;

// ==========================================
// ACCIONES PARA ESTUDIANTES
// ==========================================

// Postular a oferta
if ($action === 'postular' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validar sesión y rol
    if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'estudiante') {
        $_SESSION['mensaje'] = 'Debes iniciar sesión como estudiante para postular';
        $_SESSION['tipo_mensaje'] = 'warning';
        header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/login.php");
        exit;
    }

    $usuario_id = $_SESSION['id_usuario'];
    $est = $edao->obtenerPorUsuario($usuario_id);

    // Validar que el estudiante tenga perfil creado
    if (!$est) {
        $_SESSION['mensaje'] = '⚠ Debes completar tu perfil antes de postular';
        $_SESSION['tipo_mensaje'] = 'warning';
        header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/estudiante/perfil.php");
        exit;
    }

    // Validar oferta
    $oferta_id = intval($_POST['oferta_id']);
    if ($oferta_id <= 0) {
        $_SESSION['mensaje'] = '❌ Oferta inválida';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/estudiante/ofertas.php");
        exit;
    }

    // Evitar doble postulación
    if ($pdao->yaPostulo($est->id_estudiante, $oferta_id)) {
        $_SESSION['mensaje'] = '⚠ Ya te has postulado a esta oferta';
        $_SESSION['tipo_mensaje'] = 'warning';
        header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/estudiante/ofertas.php");
        exit;
    }

    // Registrar postulación
    try {
        $p = new Postulacion([
            'oferta_id' => $oferta_id,
            'estudiante_id' => $est->id_estudiante,
            'comentario_empresa' => null
        ]);
        
        if ($pdao->crear($p)) {
            $_SESSION['mensaje'] = '✅ Tu postulación ha sido enviada correctamente';
            $_SESSION['tipo_mensaje'] = 'success';
            header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/estudiante/mis_postulaciones.php");
        } else {
            $_SESSION['mensaje'] = '❌ Error al enviar la postulación';
            $_SESSION['tipo_mensaje'] = 'danger';
            header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/estudiante/ofertas.php");
        }
    } catch (Exception $e) {
        error_log("Error al crear postulación: " . $e->getMessage());
        $_SESSION['mensaje'] = '❌ Error al procesar la postulación';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/estudiante/ofertas.php");
    }
    exit;
}

// Anular postulación (estudiante)
if ($action === 'anular' && isset($_GET['id'])) {
    if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'estudiante') {
        $_SESSION['mensaje'] = 'Acceso denegado';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/login.php");
        exit;
    }

    $id_postulacion = intval($_GET['id']);
    
    if ($pdao->eliminar($id_postulacion)) {
        $_SESSION['mensaje'] = 'Postulación anulada correctamente';
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'Error al anular la postulación';
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    
    header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/estudiante/mis_postulaciones.php");
    exit;
}

// ==========================================
// ACCIONES PARA EMPRESAS
// ==========================================

// Aceptar postulación
if ($action === 'aceptar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'empresa') {
        $_SESSION['mensaje'] = 'Acceso denegado';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/index.php");
        exit;
    }

    $id = intval($_POST['id_postulacion']);
    
    if ($pdao->cambiarEstado($id, 'aceptada')) {
        $_SESSION['mensaje'] = 'Postulación aceptada exitosamente';
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'Error al aceptar la postulación';
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    
    header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/empresa/postulaciones_recibidas.php");
    exit;
}

// Rechazar postulación
if ($action === 'rechazar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'empresa') {
        $_SESSION['mensaje'] = 'Acceso denegado';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/index.php");
        exit;
    }

    $id = intval($_POST['id_postulacion']);
    
    if ($pdao->cambiarEstado($id, 'rechazada')) {
        $_SESSION['mensaje'] = 'Postulación rechazada';
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'Error al rechazar la postulación';
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    
    header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/empresa/postulaciones_recibidas.php");
    exit;
}

// ==========================================
// ACCIONES PARA ADMINISTRADORES
// ==========================================

// Cambiar estado (admin con comentario)
if ($action === 'cambiar_estado' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
        $_SESSION['mensaje'] = 'Acceso denegado';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/index.php");
        exit;
    }

    $id = intval($_POST['id_postulacion']);
    $estado = $_POST['estado'];
    $comentario = $_POST['comentario'] ?? null;
    
    if ($pdao->cambiarEstado($id, $estado)) {
        if ($comentario) {
            $pdao->actualizarComentario($id, $comentario);
        }
        $_SESSION['mensaje'] = 'Estado actualizado correctamente';
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'Error al actualizar el estado';
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    
    $return = $_POST['return'] ?? '/PORTALBOLSATRABAJOUNIVERSITARIO/views/admin/postulaciones.php';
    header("Location: $return");
    exit;
}

// Eliminar postulación (admin)
if ($action === 'eliminar' && isset($_GET['id'])) {
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
        $_SESSION['mensaje'] = 'Acceso denegado';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/index.php");
        exit;
    }

    $id = intval($_GET['id']);
    
    if ($pdao->eliminar($id)) {
        $_SESSION['mensaje'] = 'Postulación eliminada correctamente';
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'Error al eliminar la postulación';
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    
    header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/admin/postulaciones.php");
    exit;
}

// ==========================================
// REDIRECCIÓN POR DEFECTO
// ==========================================
error_log("Postulación Controlador: Acción no válida - $action");

if (isset($_SESSION['rol'])) {
    switch ($_SESSION['rol']) {
        case 'admin':
            header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/admin/postulaciones.php");
            break;
        case 'empresa':
            header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/empresa/postulaciones_recibidas.php");
            break;
        case 'estudiante':
            header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/estudiante/mis_postulaciones.php");
            break;
        default:
            header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/index.php");
    }
} else {
    header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/index.php");
}
exit;