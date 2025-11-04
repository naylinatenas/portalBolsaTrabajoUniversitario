<?php
// models/EmpresaDAO.php
require_once __DIR__ . '/../config/Conexion.php';
require_once 'Empresa.php';

class EmpresaDAO
{
    private $pdo;
    public function __construct()
    {
        $this->pdo = Conexion::conectar();
    }

    public function crear(Empresa $e)
    {
        $sql = "INSERT INTO empresa 
                (razon_social, ruc, direccion, telefono, correo_contacto, usuario_id, estado)
                VALUES (:razon, :ruc, :dir, :tel, :correo, :uid, :estado)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':razon' => $e->razon_social,
            ':ruc' => $e->ruc,
            ':dir' => $e->direccion,
            ':tel' => $e->telefono,
            ':correo' => $e->correo_contacto,
            ':uid' => $e->usuario_id,
            ':estado' => $e->estado ?? 'pendiente'
        ]);
        return $this->pdo->lastInsertId();
    }

    public function listar()
    {
        $stmt = $this->pdo->query("SELECT e.*, u.correo FROM empresa e JOIN usuario u ON e.usuario_id=u.id_usuario ORDER BY id_empresa DESC");
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM empresa WHERE id_empresa = :id");
        $stmt->execute([':id' => $id]);
        $r = $stmt->fetch();
        return $r ? new Empresa($r) : null;
    }

    public function actualizar(Empresa $e)
    {
        $sql = "UPDATE empresa SET razon_social=:razon, ruc=:ruc, direccion=:dir,
                telefono=:tel, correo_contacto=:correo, estado=:estado WHERE id_empresa=:id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':razon' => $e->razon_social,
            ':ruc' => $e->ruc,
            ':dir' => $e->direccion,
            ':tel' => $e->telefono,
            ':correo' => $e->correo_contacto,
            ':estado' => $e->estado,
            ':id' => $e->id_empresa
        ]);
    }

    public function cambiarEstado($id, $estado)
    {
        $stmt = $this->pdo->prepare("UPDATE empresa SET estado=:e WHERE id_empresa=:id");
        return $stmt->execute([':e' => $estado, ':id' => $id]);
    }

    public function eliminar($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM empresa WHERE id_empresa=:id");
        return $stmt->execute([':id' => $id]);
    }

    public function obtenerPorUsuario($usuario_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM empresa WHERE usuario_id = :uid LIMIT 1");
        $stmt->execute([':uid' => $usuario_id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ? new Empresa($r) : null;
    }
}
