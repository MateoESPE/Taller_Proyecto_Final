<?php declare(strict_types=1);

namespace App\Entities;

class MentorTecnico extends Participante
{
    private string $especialidad;
    private int $experiencia;
    private string $disponibilidadHoraria;

    public function __construct(
        ?string $id,
        string $nombre,
        string $email,
        string $nivelHabilidad,
        string $especialidad,
        int $experiencia,
        string $disponibilidadHoraria
    ) {
        parent::__construct($id, $nombre, $email, $nivelHabilidad);
        $this->especialidad = $especialidad;
        $this->experiencia = $experiencia;
        $this->disponibilidadHoraria = $disponibilidadHoraria;
    }

    // Getters
    public function getEspecialidad(): string           { return $this->especialidad; }
    public function getExperiencia(): int              { return $this->experiencia; }
    public function getDisponibilidadHoraria(): string { return $this->disponibilidadHoraria; }

    // Setters
    public function setEspecialidad(string $especialidad): void           { $this->especialidad = $especialidad; }
    public function setExperiencia(int $experiencia): void                { $this->experiencia = $experiencia; }
    public function setDisponibilidadHoraria(string $disponibilidad): void { $this->disponibilidadHoraria = $disponibilidad; }
}
