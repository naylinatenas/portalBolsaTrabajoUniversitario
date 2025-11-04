<?php
// models/Oferta.php
class Oferta {
    public $id_oferta;
    public $empresa_id;
    public $titulo;
    public $descripcion;
    public $tipo;
    public $salario_referencial;
    public $modalidad;
    public $fecha_publicacion;
    public $fecha_cierre;
    public $estado_oferta;

    public function __construct($data = []) {
        foreach ($data as $k => $v)
            if (property_exists($this, $k)) $this->$k = $v;
    }
}

