<?php declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\RetoReal;
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

        $retos = [];
        foreach ($rows as $r) {
            $retos[] = $this->hydrate($r);
        }
        return $retos;
    }

    public function findById(int $id): ?RetoReal
    {
        $stmt = $this->db->prepare("CALL sp_retoreal_find(:id);");
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

        $stmt = $this->db->prepare("CALL sp_retoreal_create(:titulo, :descripcion, :complejidad, :areasConocimiento, :entidadColaboradora);");
        $ok = $stmt->execute([
            ':titulo' => $entity->getTitulo(),
            ':descripcion' => $entity->getDescripcion(),
            ':complejidad' => $entity->getComplejidad(),
            ':areasConocimiento' => json_encode($entity->getAreasConocimiento()),
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

        $stmt = $this->db->prepare("CALL sp_retoreal_update(:id, :titulo, :descripcion, :complejidad, :areasConocimiento, :entidadColaboradora);");
        $ok = $stmt->execute([
            ':id' => $entity->getId(),
            ':titulo' => $entity->getTitulo(),
            ':descripcion' => $entity->getDescripcion(),
            ':complejidad' => $entity->getComplejidad(),
            ':areasConocimiento' => json_encode($entity->getAreasConocimiento()),
            ':entidadColaboradora' => $entity->getEntidadColaboradora()
        ]);

        $stmt->closeCursor();
        return $ok;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("CALL sp_retoreal_delete(:id);");
        $ok = $stmt->execute([':id' => $id]);
        $stmt->closeCursor();

        return $ok;
    }

    private function hydrate(array $row): RetoReal
    {
        return new RetoReal(
            (int)($row['id'] ?? 0),
            $row['titulo'] ?? '',
            $row['descripcion'] ?? '',
            $row['complejidad'] ?? '',
            isset($row['areasConocimiento']) ? json_decode($row['areasConocimiento'], true) : [],
            $row['entidadColaboradora'] ?? ''
        );
    }
}
