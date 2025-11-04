<?php
// models/UsuarioDAO.php
require_once __DIR__ . '/../config/Conexion.php';
require_once 'Usuario.php';

class UsuarioDAO {
    private $pdo;
    public function __construct() {
        $this->pdo = Conexion::getConexion();
    }

    public function crear(Usuario $u) {
        $sql = "INSERT INTO usuario (nombre_completo, correo, clave, rol, estado)
                VALUES (:nombre, :correo, :clave, :rol, :estado)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $u->nombre_completo,
            ':correo' => $u->correo,
            ':clave'  => password_hash($u->clave, PASSWORD_DEFAULT),
            ':rol'    => $u->rol,
            ':estado' => $u->estado ?? 1
        ]);
        return $this->pdo->lastInsertId();
    }

    public function obtenerPorCorreo($correo) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuario WHERE correo = :c");
        $stmt->execute([':c' => $correo]);
        $r = $stmt->fetch();
        return $r ? new Usuario($r) : null;
    }

    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuario WHERE id_usuario = :id");
        $stmt->execute([':id' => $id]);
        $r = $stmt->fetch();
        return $r ? new Usuario($r) : null;
    }

    public function listar() {
        $stmt = $this->pdo->query("SELECT * FROM usuario ORDER BY fecha_registro DESC");
        return $stmt->fetchAll();
    }

    public function actualizarEstado($id, $estado) {
        $stmt = $this->pdo->prepare("UPDATE usuario SET estado = :e WHERE id_usuario = :id");
        return $stmt->execute([':e'=>$estado, ':id'=>$id]);
    }

    public function eliminar($id) {
        $stmt = $this->pdo->prepare("DELETE FROM usuario WHERE id_usuario = :id");
        return $stmt->execute([':id'=>$id]);
    }
}
