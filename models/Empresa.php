<?php
// modelo/Empresa.php
class Empresa {
    public $id_empresa;
    public $razon_social;
    public $ruc;
    public $direccion;
    public $telefono;
    public $correo_contacto;
    public $usuario_id;
    public $estado;

    public function __construct($data = []) {
        foreach ($data as $k => $v) $this->$k = $v;
    }
}
