<?php declare(strict_types=1);

namespace App\Entities;

abstract class RetoSolucionable
{
    protected ?int $id;
    protected string $titulo;
    protected string $descripcion;
    protected string $complejidad;
    protected array $areasConocimiento;

    public function __construct(
        ?int $id,
        string $titulo,
        string $descripcion,
        string $complejidad,
        array $areasConocimiento
    ) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
        $this->complejidad = $complejidad;
        $this->areasConocimiento = $areasConocimiento;
    }

    public function getId(): ?string                      { return $this->id; }
    public function getTitulo(): string                   { return $this->titulo; }
    public function getDescripcion(): string              { return $this->descripcion; }
    public function getComplejidad(): string             { return $this->complejidad; }
    public function getAreasConocimiento(): array        { return $this->areasConocimiento; }

    public function setId(string $id): void                          { $this->id = $id; }
    public function setTitulo(string $titulo): void                  { $this->titulo = $titulo; }
    public function setDescripcion(string $descripcion): void        { $this->descripcion = $descripcion; }
    public function setComplejidad(string $complejidad): void        { $this->complejidad = $complejidad; }
    public function setAreasConocimiento(array $areasConocimiento): void { $this->areasConocimiento = $areasConocimiento; }
}
