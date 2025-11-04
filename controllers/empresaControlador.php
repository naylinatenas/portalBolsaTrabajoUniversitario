<?php
// controllers/empresaControlador.php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'empresa') {
    header("Location: ../views/errores/acceso_denegado.php");
    exit;
}

require_once __DIR__ . '/../models/EmpresaDAO.php';
require_once __DIR__ . '/../models/OfertaDAO.php';
require_once __DIR__ . '/../models/UsuarioDAO.php';

$empresaDAO = new EmpresaDAO();
$ofertaDAO = new OfertaDAO();

$action = $_POST['action'] ?? $_GET['action'] ?? null;
$usuario_id = $_SESSION['id_usuario'];

// Buscar empresa asociada al usuario
$empresa = null;
foreach ($empresaDAO->listar() as $e) {
    if ($e['usuario_id'] == $usuario_id) {
        $empresa = $e;
        break;
    }
}
if (!$empresa) {
    $_SESSION['error'] = "Empresa no encontrada o pendiente de aprobación.";
    header("Location: ../views/empresa/dashboard.php");
    exit;
}
$empresa_id = $empresa['id_empresa'];

// Crear nueva oferta
if ($action === 'crear_oferta' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $o = new Oferta();
    $o->empresa_id = $empresa_id;
    $o->titulo = $_POST['titulo'];
    $o->descripcion = $_POST['descripcion'];
    $o->tipo = $_POST['tipo'];
    $o->salario_referencial = $_POST['salario'];
    $o->modalidad = $_POST['modalidad'];
    $o->fecha_cierre = $_POST['fecha_cierre'];
    $ofertaDAO->crear($o);
    header("Location: ../views/empresa/mis_ofertas.php");
    exit;
}

// Editar oferta
if ($action === 'editar_oferta' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $o = new Oferta();
    $o->id_oferta = $_POST['id_oferta'];
    $o->titulo = $_POST['titulo'];
    $o->descripcion = $_POST['descripcion'];
    $o->tipo = $_POST['tipo'];
    $o->salario_referencial = $_POST['salario'];
    $o->modalidad = $_POST['modalidad'];
    $o->fecha_cierre = $_POST['fecha_cierre'];
    $o->estado_oferta = $_POST['estado'];
    $ofertaDAO->actualizar($o);
    header("Location: ../views/empresa/mis_ofertas.php");
    exit;
}

// Eliminar oferta
if ($action === 'eliminar_oferta' && isset($_GET['id'])) {
    $ofertaDAO->eliminar($_GET['id']);
    header("Location: ../views/empresa/mis_ofertas.php");
    exit;
}

header("Location: ../views/empresa/dashboard.php");
exit;
