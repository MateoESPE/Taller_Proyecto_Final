<?php declare(strict_types=1);

namespace App\Entities;

class MentorTecnico extends Participante
{
    private string $especialidad;
    private int $experiencia;
    private int $disponibilidadHoraria;

    public function __construct(
        ?int $id,
        string $nombre,
        string $email,
        string $nivelHabilidad,
        string $especialidad,
        int $experiencia,
        int $disponibilidadHoraria
    ) {
        parent::__construct($id, $nombre, $email, $nivelHabilidad);
        $this->especialidad = $especialidad;
        $this->experiencia = $experiencia;
        $this->disponibilidadHoraria = $disponibilidadHoraria;
    }

    public function getEspecialidad(): string             { return $this->especialidad; }
    public function getExperiencia(): int                 { return $this->experiencia; }
    public function getDisponibilidadHoraria(): int      { return $this->disponibilidadHoraria; }

    public function setEspecialidad(string $especialidad): void      { $this->especialidad = $especialidad; }
    public function setExperiencia(int $experiencia): void           { $this->experiencia = $experiencia; }
    public function setDisponibilidadHoraria(int $horas): void       { $this->disponibilidadHoraria = $horas; }
}
