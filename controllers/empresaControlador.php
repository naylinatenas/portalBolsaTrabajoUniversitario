<?php
// controlador/empresaControlador.php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'empresa') {
    header("Location: ../vista/errores/acceso_denegado.php"); exit;
}

require_once __DIR__ . '/../modelo/EmpresaDAO.php';
require_once __DIR__ . '/../modelo/OfertaDAO.php';

$empresaDAO = new EmpresaDAO();
$ofertaDAO = new OfertaDAO();

$action = $_POST['action'] ?? $_GET['action'] ?? null;
$usuario_id = $_SESSION['id_usuario'];

// obtener empresa asociada al usuario logueado
$empresa = null;
$empresas = $empresaDAO->listar();
foreach ($empresas as $e) {
    if ($e['usuario_id'] == $usuario_id) { $empresa = $e; break; }
}
if (!$empresa) { $_SESSION['error']="Empresa no encontrada o pendiente."; header("Location: ../vista/empresa/dashboard.php"); exit; }

$empresa_id = $empresa['id_empresa'];

if ($action === 'crear_oferta') {
    $o = new stdClass();
    $o->empresa_id = $empresa_id;
    $o->titulo = $_POST['titulo'];
    $o->descripcion = $_POST['descripcion'];
    $o->tipo = $_POST['tipo'];
    $o->salario_referencial = $_POST['salario'];
    $o->modalidad = $_POST['modalidad'];
    $o->fecha_cierre = $_POST['fecha_cierre'];
    $ofertaDAO->crear($o);
    header("Location: ../vista/empresa/mis_ofertas.php"); exit;
}

if ($action === 'editar_oferta') {
    $o = new stdClass();
    $o->id_oferta = $_POST['id_oferta'];
    $o->titulo = $_POST['titulo'];
    $o->descripcion = $_POST['descripcion'];
    $o->tipo = $_POST['tipo'];
    $o->salario_referencial = $_POST['salario'];
    $o->modalidad = $_POST['modalidad'];
    $o->fecha_cierre = $_POST['fecha_cierre'];
    $o->estado_oferta = $_POST['estado'] ?? 'activa';
    $ofertaDAO->actualizar($o);
    header("Location: ../vista/empresa/mis_ofertas.php"); exit;
}

if ($action === 'eliminar_oferta' && isset($_GET['id'])) {
    $ofertaDAO->eliminar($_GET['id']);
    header("Location: ../vista/empresa/mis_ofertas.php"); exit;
}

if ($action === 'cambiar_estado' && isset($_GET['id']) && isset($_GET['estado'])) {
    $ofertaDAO->cambiarEstado($_GET['id'], $_GET['estado']);
    header("Location: ../vista/empresa/mis_ofertas.php"); exit;
}
