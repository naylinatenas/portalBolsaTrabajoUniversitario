<?php
// controllers/ofertaControlador.php
session_start();
require_once __DIR__ . '/../models/OfertaDAO.php';
$dao = new OfertaDAO();

$action = $_GET['action'] ?? null;

// Listar ofertas activas
if ($action === 'listar') {
    $ofertas = $dao->listarActivas();
    header('Content-Type: application/json');
    echo json_encode($ofertas);
    exit;
}

// Detalle de oferta
if ($action === 'detalle' && isset($_GET['id'])) {
    $o = $dao->obtenerPorId($_GET['id']);
    header('Content-Type: application/json');
    echo json_encode($o);
    exit;
}

header("Location: ../views/estudiante/ofertas.php");
exit;
