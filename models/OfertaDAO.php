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
                    (empresa_id, titulo, descripcion, tipo, salario_referencial, 
                     modalidad, fecha_publicacion, fecha_cierre, estado_oferta)
                    VALUES (:emp, :tit, :desc, :tipo, :sal, :mod, :pub, :cierre, :estado)";
            $stmt = $this->pdo->prepare($sql);
            $resultado = $stmt->execute([
                ':emp' => $datos['empresa_id'],
                ':tit' => $datos['titulo'],
                ':desc' => $datos['descripcion'],
                ':tipo' => $datos['tipo'],
                ':sal' => $datos['salario_referencial'],
                ':mod' => $datos['modalidad'],
                ':pub' => $datos['fecha_publicacion'] ?? date('Y-m-d'),
                ':cierre' => $datos['fecha_cierre'],
                ':estado' => $datos['estado_oferta'] ?? 'activa'
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
     * Obtener oferta por ID (retorna objeto Oferta)
     */
    public function obtenerPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT o.*, e.razon_social, e.correo_contacto 
                                    FROM oferta o 
                                    JOIN empresa e ON o.empresa_id = e.id_empresa 
                                    WHERE o.id_oferta = :id");
        $stmt->execute([':id' => $id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ? $r : null; // Retorna array para compatibilidad con JSON
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
                    empresa_id = :emp,
                    titulo = :tit, 
                    tipo = :tipo, 
                    modalidad = :mod, 
                    salario_referencial = :sal, 
                    estado_oferta = :estado 
                    WHERE id_oferta = :id";
            $stmt = $this->pdo->prepare($sql);
            $resultado = $stmt->execute([
                ':emp' => $datos['empresa_id'],
                ':tit' => $datos['titulo'],
                ':tipo' => $datos['tipo'],
                ':mod' => $datos['modalidad'],
                ':sal' => $datos['salario_referencial'],
                ':estado' => $datos['estado_oferta'],
                ':id' => $datos['id_oferta']
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
        return $this->cambiarEstado($id, 'inactiva');
    }

    /**
     * Eliminación física (borra registro)
     */
    public function eliminarFisico($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM oferta WHERE id_oferta = :id");
        return $stmt->execute([':id' => $id]);
    }


    public function listarActivasFiltradas($tipo, $modalidad, $empresa)
    {
        $sql = "SELECT o.*, e.razon_social
            FROM oferta o 
            JOIN empresa e ON e.id_empresa=o.empresa_id
            WHERE o.estado_oferta='activa'";

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

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
