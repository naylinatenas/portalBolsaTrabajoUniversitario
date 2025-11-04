<?php
// controlador/adminControlador.php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../vista/errores/acceso_denegado.php");
    exit;
}

require_once __DIR__ . '/../modelo/EmpresaDAO.php';
$empresaDAO = new EmpresaDAO();

$action = $_GET['action'] ?? null;

if ($action === 'aprobar' && isset($_GET['id'])) {
    $empresaDAO->cambiarEstado($_GET['id'], 'aprobada');
    header("Location: ../vista/admin/empresas.php"); exit;
}
if ($action === 'rechazar' && isset($_GET['id'])) {
    $empresaDAO->cambiarEstado($_GET['id'], 'rechazada');
    header("Location: ../vista/admin/empresas.php"); exit;
}
if ($action === 'eliminar' && isset($_GET['id'])) {
    $empresaDAO->eliminar($_GET['id']);
    header("Location: ../vista/admin/empresas.php"); exit;
}

// para listar empresas en la vista incluimos el DAO en la vista directamente o use header redirect
