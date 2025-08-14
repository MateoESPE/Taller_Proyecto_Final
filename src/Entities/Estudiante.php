<?php declare(strict_types=1);

namespace App\Entities;

class Estudiante extends Participante
{
    private string $grado;
    private string $institucion;
    private int $tiempoDisponibleSemanal;

    public function __construct(
        ?int $id,
        string $nombre,
        string $email,
        string $nivelHabilidad,
        string $grado,
        string $institucion,
        int $tiempoDisponibleSemanal
    ) {
        parent::__construct($id, $nombre, $email, $nivelHabilidad);
        $this->grado = $grado;
        $this->institucion = $institucion;
        $this->tiempoDisponibleSemanal = $tiempoDisponibleSemanal;
    }

    public function getGrado(): string                  { return $this->grado; }
    public function getInstitucion(): string            { return $this->institucion; }
    public function getTiempoDisponibleSemanal(): int   { return $this->tiempoDisponibleSemanal; }

    public function setGrado(string $grado): void                      { $this->grado = $grado; }
    public function setInstitucion(string $institucion): void          { $this->institucion = $institucion; }
    public function setTiempoDisponibleSemanal(int $tiempo): void      { $this->tiempoDisponibleSemanal = $tiempo; }
}
