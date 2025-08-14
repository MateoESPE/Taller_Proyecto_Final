<?php declare(strict_types=1);

namespace App\Entities;

class RetoExperimental extends RetoSolucionable
{
    private string $enfoquePedagogico;

    public function __construct(
        ?int $id,
        string $titulo,
        string $descripcion,
        string $complejidad,
        array $areasConocimiento,
        string $enfoquePedagogico
    ) {
        parent::__construct($id, $titulo, $descripcion, $complejidad, $areasConocimiento);
        $this->enfoquePedagogico = $enfoquePedagogico;
    }

    // Getters
    public function getEnfoquePedagogico(): string { return $this->enfoquePedagogico; }

    // Setters
    public function setEnfoquePedagogico(string $enfoquePedagogico): void { $this->enfoquePedagogico = $enfoquePedagogico; }
}
