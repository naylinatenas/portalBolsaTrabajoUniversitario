<?php
// models/OfertaDAO.php
require_once __DIR__ . '/../config/Conexion.php';
require_once 'Oferta.php';

class OfertaDAO
{
    private $pdo;
    public function __construct()
    {
        $this->pdo = Conexion::conectar();
    }

    public function crear(Oferta $o)
    {
        $sql = "INSERT INTO oferta (empresa_id, titulo, descripcion, tipo, salario_referencial,
                modalidad, fecha_cierre, estado_oferta)
                VALUES (:emp, :tit, :desc, :tipo, :sal, :mod, :fcierre, :estado)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':emp' => $o->empresa_id,
            ':tit' => $o->titulo,
            ':desc' => $o->descripcion,
            ':tipo' => $o->tipo,
            ':sal' => $o->salario_referencial,
            ':mod' => $o->modalidad,
            ':fcierre' => $o->fecha_cierre,
            ':estado' => $o->estado_oferta ?? 'activa'
        ]);
        return $this->pdo->lastInsertId();
    }

    public function listarActivas()
    {
        $sql = "SELECT o.*, e.razon_social FROM oferta o JOIN empresa e ON e.id_empresa=o.empresa_id 
                WHERE o.estado_oferta='activa' AND e.estado='aprobada' ORDER BY fecha_publicacion DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function listarActivasFiltradas($tipo, $modalidad, $empresa) {
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


    public function listarPorEmpresa($empresa_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM oferta WHERE empresa_id=:e ORDER BY fecha_publicacion DESC");
        $stmt->execute([':e' => $empresa_id]);
        return $stmt->fetchAll();
    }

    public function listar()
    {
        $sql = "SELECT * FROM oferta";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerPorId($id)
    {
        $sql = "SELECT o.*, e.razon_social 
            FROM oferta o 
            JOIN empresa e ON e.id_empresa = o.empresa_id
            WHERE o.id_oferta = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar(Oferta $o)
    {
        $sql = "UPDATE oferta SET titulo=:tit, descripcion=:desc, tipo=:tipo,
                salario_referencial=:sal, modalidad=:mod, fecha_cierre=:fcierre, estado_oferta=:estado
                WHERE id_oferta=:id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':tit' => $o->titulo,
            ':desc' => $o->descripcion,
            ':tipo' => $o->tipo,
            ':sal' => $o->salario_referencial,
            ':mod' => $o->modalidad,
            ':fcierre' => $o->fecha_cierre,
            ':estado' => $o->estado_oferta,
            ':id' => $o->id_oferta
        ]);
    }

    public function eliminar($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM oferta WHERE id_oferta=:id");
        return $stmt->execute([':id' => $id]);
    }
}
