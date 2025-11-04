<?php
// models/Postulacion.php
class Postulacion {
    public $id_postulacion;
    public $oferta_id;
    public $estudiante_id;
    public $fecha_postulacion;
    public $estado_postulacion;
    public $comentario_empresa;

    public function __construct($data = []) {
        foreach ($data as $k => $v)
            if (property_exists($this, $k)) $this->$k = $v;
    }
}
