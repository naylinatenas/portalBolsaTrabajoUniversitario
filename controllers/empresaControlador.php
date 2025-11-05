<?php
// controllers/empresaControlador.php
session_start();

// Verificar que el usuario esté autenticado y sea empresa
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'empresa') {
    header("Location: ../views/errores/acceso_denegado.php");
    exit;
}

require_once __DIR__ . '/../models/EmpresaDAO.php';
require_once __DIR__ . '/../models/OfertaDAO.php';
require_once __DIR__ . '/../models/Oferta.php';

$empresaDAO = new EmpresaDAO();
$ofertaDAO = new OfertaDAO();

$action = $_POST['action'] ?? $_GET['action'] ?? null;
$usuario_id = $_SESSION['id_usuario'];

// Buscar empresa asociada al usuario
$empresa = $empresaDAO->obtenerPorUsuario($usuario_id);

if (!$empresa) {
    $_SESSION['mensaje'] = "Empresa no encontrada o pendiente de aprobación.";
    $_SESSION['tipo_mensaje'] = "warning";
    header("Location: ../views/empresa/dashboard.php");
    exit;
}

$empresa_id = $empresa->id_empresa;

// Verificar que la empresa esté aprobada
if ($empresa->estado !== 'aprobada') {
    $_SESSION['mensaje'] = "Su empresa está en estado: " . $empresa->estado . ". Espere la aprobación del administrador.";
    $_SESSION['tipo_mensaje'] = "warning";
    header("Location: ../views/empresa/dashboard.php");
    exit;
}

// Crear nueva oferta
if ($action === 'crear_oferta' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $o = new Oferta();
        $o->empresa_id = $empresa_id;
        $o->titulo = $_POST['titulo'];
        $o->descripcion = $_POST['descripcion'];
        $o->tipo = $_POST['tipo'];
        $o->salario_referencial = $_POST['salario'] ?? null;
        $o->modalidad = $_POST['modalidad'];
        $o->fecha_cierre = $_POST['fecha_cierre'];
        $o->estado_oferta = 'activa';
        
        $ofertaDAO->crear($o);
        
        $_SESSION['mensaje'] = 'Oferta creada correctamente';
        $_SESSION['tipo_mensaje'] = 'success';
    } catch (Exception $e) {
        $_SESSION['mensaje'] = 'Error al crear oferta: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    
    header("Location: ../views/empresa/mis_ofertas.php");
    exit;
}

// Editar oferta
if ($action === 'editar_oferta' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Verificar que la oferta pertenezca a la empresa
        $ofertaActual = $ofertaDAO->obtenerPorId($_POST['id_oferta']);
        
        if (!$ofertaActual || $ofertaActual->empresa_id != $empresa_id) {
            $_SESSION['mensaje'] = 'No tiene permisos para editar esta oferta';
            $_SESSION['tipo_mensaje'] = 'danger';
            header("Location: ../views/empresa/mis_ofertas.php");
            exit;
        }
        
        $o = new Oferta();
        $o->id_oferta = $_POST['id_oferta'];
        $o->empresa_id = $empresa_id;
        $o->titulo = $_POST['titulo'];
        $o->descripcion = $_POST['descripcion'];
        $o->tipo = $_POST['tipo'];
        $o->salario_referencial = $_POST['salario'] ?? null;
        $o->modalidad = $_POST['modalidad'];
        $o->fecha_cierre = $_POST['fecha_cierre'];
        $o->estado_oferta = $_POST['estado'] ?? 'activa';
        
        $ofertaDAO->actualizar($o);
        
        $_SESSION['mensaje'] = 'Oferta actualizada correctamente';
        $_SESSION['tipo_mensaje'] = 'success';
    } catch (Exception $e) {
        $_SESSION['mensaje'] = 'Error al actualizar oferta: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    
    header("Location: ../views/empresa/mis_ofertas.php");
    exit;
}

// Eliminar oferta
if ($action === 'eliminar_oferta' && isset($_GET['id'])) {
    try {
        // Verificar que la oferta pertenezca a la empresa
        $ofertaActual = $ofertaDAO->obtenerPorId($_GET['id']);
        
        if (!$ofertaActual || $ofertaActual->empresa_id != $empresa_id) {
            $_SESSION['mensaje'] = 'No tiene permisos para eliminar esta oferta';
            $_SESSION['tipo_mensaje'] = 'danger';
            header("Location: ../views/empresa/mis_ofertas.php");
            exit;
        }
        
        $ofertaDAO->eliminar($_GET['id']);
        
        $_SESSION['mensaje'] = 'Oferta eliminada correctamente';
        $_SESSION['tipo_mensaje'] = 'success';
    } catch (Exception $e) {
        $_SESSION['mensaje'] = 'Error al eliminar oferta: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    
    header("Location: ../views/empresa/mis_ofertas.php");
    exit;
}

// Actualizar perfil de empresa
if ($action === 'actualizar_perfil' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $empresa->razon_social = $_POST['razon_social'];
        $empresa->ruc = $_POST['ruc'] ?? '';
        $empresa->direccion = $_POST['direccion'] ?? '';
        $empresa->telefono = $_POST['telefono'] ?? '';
        $empresa->correo_contacto = $_POST['correo_contacto'];
        
        $empresaDAO->actualizar($empresa);
        
        $_SESSION['mensaje'] = 'Perfil actualizado correctamente';
        $_SESSION['tipo_mensaje'] = 'success';
    } catch (Exception $e) {
        $_SESSION['mensaje'] = 'Error al actualizar perfil: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    
    header("Location: ../views/empresa/perfil.php");
    exit;
}

// Si no hay acción válida, redirigir al dashboard
$_SESSION['mensaje'] = 'Acción no válida';
$_SESSION['tipo_mensaje'] = 'warning';
header("Location: ../views/empresa/dashboard.php");
exit;