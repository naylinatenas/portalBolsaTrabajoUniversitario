<?php
// modelo/PostulacionDAO.php
require_once __DIR__ . '/../config/Conexion.php';
require_once 'Postulacion.php';

class PostulacionDAO {
    private $pdo;
    public function __construct() { $this->pdo = Conexion::getConnection(); }

    public function crear($p) {
        // previene duplicados por constraint UNIQUE
        $sql = "INSERT INTO postulacion (oferta_id, estudiante_id, comentario_empresa) VALUES (:oferta, :estudiante, :comentario)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':oferta'=>$p->oferta_id, ':estudiante'=>$p->estudiante_id, ':comentario'=>$p->comentario_empresa]);
    }

    public function listarPorOferta($oferta_id) {
        $stmt = $this->pdo->prepare("SELECT p.*, s.codigo_estudiante, u.nombre_completo, u.correo FROM postulacion p JOIN estudiante s ON s.id_estudiante = p.estudiante_id JOIN usuario u ON u.id_usuario = s.usuario_id WHERE p.oferta_id = :oferta ORDER BY p.fecha_postulacion DESC");
        $stmt->execute([':oferta'=>$oferta_id]);
        return $stmt->fetchAll();
    }

    public function listarPorEstudiante($estudiante_id) {
        $stmt = $this->pdo->prepare("SELECT p.*, o.titulo, e.razon_social FROM postulacion p JOIN oferta o ON o.id_oferta = p.oferta_id JOIN empresa e ON e.id_empresa = o.empresa_id WHERE p.estudiante_id = :est ORDER BY p.fecha_postulacion DESC");
        $stmt->execute([':est'=>$estudiante_id]);
        return $stmt->fetchAll();
    }

    public function cambiarEstado($id, $estado, $comentario = null) {
        $sql = "UPDATE postulacion SET estado_postulacion = :estado, comentario_empresa = :comentario WHERE id_postulacion = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':estado'=>$estado, ':comentario'=>$comentario, ':id'=>$id]);
    }

    public function existePostulacion($oferta_id, $estudiante_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as c FROM postulacion WHERE oferta_id = :o AND estudiante_id = :e");
        $stmt->execute([':o'=>$oferta_id, ':e'=>$estudiante_id]);
        $r = $stmt->fetch();
        return $r['c'] > 0;
    }
}
