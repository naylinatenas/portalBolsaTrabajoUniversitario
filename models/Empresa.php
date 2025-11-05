<?php
// models/Empresa.php

class Empresa
{
    public $id_empresa;
    public $razon_social;
    public $ruc;
    public $direccion;
    public $telefono;
    public $correo_contacto;
    public $usuario_id;
    public $estado;

    public function __construct($data = [])
    {
        if (is_array($data)) {
            $this->id_empresa = $data['id_empresa'] ?? null;
            $this->razon_social = $data['razon_social'] ?? '';
            $this->ruc = $data['ruc'] ?? '';
            $this->direccion = $data['direccion'] ?? '';
            $this->telefono = $data['telefono'] ?? '';
            $this->correo_contacto = $data['correo_contacto'] ?? '';
            $this->usuario_id = $data['usuario_id'] ?? null;
            $this->estado = $data['estado'] ?? 'pendiente';
        }
    }
}