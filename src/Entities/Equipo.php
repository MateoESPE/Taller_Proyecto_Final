<?php declare(strict_types=1);

namespace App\Entities;

use App\Entities\Participante;
use App\Entities\RetoSolucionable;

class Equipo
{
    private string $id;
    private string $nombre;
    private string $hackathonId;
    private array $participantes;
    private array $retosAsignados;

    public function __construct(
        string $id,
        string $nombre,
        string $hackathonId,
        array $participantes = [],
        array $retosAsignados = []
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->hackathonId = $hackathonId;
        $this->participantes = $participantes;
        $this->retosAsignados = $retosAsignados;
    }

    // Getters
    public function getId(): string                        { return $this->id; }
    public function getNombre(): string                    { return $this->nombre; }
    public function getHackathonId(): string              { return $this->hackathonId; }
    public function getParticipantes(): array             { return $this->participantes; }
    public function getRetosAsignados(): array            { return $this->retosAsignados; }

    // Setters
    public function setId(string $id): void                              { $this->id = $id; }
    public function setNombre(string $nombre): void                      { $this->nombre = $nombre; }
    public function setHackathonId(string $hackathonId): void            { $this->hackathonId = $hackathonId; }
    public function setParticipantes(array $participantes): void         { $this->participantes = $participantes; }
    public function setRetosAsignados(array $retosAsignados): void       { $this->retosAsignados = $retosAsignados; }
}
