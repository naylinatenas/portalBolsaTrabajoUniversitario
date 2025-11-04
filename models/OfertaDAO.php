<?php
// modelo/OfertaDAO.php
require_once __DIR__ . '/../config/Conexion.php';
require_once 'Oferta.php';

class OfertaDAO {
    private $pdo;
    public function __construct() { $this->pdo = Conexion::getConnection(); }

    public function crear($o) {
        $sql = "INSERT INTO oferta (empresa_id, titulo, descripcion, tipo, salario_referencial, modalidad, fecha_cierre, estado_oferta)
                VALUES (:empresa_id, :titulo, :descripcion, :tipo, :salario, :modalidad, :fecha_cierre, :estado)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':empresa_id'=>$o->empresa_id, ':titulo'=>$o->titulo, ':descripcion'=>$o->descripcion,
            ':tipo'=>$o->tipo, ':salario'=>$o->salario_referencial, ':modalidad'=>$o->modalidad,
            ':fecha_cierre'=>$o->fecha_cierre, ':estado'=>$o->estado_oferta ?? 'activa'
        ]);
        return $this->pdo->lastInsertId();
    }

    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM oferta WHERE id_oferta = :id");
        $stmt->execute([':id'=>$id]);
        $r = $stmt->fetch();
        return $r ? new Oferta($r) : null;
    }

    public function listarActivas($filters = []) {
        $sql = "SELECT o.*, e.razon_social FROM oferta o JOIN empresa e ON e.id_empresa = o.empresa_id WHERE o.estado_oferta = 'activa'";
        $params = [];
        if (!empty($filters['modalidad'])) { $sql .= " AND modalidad = :modalidad"; $params[':modalidad']=$filters['modalidad']; }
        if (!empty($filters['tipo'])) { $sql .= " AND tipo = :tipo"; $params[':tipo']=$filters['tipo']; }
        $sql .= " ORDER BY fecha_publicacion DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function listarPorEmpresa($empresa_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM oferta WHERE empresa_id = :eid ORDER BY fecha_publicacion DESC");
        $stmt->execute([':eid'=>$empresa_id]);
        return $stmt->fetchAll();
    }

    public function actualizar($o) {
        $sql = "UPDATE oferta SET titulo=:titulo, descripcion=:descripcion, tipo=:tipo, salario_referencial=:salario, modalidad=:modalidad, fecha_cierre=:fecha_cierre, estado_oferta=:estado WHERE id_oferta=:id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':titulo'=>$o->titulo, ':descripcion'=>$o->descripcion, ':tipo'=>$o->tipo,
            ':salario'=>$o->salario_referencial, ':modalidad'=>$o->modalidad, ':fecha_cierre'=>$o->fecha_cierre,
            ':estado'=>$o->estado_oferta, ':id'=>$o->id_oferta
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->pdo->prepare("DELETE FROM oferta WHERE id_oferta = :id");
        return $stmt->execute([':id'=>$id]);
    }

    public function cambiarEstado($id, $estado) {
        $stmt = $this->pdo->prepare("UPDATE oferta SET estado_oferta = :estado WHERE id_oferta = :id");
        return $stmt->execute([':estado'=>$estado, ':id'=>$id]);
    }
}
