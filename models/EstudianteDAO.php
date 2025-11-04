<?php
// models/EstudianteDAO.php
require_once __DIR__ . '/../config/Conexion.php';
require_once 'Estudiante.php';

class EstudianteDAO {
    private $pdo;
    public function __construct() {
        $this->pdo = Conexion::conectar();
    }

    public function crear(Estudiante $e) {
        $sql = "INSERT INTO estudiante (usuario_id, codigo_estudiante, carrera, ciclo, cv_url, resumen_perfil)
                VALUES (:uid, :cod, :carr, :ciclo, :cv, :resumen)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':uid'=>$e->usuario_id, ':cod'=>$e->codigo_estudiante,
            ':carr'=>$e->carrera, ':ciclo'=>$e->ciclo,
            ':cv'=>$e->cv_url, ':resumen'=>$e->resumen_perfil
        ]);
        return $this->pdo->lastInsertId();
    }

    public function obtenerPorUsuario($usuario_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM estudiante WHERE usuario_id=:u");
        $stmt->execute([':u'=>$usuario_id]);
        $r = $stmt->fetch();
        return $r ? new Estudiante($r) : null;
    }

    public function actualizar(Estudiante $e) {
        $sql = "UPDATE estudiante SET codigo_estudiante=:cod, carrera=:carr, ciclo=:ciclo,
                cv_url=:cv, resumen_perfil=:resumen WHERE id_estudiante=:id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':cod'=>$e->codigo_estudiante, ':carr'=>$e->carrera,
            ':ciclo'=>$e->ciclo, ':cv'=>$e->cv_url,
            ':resumen'=>$e->resumen_perfil, ':id'=>$e->id_estudiante
        ]);
    }
}
