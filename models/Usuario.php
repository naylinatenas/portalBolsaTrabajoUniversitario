<?php
class Usuario {
    public $id_usuario;
    public $nombre_completo;
    public $correo;
    public $clave;
    public $rol;
    public $estado;
    public $fecha_registro;

    public function __construct($data = []) {
        foreach ($data as $k => $v) {
            if (property_exists($this, $k)) $this->$k = $v;
        }
    }
}
