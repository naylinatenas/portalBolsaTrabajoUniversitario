<?php
// controllers/ofertaControlador.php
session_start();

// DEBUG - Temporal (puedes comentar después)
error_log("=== OFERTA CONTROLADOR ===");
error_log("Action: " . ($_POST['action'] ?? $_GET['action'] ?? 'NINGUNA'));
error_log("Rol: " . ($_SESSION['rol'] ?? 'NO DEFINIDO'));

require_once __DIR__ . '/../models/OfertaDAO.php';
require_once __DIR__ . '/../models/EmpresaDAO.php';

$dao = new OfertaDAO();
$action = $_POST['action'] ?? $_GET['action'] ?? null;

// ==========================================
// ACCIONES PARA ADMINISTRADORES
// ==========================================
if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
    
    switch ($action) {
        case 'crear_oferta':
            try {
                error_log("Admin: Intentando crear oferta...");
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
                error_log("Admin: Intentando editar oferta...");
                $datos = [
                    'id_oferta' => $_POST['id_oferta'],
                    'empresa_id' => $_POST['empresa_id'],
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
                error_log("Admin: Intentando eliminar oferta...");
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
// ACCIONES PARA EMPRESAS
// ==========================================
if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'empresa') {
    
    switch ($action) {
        case 'editar_oferta':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    error_log("Empresa: Intentando editar oferta...");
                    $datos = [
                        'id_oferta' => $_POST['id_oferta'],
                        'empresa_id' => $_POST['empresa_id'],
                        'titulo' => $_POST['titulo'],
                        'tipo' => $_POST['tipo'],
                        'modalidad' => $_POST['modalidad'],
                        'salario_referencial' => $_POST['salario_referencial'],
                        'estado_oferta' => $_POST['estado_oferta']
                    ];

                    error_log("Datos empresa: " . print_r($datos, true));

                    $resultado = $dao->actualizarDesdeArray($datos);

                    if ($resultado) {
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
            }
            header('Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/empresa/mis_ofertas.php');
            exit;

        case 'pausar':
            $id = $_GET['id'] ?? 0;
            error_log("Empresa: Pausando oferta ID: " . $id);
            if ($dao->cambiarEstado($id, 'pausada')) {
                $_SESSION['mensaje'] = 'Oferta pausada correctamente';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al pausar la oferta';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
            header('Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/empresa/mis_ofertas.php');
            exit;

        case 'reanudar':
            $id = $_GET['id'] ?? 0;
            error_log("Empresa: Reanudando oferta ID: " . $id);
            if ($dao->cambiarEstado($id, 'activa')) {
                $_SESSION['mensaje'] = 'Oferta reanudada correctamente';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al reanudar la oferta';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
            header('Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/empresa/mis_ofertas.php');
            exit;

        case 'eliminar':
            $id = $_GET['id'] ?? 0;
            error_log("Empresa: Eliminando oferta ID: " . $id);
            if ($dao->eliminarFisico($id)) {
                $_SESSION['mensaje'] = 'Oferta eliminada correctamente';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al eliminar la oferta';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
            header('Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/empresa/mis_ofertas.php');
            exit;

        case 'crear_oferta':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    error_log("Empresa: Creando nueva oferta...");
                    
                    // Obtener empresa del usuario logueado
                    $empresaDAO = new EmpresaDAO();
                    $empresa = $empresaDAO->obtenerPorUsuario($_SESSION['id_usuario']);
                    
                    if (!$empresa) {
                        throw new Exception("No se encontró la empresa asociada");
                    }

                    $datos = [
                        'empresa_id' => $empresa->id_empresa,
                        'titulo' => $_POST['titulo'],
                        'descripcion' => $_POST['descripcion'],
                        'tipo' => $_POST['tipo'],
                        'modalidad' => $_POST['modalidad'],
                        'salario_referencial' => $_POST['salario_referencial'],
                        'fecha_publicacion' => date('Y-m-d'),
                        'fecha_cierre' => $_POST['fecha_cierre'],
                        'estado_oferta' => 'activa'
                    ];

                    error_log("Datos nueva oferta: " . print_r($datos, true));

                    if ($dao->crearDesdeArray($datos)) {
                        $_SESSION['mensaje'] = 'Oferta creada exitosamente';
                        $_SESSION['tipo_mensaje'] = 'success';
                    } else {
                        $_SESSION['mensaje'] = 'Error al crear la oferta';
                        $_SESSION['tipo_mensaje'] = 'danger';
                    }
                } catch (Exception $e) {
                    $_SESSION['mensaje'] = 'Error: ' . $e->getMessage();
                    $_SESSION['tipo_mensaje'] = 'danger';
                    error_log("Excepción al crear: " . $e->getMessage());
                }
            }
            header('Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/empresa/mis_ofertas.php');
            exit;
    }
}

// ==========================================
// ACCIONES PÚBLICAS (ESTUDIANTES/VISITANTES)
// ==========================================

// Listar ofertas activas (API JSON)
if ($action === 'listar') {
    $ofertas = $dao->listarActivas();
    header('Content-Type: application/json');
    echo json_encode($ofertas);
    exit;
}

// Detalle de oferta (API JSON)
if ($action === 'detalle' && isset($_GET['id'])) {
    $o = $dao->obtenerPorId($_GET['id']);
    header('Content-Type: application/json');
    echo json_encode($o);
    exit;
}

// ==========================================
// REDIRECCIÓN POR DEFECTO
// ==========================================
error_log("Sin acción válida, redirigiendo según rol...");

if (isset($_SESSION['rol'])) {
    switch ($_SESSION['rol']) {
        case 'admin':
            header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/admin/ofertas.php");
            break;
        case 'empresa':
            header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/empresa/mis_ofertas.php");
            break;
        case 'estudiante':
            header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/views/estudiante/ofertas.php");
            break;
        default:
            header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/index.php");
    }
} else {
    header("Location: /PORTALBOLSATRABAJOUNIVERSITARIO/index.php");
}
exit;