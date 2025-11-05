<?php
// controllers/adminControlador.php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../views/errores/acceso_denegado.php");
    exit;
}

require_once __DIR__ . '/../models/EmpresaDAO.php';
require_once __DIR__ . '/../models/Empresa.php';
require_once __DIR__ . '/../models/OfertaDAO.php';
require_once __DIR__ . '/../models/PostulacionDAO.php';

$empresaDAO = new EmpresaDAO();
$ofertaDAO = new OfertaDAO();
$postDAO = new PostulacionDAO();

$action = $_GET['action'] ?? $_POST['action'] ?? null;

// Crear nueva empresa
if ($action === 'crear_empresa' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $empresa = new Empresa([
            'razon_social' => $_POST['razon_social'],
            'ruc' => $_POST['ruc'] ?? '',
            'direccion' => $_POST['direccion'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'correo_contacto' => $_POST['correo_contacto'],
            'usuario_id' => $_SESSION['id_usuario'], // Asignar al admin que lo crea
            'estado' => $_POST['estado'] ?? 'aprobada'
        ]);
        
        $idEmpresa = $empresaDAO->crear($empresa);
        
        $_SESSION['mensaje'] = 'Empresa creada correctamente con ID: ' . $idEmpresa;
        $_SESSION['tipo_mensaje'] = 'success';
    } catch (Exception $e) {
        $_SESSION['mensaje'] = 'Error al crear empresa: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    
    header("Location: ../views/admin/empresas.php");
    exit;
}

// Aprobar empresa
if ($action === 'aprobar' && isset($_GET['id'])) {
    $empresaDAO->cambiarEstado($_GET['id'], 'aprobada');
    $_SESSION['mensaje'] = 'Empresa aprobada correctamente';
    $_SESSION['tipo_mensaje'] = 'success';
    header("Location: ../views/admin/empresas.php");
    exit;
}

// Rechazar empresa
if ($action === 'rechazar' && isset($_GET['id'])) {
    $empresaDAO->cambiarEstado($_GET['id'], 'rechazada');
    $_SESSION['mensaje'] = 'Empresa rechazada';
    $_SESSION['tipo_mensaje'] = 'warning';
    header("Location: ../views/admin/empresas.php");
    exit;
}

// Editar empresa
if ($action === 'editar_empresa' && isset($_POST['id_empresa'])) {
    try {
        // Obtener la empresa actual para mantener los datos no editables
        $empresaActual = $empresaDAO->obtenerPorId($_POST['id_empresa']);
        
        if ($empresaActual) {
            // Actualizar solo los campos editables
            $empresaActual->razon_social = $_POST['razon_social'];
            $empresaActual->correo_contacto = $_POST['correo_contacto'];
            $empresaActual->estado = $_POST['estado'];
            
            // Mantener otros campos si existen en el POST
            if (isset($_POST['ruc'])) $empresaActual->ruc = $_POST['ruc'];
            if (isset($_POST['direccion'])) $empresaActual->direccion = $_POST['direccion'];
            if (isset($_POST['telefono'])) $empresaActual->telefono = $_POST['telefono'];
            
            $empresaDAO->actualizar($empresaActual);
            
            $_SESSION['mensaje'] = 'Empresa actualizada correctamente';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Empresa no encontrada';
            $_SESSION['tipo_mensaje'] = 'danger';
        }
    } catch (Exception $e) {
        $_SESSION['mensaje'] = 'Error al actualizar: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    
    header("Location: ../views/admin/empresas.php");
    exit;
}

// Eliminar empresa (lógico si tiene dependencias, físico si no)
if ($action === 'eliminar_empresa' && isset($_POST['id_empresa'])) {
    try {
        $id = $_POST['id_empresa'];
        
        // Verificar si tiene ofertas asociadas
        if ($empresaDAO->tieneOfertas($id)) {
            // Eliminación lógica (marcar como inactiva)
            $empresaDAO->eliminar($id);
            $_SESSION['mensaje'] = 'Empresa marcada como inactiva (tiene ofertas asociadas)';
            $_SESSION['tipo_mensaje'] = 'warning';
        } else {
            // Eliminación física
            $resultado = $empresaDAO->eliminarFisico($id);
            $_SESSION['mensaje'] = $resultado['message'];
            $_SESSION['tipo_mensaje'] = $resultado['success'] ? 'success' : 'danger';
        }
    } catch (Exception $e) {
        $_SESSION['mensaje'] = 'Error al eliminar: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    
    header("Location: ../views/admin/empresas.php");
    exit;
}

// Eliminar oferta
if ($action === 'eliminar_oferta' && isset($_GET['id'])) {
    try {
        $ofertaDAO->eliminar($_GET['id']);
        $_SESSION['mensaje'] = 'Oferta eliminada correctamente';
        $_SESSION['tipo_mensaje'] = 'success';
    } catch (Exception $e) {
        $_SESSION['mensaje'] = 'Error al eliminar oferta: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    header("Location: ../views/admin/ofertas.php");
    exit;
}

// Si no hay acción válida, redirigir al dashboard
$_SESSION['mensaje'] = 'Acción no válida';
$_SESSION['tipo_mensaje'] = 'warning';
header("Location: ../views/admin/dashboard.php");
exit;