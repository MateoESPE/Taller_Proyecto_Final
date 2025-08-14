<?php declare(strict_types=1);

namespace App\Entities;

class Estudiante extends Participante
{
    private string $grado;
    private string $institucion;
    private int $tiempoDisponibleSemanal;
    private array $habilidades;

    public function __construct(
        ?string $id,
        string $nombre,
        string $email,
        string $nivelHabilidad,
        string $grado,
        string $institucion,
        int $tiempoDisponibleSemanal,
        array $habilidades
    ) {
        parent::__construct($id, $nombre, $email, $nivelHabilidad);
        $this->grado = $grado;
        $this->institucion = $institucion;
        $this->tiempoDisponibleSemanal = $tiempoDisponibleSemanal;
        $this->habilidades = $habilidades;
    }

    // Getters
    public function getGrado(): string                  { return $this->grado; }
    public function getInstitucion(): string            { return $this->institucion; }
    public function getTiempoDisponibleSemanal(): int   { return $this->tiempoDisponibleSemanal; }
    public function getHabilidades(): array             { return $this->habilidades; }

    // Setters
    public function setGrado(string $grado): void                      { $this->grado = $grado; }
    public function setInstitucion(string $institucion): void          { $this->institucion = $institucion; }
    public function setTiempoDisponibleSemanal(int $tiempo): void      { $this->tiempoDisponibleSemanal = $tiempo; }
    public function setHabilidades(array $habilidades): void           { $this->habilidades = $habilidades; }
}
