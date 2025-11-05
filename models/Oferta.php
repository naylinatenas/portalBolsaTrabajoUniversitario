<?php
// models/Oferta.php

class Oferta
{
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

    public function __construct($data = [])
    {
        if (is_array($data)) {
            $this->id_oferta = $data['id_oferta'] ?? null;
            $this->empresa_id = $data['empresa_id'] ?? null;
            $this->titulo = $data['titulo'] ?? '';
            $this->descripcion = $data['descripcion'] ?? '';
            $this->tipo = $data['tipo'] ?? 'tiempo_completo';
            $this->salario_referencial = $data['salario_referencial'] ?? null;
            $this->modalidad = $data['modalidad'] ?? 'presencial';
            $this->fecha_publicacion = $data['fecha_publicacion'] ?? date('Y-m-d');
            $this->fecha_cierre = $data['fecha_cierre'] ?? null;
            $this->estado_oferta = $data['estado_oferta'] ?? 'activa';
        }
    }
}