<?php
// controllers/ofertaControlador.php
session_start();

// DEBUG - Temporal
error_log("=== OFERTA CONTROLADOR ===");
error_log("Action: " . ($_POST['action'] ?? $_GET['action'] ?? 'NINGUNA'));
error_log("Rol: " . ($_SESSION['rol'] ?? 'NO DEFINIDO'));

require_once __DIR__ . '/../models/OfertaDAO.php';
$dao = new OfertaDAO();

$action = $_POST['action'] ?? $_GET['action'] ?? null;

// ==========================================
// ACCIONES PARA ADMINISTRADORES
// ==========================================
if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
    
    switch ($action) {
        case 'crear_oferta':
            try {
                error_log("Intentando crear oferta...");
                $datos = [
                    'empresa_id' => $_POST['empresa_id'],
                    'titulo' => $_POST['titulo'],
                    'descripcion' => $_POST['descripcion'],
                    'tipo' => $_POST['tipo'],
                    'modalidad' => $_POST['modalidad'],
                    'salario_referencial' => $_POST['salario_referencial'],
                    'fecha_publicacion' => $_POST['fecha_publicacion'],
                    'fecha_cierre' => $_POST['fecha_cierre'],
                    'estado_oferta' => $_POST['estado_oferta']
                ];
                
                error_log("Datos: " . print_r($datos, true));
                
                if ($dao->crearDesdeArray($datos)) {
                    $_SESSION['mensaje'] = 'Oferta creada exitosamente';
                    $_SESSION['tipo_mensaje'] = 'success';
                    error_log("Oferta creada con éxito");
                } else {
                    $_SESSION['mensaje'] = 'Error al crear la oferta';
                    $_SESSION['tipo_mensaje'] = 'danger';
                    error_log("Error al crear oferta");
                }
            } catch (Exception $e) {
                $_SESSION['mensaje'] = 'Error: ' . $e->getMessage();
                $_SESSION['tipo_mensaje'] = 'danger';
                error_log("Excepción al crear: " . $e->getMessage());
            }
            header('Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/admin/ofertas.php');
            exit;

            case 'editar_oferta':
                try {
                    error_log("Intentando editar oferta...");
                    $datos = [
                        'id_oferta' => $_POST['id_oferta'],
                        'empresa_id' => $_POST['empresa_id'],  // ← NUEVO
                        'titulo' => $_POST['titulo'],
                        'tipo' => $_POST['tipo'],
                        'modalidad' => $_POST['modalidad'],
                        'salario_referencial' => $_POST['salario_referencial'],
                        'estado_oferta' => $_POST['estado_oferta']
                    ];
                    
                    error_log("Datos: " . print_r($datos, true));
                    
                    if ($dao->actualizarDesdeArray($datos)) {
                        $_SESSION['mensaje'] = 'Oferta actualizada exitosamente';
                        $_SESSION['tipo_mensaje'] = 'success';
                        error_log("Oferta actualizada con éxito");
                    } else {
                        $_SESSION['mensaje'] = 'Error al actualizar la oferta';
                        $_SESSION['tipo_mensaje'] = 'danger';
                        error_log("Error al actualizar oferta");
                    }
                } catch (Exception $e) {
                    $_SESSION['mensaje'] = 'Error: ' . $e->getMessage();
                    $_SESSION['tipo_mensaje'] = 'danger';
                    error_log("Excepción al actualizar: " . $e->getMessage());
                }
                header('Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/admin/ofertas.php');
                exit;

        case 'eliminar_oferta':
            try {
                error_log("Intentando eliminar oferta...");
                $id = $_POST['id_oferta'] ?? $_GET['id'] ?? null;
                
                error_log("ID a eliminar: " . $id);
                
                if ($id && $dao->eliminar($id)) {
                    $_SESSION['mensaje'] = 'Oferta eliminada exitosamente';
                    $_SESSION['tipo_mensaje'] = 'success';
                    error_log("Oferta eliminada con éxito");
                } else {
                    $_SESSION['mensaje'] = 'Error al eliminar la oferta';
                    $_SESSION['tipo_mensaje'] = 'danger';
                    error_log("Error al eliminar oferta");
                }
            } catch (Exception $e) {
                $_SESSION['mensaje'] = 'Error: ' . $e->getMessage();
                $_SESSION['tipo_mensaje'] = 'danger';
                error_log("Excepción al eliminar: " . $e->getMessage());
            }
            header('Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/admin/ofertas.php');
            exit;
    }
}

// ==========================================
// ACCIONES PÚBLICAS (ESTUDIANTES/VISITANTES)
// ==========================================

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

// Redirección por defecto
error_log("Sin acción específica, redirigiendo...");
if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
    header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/admin/ofertas.php");
} else {
    header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/estudiante/ofertas.php");
}
exit;