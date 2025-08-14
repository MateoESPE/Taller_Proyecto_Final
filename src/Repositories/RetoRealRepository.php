<?php declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\RetoReal;
use App\Entities\RetoSolucionable;
use PDO;

class RetoRealRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("CALL sp_retoreal_list();");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return array_map([$this, 'hydrate'], $rows);
    }

    public function findById(int $id): ?RetoReal
    {
        $stmt = $this->db->prepare("CALL sp_find_retoreal(:id);");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $row ? $this->hydrate($row) : null;
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof RetoReal) {
            throw new \InvalidArgumentException("RetoReal expected");
        }

        $stmt = $this->db->prepare("CALL sp_create_retoreal(:titulo, :descripcion, :complejidad, :areasConocimiento, :entidadColaboradora);");
        $ok = $stmt->execute([
            ':titulo' => $entity->getTitulo(),
            ':descripcion' => $entity->getDescripcion(),
            ':complejidad' => $entity->getComplejidad(),
            ':areasConocimiento' => implode(',', $entity->getAreasConocimiento()),
            ':entidadColaboradora' => $entity->getEntidadColaboradora()
        ]);

        $stmt->closeCursor();
        return $ok;
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof RetoReal) {
            throw new \InvalidArgumentException("RetoReal expected");
        }

        $stmt = $this->db->prepare("CALL sp_update_retoreal(:id, :titulo, :descripcion, :complejidad, :areasConocimiento, :entidadColaboradora);");
        $ok = $stmt->execute([
            ':id' => $entity->getId(),
            ':titulo' => $entity->getTitulo(),
            ':descripcion' => $entity->getDescripcion(),
            ':complejidad' => $entity->getComplejidad(),
            ':areasConocimiento' => implode(',', $entity->getAreasConocimiento()),
            ':entidadColaboradora' => $entity->getEntidadColaboradora()
        ]);

        $stmt->closeCursor();
        return $ok;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("CALL sp_delete_retoreal(:id);");
        $ok = $stmt->execute([':id' => $id]);
        $stmt->closeCursor();
        return $ok;
    }

    public function hydrate(array $row): RetoReal
    {
        $areas = isset($row['areasConocimiento']) ? explode(',', $row['areasConocimiento']) : [];
        return new RetoReal(
            (int)$row['id'],
            $row['titulo'] ?? '',
            $row['descripcion'] ?? '',
            $row['complejidad'] ?? '',
            $areas,
            $row['entidadColaboradora'] ?? ''
        );
    }
}
