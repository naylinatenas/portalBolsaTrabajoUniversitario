<?php
// modelo/EstudianteDAO.php
require_once __DIR__ . '/../config/Conexion.php';
require_once 'Estudiante.php';

class EstudianteDAO {
    private $pdo;
    public function __construct() { $this->pdo = Conexion::getConnection(); }

    public function crear($est) {
        $sql = "INSERT INTO estudiante (usuario_id, codigo_estudiante, carrera, ciclo, cv_url, resumen_perfil)
                VALUES (:usuario_id, :codigo, :carrera, :ciclo, :cv_url, :resumen)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':usuario_id'=>$est->usuario_id, ':codigo'=>$est->codigo_estudiante,
            ':carrera'=>$est->carrera, ':ciclo'=>$est->ciclo, ':cv_url'=>$est->cv_url, ':resumen'=>$est->resumen_perfil
        ]);
        return $this->pdo->lastInsertId();
    }

    public function obtenerPorUsuarioId($usuario_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM estudiante WHERE usuario_id = :uid");
        $stmt->execute([':uid'=>$usuario_id]);
        $row = $stmt->fetch();
        return $row ? new Estudiante($row) : null;
    }

    public function actualizar($est) {
        $sql = "UPDATE estudiante SET codigo_estudiante=:codigo, carrera=:carrera, ciclo=:ciclo, cv_url=:cv_url, resumen_perfil=:resumen WHERE id_estudiante=:id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':codigo'=>$est->codigo_estudiante, ':carrera'=>$est->carrera, ':ciclo'=>$est->ciclo,
            ':cv_url'=>$est->cv_url, ':resumen'=>$est->resumen_perfil, ':id'=>$est->id_estudiante
        ]);
    }
}
