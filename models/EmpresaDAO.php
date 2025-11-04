<?php
// modelo/EmpresaDAO.php
require_once __DIR__ . '/../config/Conexion.php';
require_once 'Empresa.php';

class EmpresaDAO {
    private $pdo;
    public function __construct() { $this->pdo = Conexion::getConnection(); }

    public function crear($empresa) {
        $sql = "INSERT INTO empresa (razon_social, ruc, direccion, telefono, correo_contacto, usuario_id, estado)
                VALUES (:razon, :ruc, :direccion, :telefono, :correo_contacto, :usuario_id, :estado)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':razon' => $empresa->razon_social,
            ':ruc' => $empresa->ruc,
            ':direccion' => $empresa->direccion,
            ':telefono' => $empresa->telefono,
            ':correo_contacto' => $empresa->correo_contacto,
            ':usuario_id' => $empresa->usuario_id,
            ':estado' => $empresa->estado ?? 'pendiente'
        ]);
        return $this->pdo->lastInsertId();
    }

    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM empresa WHERE id_empresa = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? new Empresa($row) : null;
    }

    public function listar() {
        $stmt = $this->pdo->query("SELECT e.*, u.correo as correo_usuario FROM empresa e LEFT JOIN usuario u ON u.id_usuario = e.usuario_id ORDER BY id_empresa DESC");
        return $stmt->fetchAll();
    }

    public function actualizar($empresa) {
        $sql = "UPDATE empresa SET razon_social=:razon, ruc=:ruc, direccion=:direccion, telefono=:telefono, correo_contacto=:correo_contacto, estado=:estado WHERE id_empresa=:id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':razon'=>$empresa->razon_social, ':ruc'=>$empresa->ruc, ':direccion'=>$empresa->direccion,
            ':telefono'=>$empresa->telefono, ':correo_contacto'=>$empresa->correo_contacto, ':estado'=>$empresa->estado, ':id'=>$empresa->id_empresa
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->pdo->prepare("DELETE FROM empresa WHERE id_empresa = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function cambiarEstado($id, $estado) {
        $stmt = $this->pdo->prepare("UPDATE empresa SET estado = :estado WHERE id_empresa = :id");
        return $stmt->execute([':estado'=>$estado, ':id'=>$id]);
    }
}
