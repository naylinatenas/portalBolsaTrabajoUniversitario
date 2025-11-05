<?php
// models/OfertaDAO.php
require_once __DIR__ . '/../config/Conexion.php';

class OfertaDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Conexion::conectar();
    }

    /**
     * Crear oferta desde objeto Oferta
     */
    public function crear($o)
    {
        // Si recibe un objeto, convierte a array
        if (is_object($o)) {
            $datos = [
                'empresa_id' => $o->empresa_id,
                'titulo' => $o->titulo,
                'descripcion' => $o->descripcion,
                'tipo' => $o->tipo,
                'salario_referencial' => $o->salario_referencial,
                'modalidad' => $o->modalidad,
                'fecha_publicacion' => $o->fecha_publicacion ?? date('Y-m-d'),
                'fecha_cierre' => $o->fecha_cierre,
                'estado_oferta' => $o->estado_oferta ?? 'activa'
            ];
        } else {
            $datos = $o; // Ya es array
        }

        return $this->crearDesdeArray($datos);
    }

    /**
     * Crear oferta desde array (para formularios admin)
     */
    public function crearDesdeArray($datos)
    {
        try {
            $sql = "INSERT INTO oferta 
                (empresa_id, titulo, descripcion, tipo, salario_referencial, modalidad, fecha_publicacion, fecha_cierre, estado_oferta)
                VALUES
                (:empresa_id, :titulo, :descripcion, :tipo, :salario_referencial, :modalidad, :fecha_publicacion, :fecha_cierre, :estado_oferta)";
            
            $stmt = $this->pdo->prepare($sql);
            $resultado = $stmt->execute([
                ':empresa_id' => $datos['empresa_id'],
                ':titulo' => $datos['titulo'],
                ':descripcion' => $datos['descripcion'] ?? null,
                ':tipo' => $datos['tipo'],
                ':salario_referencial' => $datos['salario_referencial'] ?? null,
                ':modalidad' => $datos['modalidad'],
                ':fecha_publicacion' => $datos['fecha_publicacion'] ?? date('Y-m-d'),
                ':fecha_cierre' => $datos['fecha_cierre'] ?? null,
                ':estado_oferta' => $datos['estado_oferta'] ?? 'activa'
            ]);

            if ($resultado) {
                return $this->pdo->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error en crearDesdeArray: " . $e->getMessage());
            throw new Exception("Error al crear oferta: " . $e->getMessage());
        }
    }

    /**
     * Listar todas las ofertas con empresa (para admin)
     */
    public function listar()
    {
        $stmt = $this->pdo->query("SELECT o.*, e.razon_social 
                                   FROM oferta o 
                                   JOIN empresa e ON o.empresa_id = e.id_empresa 
                                   ORDER BY o.fecha_publicacion DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Listar ofertas activas (para estudiantes)
     */
    public function listarActivas()
    {
        $stmt = $this->pdo->prepare("SELECT o.*, e.razon_social, e.correo_contacto 
                                    FROM oferta o 
                                    JOIN empresa e ON o.empresa_id = e.id_empresa 
                                    WHERE o.estado_oferta = 'activa' 
                                    AND e.estado = 'aprobada'
                                    AND (o.fecha_cierre IS NULL OR o.fecha_cierre >= CURDATE())
                                    ORDER BY o.fecha_publicacion DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Listar ofertas por empresa
     */
    public function listarPorEmpresa($empresa_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM oferta 
                                     WHERE empresa_id = :id 
                                     ORDER BY fecha_publicacion DESC");
        $stmt->execute([':id' => $empresa_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener oferta por ID (retorna array)
     */
    public function obtenerPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT o.*, e.razon_social, e.correo_contacto 
                                    FROM oferta o 
                                    JOIN empresa e ON o.empresa_id = e.id_empresa 
                                    WHERE o.id_oferta = :id");
        $stmt->execute([':id' => $id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ? $r : null;
    }

    /**
     * Actualizar oferta desde objeto Oferta o array
     */
    public function actualizar($o)
    {
        // Si recibe un objeto, convierte a array
        if (is_object($o)) {
            $datos = [
                'id_oferta' => $o->id_oferta,
                'empresa_id' => $o->empresa_id,
                'titulo' => $o->titulo,
                'descripcion' => $o->descripcion ?? null,
                'tipo' => $o->tipo,
                'salario_referencial' => $o->salario_referencial,
                'modalidad' => $o->modalidad,
                'fecha_cierre' => $o->fecha_cierre ?? null,
                'estado_oferta' => $o->estado_oferta
            ];
        } else {
            $datos = $o; // Ya es array
        }

        return $this->actualizarDesdeArray($datos);
    }

    /**
     * Actualizar oferta desde array (para formularios admin)
     */
    public function actualizarDesdeArray($datos)
    {
        try {
            $sql = "UPDATE oferta SET 
                    empresa_id = :empresa_id,
                    titulo = :titulo,
                    descripcion = :descripcion,
                    tipo = :tipo, 
                    modalidad = :modalidad, 
                    salario_referencial = :salario_referencial,
                    fecha_cierre = :fecha_cierre,
                    estado_oferta = :estado_oferta 
                    WHERE id_oferta = :id_oferta";
            
            $stmt = $this->pdo->prepare($sql);
            $resultado = $stmt->execute([
                ':empresa_id' => $datos['empresa_id'],
                ':titulo' => $datos['titulo'],
                ':descripcion' => $datos['descripcion'] ?? null,
                ':tipo' => $datos['tipo'],
                ':modalidad' => $datos['modalidad'],
                ':salario_referencial' => $datos['salario_referencial'] ?? null,
                ':fecha_cierre' => $datos['fecha_cierre'] ?? null,
                ':estado_oferta' => $datos['estado_oferta'],
                ':id_oferta' => $datos['id_oferta']
            ]);

            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en actualizarDesdeArray: " . $e->getMessage());
            throw new Exception("Error al actualizar oferta: " . $e->getMessage());
        }
    }

    /**
     * Cambiar estado de la oferta
     */
    public function cambiarEstado($id, $estado)
    {
        $stmt = $this->pdo->prepare("UPDATE oferta SET estado_oferta = :estado WHERE id_oferta = :id");
        return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }

    /**
     * Eliminación lógica (cambia estado a inactiva)
     */
    public function eliminar($id)
    {
        return $this->cambiarEstado($id, 'cerrada');
    }

    /**
     * Eliminación física (borra registro)
     */
    public function eliminarFisico($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM oferta WHERE id_oferta = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Listar ofertas activas con filtros
     */
    public function listarActivasFiltradas($tipo, $modalidad, $empresa)
    {
        $sql = "SELECT o.*, e.razon_social
                FROM oferta o 
                JOIN empresa e ON e.id_empresa = o.empresa_id
                WHERE o.estado_oferta = 'activa'";

        $params = [];

        if ($tipo) {
            $sql .= " AND o.tipo = :tipo";
            $params[':tipo'] = $tipo;
        }
        if ($modalidad) {
            $sql .= " AND o.modalidad = :modalidad";
            $params[':modalidad'] = $modalidad;
        }
        if ($empresa) {
            $sql .= " AND e.razon_social LIKE :empresa";
            $params[':empresa'] = "%$empresa%";
        }

        $sql .= " ORDER BY o.fecha_publicacion DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Contar ofertas activas
     */
    public function contarActivas()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM oferta WHERE estado_oferta = 'activa'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    /**
     * Obtener ofertas próximas a vencer (dentro de 7 días)
     */
    public function obtenerProximasVencer()
    {
        $stmt = $this->pdo->prepare("SELECT o.*, e.razon_social 
                                    FROM oferta o 
                                    JOIN empresa e ON o.empresa_id = e.id_empresa 
                                    WHERE o.estado_oferta = 'activa' 
                                    AND o.fecha_cierre IS NOT NULL
                                    AND o.fecha_cierre BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                                    ORDER BY o.fecha_cierre ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}