<?php
// models/PostulacionDAO.php
require_once __DIR__ . '/../config/Conexion.php';
require_once 'Postulacion.php';

class PostulacionDAO
{
    private $pdo;
    public function __construct()
    {
        $this->pdo = Conexion::conectar();
    }

    public function crear(Postulacion $p)
    {
        $sql = "INSERT INTO postulacion (oferta_id, estudiante_id, comentario_empresa)
                VALUES (:oferta, :estu, :coment)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':oferta' => $p->oferta_id,
            ':estu' => $p->estudiante_id,
            ':coment' => $p->comentario_empresa
        ]);
        return $this->pdo->lastInsertId();
    }

    public function listarPorOferta($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT p.*, u.nombre_completo, u.correo
            FROM postulacion p
            JOIN estudiante e ON e.id_estudiante=p.estudiante_id
            JOIN usuario u ON u.id_usuario=e.usuario_id
            WHERE p.oferta_id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll();
    }

    public function listar()
    {
        $sql = "SELECT * FROM postulacion";
        return $this->pdo->query($sql)->fetchAll();
    }


    public function listarPorEstudiante($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT p.*, o.titulo, e.razon_social
            FROM postulacion p
            JOIN oferta o ON o.id_oferta=p.oferta_id
            JOIN empresa e ON e.id_empresa=o.empresa_id
            WHERE p.estudiante_id=:id ORDER BY p.fecha_postulacion DESC");
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll();
    }

    public function cambiarEstado($id, $estado, $coment = null)
    {
        $stmt = $this->pdo->prepare("UPDATE postulacion SET estado_postulacion=:e, comentario_empresa=:c WHERE id_postulacion=:id");
        return $stmt->execute([':e' => $estado, ':c' => $coment, ':id' => $id]);
    }

    public function existe($oferta_id, $estudiante_id)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) c FROM postulacion WHERE oferta_id=:o AND estudiante_id=:e");
        $stmt->execute([':o' => $oferta_id, ':e' => $estudiante_id]);
        return $stmt->fetch()['c'] > 0;
    }

    public function contarPorEmpresa($empresa_id)
    {
        $stmt = $this->pdo->prepare("
        SELECT COUNT(*) AS total
        FROM postulacion p
        JOIN oferta o ON o.id_oferta = p.oferta_id
        WHERE o.empresa_id = :emp
    ");
        $stmt->execute([':emp' => $empresa_id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ? intval($r['total']) : 0;
    }

    public function contarPorEstudiante($id_estudiante)
    {
        $sql = "SELECT COUNT(*) AS total FROM postulacion WHERE estudiante_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_estudiante]);
        return $stmt->fetch()['total'];
    }

    public function listarPaginado($id_estudiante, $limit, $offset)
    {
        $sql = "SELECT p.*, o.titulo AS titulo_oferta, e.razon_social
            FROM postulacion p
            JOIN oferta o ON p.oferta_id = o.id_oferta
            JOIN empresa e ON o.empresa_id = e.id_empresa
            WHERE p.estudiante_id = ?
            ORDER BY p.fecha_postulacion DESC
            LIMIT ? OFFSET ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $id_estudiante, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminarPostulacion($id_postulacion)
    {
        $sql = "DELETE FROM postulacion WHERE id_postulacion = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_postulacion]);
    }
}
