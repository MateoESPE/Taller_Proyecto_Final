<?php declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\Estudiante;
use PDO;

class EstudianteRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("CALL sp_estudiante_list();");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return array_map([$this, 'hydrate'], $rows);
    }

    public function findById(int $id): ?Estudiante
    {
        $stmt = $this->db->prepare("CALL sp_find_estudiante(:id);");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $row ? $this->hydrate($row) : null;
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof Estudiante) {
            throw new \InvalidArgumentException("Estudiante expected");
        }

        $stmt = $this->db->prepare("CALL sp_create_estudiante(:nombre, :email, :nivelHabilidad, :grado, :institucion, :tiempoDisponibleSemanal);");
        $ok = $stmt->execute([
            ':nombre' => $entity->getNombre(),
            ':email' => $entity->getEmail(),
            ':nivelHabilidad' => $entity->getNivelHabilidad(),
            ':grado' => $entity->getGrado(),
            ':institucion' => $entity->getInstitucion(),
            ':tiempoDisponibleSemanal' => $entity->getTiempoDisponibleSemanal(),
        ]);

        $stmt->closeCursor();
        return $ok;
    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof Estudiante) {
            throw new \InvalidArgumentException("Estudiante expected");
        }

        $stmt = $this->db->prepare("CALL sp_update_estudiante(:id, :nombre, :email, :nivelHabilidad, :grado, :institucion, :tiempoDisponibleSemanal);");
        $ok = $stmt->execute([
            ':id' => $entity->getId(),
            ':nombre' => $entity->getNombre(),
            ':email' => $entity->getEmail(),
            ':nivelHabilidad' => $entity->getNivelHabilidad(),
            ':grado' => $entity->getGrado(),
            ':institucion' => $entity->getInstitucion(),
            ':tiempoDisponibleSemanal' => $entity->getTiempoDisponibleSemanal(),
        ]);

        $stmt->closeCursor();
        return $ok;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("CALL sp_delete_estudiante(:id);");
        $ok = $stmt->execute([':id' => $id]);
        $stmt->closeCursor();
        return $ok;
    }

    public function hydrate(array $row): Estudiante
    {
        return new Estudiante(
            (int)$row['id'],
            $row['nombre'] ?? '',
            $row['email'] ?? '',
            $row['nivelHabilidad'] ?? '',
            $row['grado'] ?? '',
            $row['institucion'] ?? '',
            (int)($row['tiempoDisponibleSemanal'] ?? 0)
        );
    }
}
