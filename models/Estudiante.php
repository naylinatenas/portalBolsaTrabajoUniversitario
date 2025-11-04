<?php
// models/Estudiante.php
class Estudiante {
    public $id_estudiante;
    public $usuario_id;
    public $codigo_estudiante;
    public $carrera;
    public $ciclo;
    public $cv_url;
    public $resumen_perfil;

    public function __construct($data = []) {
        foreach ($data as $k => $v)
            if (property_exists($this, $k)) $this->$k = $v;
    }
}
