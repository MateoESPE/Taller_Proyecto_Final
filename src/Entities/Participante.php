<?php declare(strict_types=1);

namespace App\Entities;

abstract class Participante
{
    protected ?string $id;
    protected string $nombre;
    protected string $email;
    protected string $nivelHabilidad;

    public function __construct(
        ?string $id,
        string $nombre,
        string $email,
        string $nivelHabilidad
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->nivelHabilidad = $nivelHabilidad;
    }

    public function getId(): ?string               { return $this->id; }
    public function getNombre(): string            { return $this->nombre; }
    public function getEmail(): string             { return $this->email; }
    public function getNivelHabilidad(): string    { return $this->nivelHabilidad; }

    public function setId(string $id): void                   { $this->id = $id; }
    public function setNombre(string $nombre): void           { $this->nombre = $nombre; }
    public function setEmail(string $email): void             { $this->email = $email; }
    public function setNivelHabilidad(string $nivelHabilidad): void { $this->nivelHabilidad = $nivelHabilidad; }
}
