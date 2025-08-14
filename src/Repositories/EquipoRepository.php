<?php declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\Equipo;
use PDO;

class EquipoRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("CALL sp_equipo_list();");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $equipos = [];
        foreach ($rows as $r) {
            $equipos[] = $this->hydrate($r);
        }
        return $equipos;
    }

    public function findById(int $id): ?Equipo
    {
        $stmt = $this->db->prepare("CALL sp_equipo_find(:id);");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $row ? $this->hydrate($row) : null;
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof Equipo) {
            throw new \InvalidArgumentException("Equipo expected");
        }

        $stmt = $this->db->prepare("CALL sp_equipo_create(:nombre, :hackathonId, :participantes, :retosAsignados);");
        $ok = $stmt->execute([
            ':nombre' => $entity->getNombre(),
            ':hackathonId' => $entity->getHackathonId(),
            ':participantes' => json_encode($entity->getParticipantes()),
            ':retosAsignados' => json_encode($entity->getRetosAsignados())
        ]);

        $stmt->closeCursor();
        return $ok;
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof Equipo) {
            throw new \InvalidArgumentException("Equipo expected");
        }

        $stmt = $this->db->prepare("CALL sp_equipo_update(:id, :nombre, :hackathonId, :participantes, :retosAsignados);");
        $ok = $stmt->execute([
            ':id' => $entity->getId(),
            ':nombre' => $entity->getNombre(),
            ':hackathonId' => $entity->getHackathonId(),
            ':participantes' => json_encode($entity->getParticipantes()),
            ':retosAsignados' => json_encode($entity->getRetosAsignados())
        ]);

        $stmt->closeCursor();
        return $ok;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("CALL sp_equipo_delete(:id);");
        $ok = $stmt->execute([':id' => $id]);
        $stmt->closeCursor();

        return $ok;
    }

    private function hydrate(array $row): Equipo
    {
        return new Equipo(
            (int)($row['id'] ?? 0),
            $row['nombre'] ?? '',
            $row['hackathonId'] ?? '',
            isset($row['participantes']) ? json_decode($row['participantes'], true) : [],
            isset($row['retosAsignados']) ? json_decode($row['retosAsignados'], true) : []
        );
    }
}
