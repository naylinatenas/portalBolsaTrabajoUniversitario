<?php
// controllers/adminControlador.php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../views/errores/acceso_denegado.php");
    exit;
}

require_once __DIR__ . '/../models/EmpresaDAO.php';
require_once __DIR__ . '/../models/OfertaDAO.php';
require_once __DIR__ . '/../models/PostulacionDAO.php';

$empresaDAO = new EmpresaDAO();
$ofertaDAO = new OfertaDAO();
$postDAO = new PostulacionDAO();

$action = $_GET['action'] ?? $_POST['action'] ?? null;

// Aprobar o rechazar empresa
if ($action === 'aprobar' && isset($_GET['id'])) {
    $empresaDAO->cambiarEstado($_GET['id'], 'aprobada');
    header("Location: ../views/admin/empresas.php");
    exit;
}
if ($action === 'rechazar' && isset($_GET['id'])) {
    $empresaDAO->cambiarEstado($_GET['id'], 'rechazada');
    header("Location: ../views/admin/empresas.php");
    exit;
}

// Eliminar empresa
if ($action === 'eliminar_empresa' && isset($_GET['id'])) {
    $empresaDAO->eliminar($_GET['id']);
    header("Location: ../views/admin/empresas.php");
    exit;
}

// Eliminar oferta
if ($action === 'eliminar_oferta' && isset($_GET['id'])) {
    $ofertaDAO->eliminar($_GET['id']);
    header("Location: ../views/admin/ofertas.php");
    exit;
}

header("Location: ../views/admin/dashboard.php");
exit;
