<?php declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\RetoSolucionable;
use PDO;

class RetoSolucionableRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("CALL sp_retosolucionable_list();");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $retos = [];
        foreach ($rows as $r) {
            $retos[] = $this->hydrate($r);
        }
        return $retos;
    }

    public function findById(int $id): ?RetoSolucionable
    {
        $stmt = $this->db->prepare("CALL sp_retosolucionable_find(:id);");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $row ? $this->hydrate($row) : null;
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof RetoSolucionable) {
            throw new \InvalidArgumentException("RetoSolucionable expected");
        }

        $stmt = $this->db->prepare("CALL sp_retosolucionable_create(:titulo, :descripcion, :complejidad, :areasConocimiento);");
        $ok = $stmt->execute([
            ':titulo' => $entity->getTitulo(),
            ':descripcion' => $entity->getDescripcion(),
            ':complejidad' => $entity->getComplejidad(),
            ':areasConocimiento' => json_encode($entity->getAreasConocimiento())
        ]);

        $stmt->closeCursor();
        return $ok;
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof RetoSolucionable) {
            throw new \InvalidArgumentException("RetoSolucionable expected");
        }

        $stmt = $this->db->prepare("CALL sp_retosolucionable_update(:id, :titulo, :descripcion, :complejidad, :areasConocimiento);");
        $ok = $stmt->execute([
            ':id' => $entity->getId(),
            ':titulo' => $entity->getTitulo(),
            ':descripcion' => $entity->getDescripcion(),
            ':complejidad' => $entity->getComplejidad(),
            ':areasConocimiento' => json_encode($entity->getAreasConocimiento())
        ]);

        $stmt->closeCursor();
        return $ok;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("CALL sp_retosolucionable_delete(:id);");
        $ok = $stmt->execute([':id' => $id]);
        $stmt->closeCursor();

        return $ok;
    }

    private function hydrate(array $row): RetoSolucionable
    {
        return new class(
            (int) ($row['id'] ?? 0),
            $row['titulo'] ?? '',
            $row['descripcion'] ?? '',
            $row['complejidad'] ?? '',
            isset($row['areasConocimiento']) ? json_decode($row['areasConocimiento'], true) : []
        ) extends RetoSolucionable {};
    }
}
