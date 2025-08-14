<?php declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\MentorTecnico;
use PDO;

class MentorTecnicoRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("CALL sp_mentortecnico_list();");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return array_map([$this, 'hydrate'], $rows);
    }

    public function findById(int $id): ?MentorTecnico
    {
        $stmt = $this->db->prepare("CALL sp_find_mentortecnico(:id);");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $row ? $this->hydrate($row) : null;
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof MentorTecnico) {
            throw new \InvalidArgumentException("MentorTecnico expected");
        }

        $stmt = $this->db->prepare("CALL sp_create_mentortecnico(:nombre, :email, :nivelHabilidad, :especialidad, :experiencia, :disponibilidad);");
        $ok = $stmt->execute([
            ':nombre' => $entity->getNombre(),
            ':email' => $entity->getEmail(),
            ':nivelHabilidad' => $entity->getNivelHabilidad(),
            ':especialidad' => $entity->getEspecialidad(),
            ':experiencia' => $entity->getExperiencia(),
            ':disponibilidad' => $entity->getDisponibilidad(),
        ]);

        $stmt->closeCursor();
        return $ok;
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof MentorTecnico) {
            throw new \InvalidArgumentException("MentorTecnico expected");
        }

        $stmt = $this->db->prepare("CALL sp_update_mentortecnico(:id, :nombre, :email, :nivelHabilidad, :especialidad, :experiencia, :disponibilidad);");
        $ok = $stmt->execute([
            ':id' => $entity->getId(),
            ':nombre' => $entity->getNombre(),
            ':email' => $entity->getEmail(),
            ':nivelHabilidad' => $entity->getNivelHabilidad(),
            ':especialidad' => $entity->getEspecialidad(),
            ':experiencia' => $entity->getExperiencia(),
            ':disponibilidad' => $entity->getDisponibilidad(),
        ]);

        $stmt->closeCursor();
        return $ok;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("CALL sp_delete_mentortecnico(:id);");
        $ok = $stmt->execute([':id' => $id]);
        $stmt->closeCursor();
        return $ok;
    }

    public function hydrate(array $row): MentorTecnico
    {
        return new MentorTecnico(
            (int)$row['id'],
            $row['nombre'] ?? '',
            $row['email'] ?? '',
            $row['nivelHabilidad'] ?? '',
            $row['especialidad'] ?? '',
            (int)($row['experiencia'] ?? 0),
            $row['disponibilidad'] ?? ''
        );
    }
}
