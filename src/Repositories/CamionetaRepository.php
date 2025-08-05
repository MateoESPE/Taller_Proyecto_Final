<?php declare(strict_types=1);

namespace App\Repositories;

use App\Config\Database;
use App\Interfaces\RepositoryInterface;
use PDO;

class CamionetaRepository implements RepositoryInterface
{
    private PDO $db;
    
    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(object $entity): bool{
        if (!$entity instanceof Camioneta) {
            throw new \InvalidArgumentException("Camioneta expected");
        }

        $stmt = $this->db->prepare("CALL sp_create_camioneta(:marca, :modelo, :color, :anio, :cabina, :capacidad);");
        $ok = $stmt->execute([
            ':marca' => $entity->getMarca(),
            ':modelo' => $entity->getModelo(),
            ':color' => $entity->getColor(),
            ':anio' => $entity->getAnio(),
            ':cabina' => $entity->getCabina(),
            ':capacidad' => $entity->getCapacidad()
        ]);

        if ($ok) {
            $stmt->fetch();
        }

        $stmt->closeCursor();
        return $ok;
    }

    public function findById(int $id): ?object{
        $stmt = $this->db->prepare("CALL sp_find_camioneta(:id);");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        $stmt->closeCursor();

        return $row ? $this->hydrate($row) : null;
    }

    public function update(object $entity): bool{
        if (!$entity instanceof Camioneta) {
            throw new \InvalidArgumentException("Camioneta expected");
        }
        $stmt = $this->db->prepare("CALL sp_update_camioneta(:id, :marca, :modelo, :color, :anio, :cabina, :capacidad);");
        $ok = $stmt->execute([
            ':id' => $entity->getId(),
            ':marca' => $entity->getMarca(),
            ':modelo' => $entity->getModelo(),
            ':color' => $entity->getColor(),
            ':anio' => $entity->getAnio(),
            ':cabina' => $entity->getCabina(),
            ':capacidad' => $entity->getCapacidad()
        ]);

        if ($ok) {
            $stmt->fetch();
        }

        $stmt->closeCursor();
        return $ok;
    }

    public function delete(int $id): bool{
        $stmt = $this->db->prepare("CALL sp_delete_camioneta(:id);");
        $ok = $stmt->execute([':id' => $id]);

        if ($ok){
            $stmt -> fetch();
        }
        $stmt->closeCursor();
        return $ok;
    }

    public function findAll(): array{
        $stmt = $this->db->query("CALL sp_camioneta_list();");
        $rows = $stmt->fetchAll();
        $stmt->closeCursor();

        $out = [];

        foreach ($rows as $r) {
            $out[] = $this->hydrate($r);
        }
        return $out;
    }

    public function hydrate(array $row): Camioneta{
        return new Camioneta(
            (int)$row['auto_id'],
            $row['marca'],
            $row['modelo'],
            $row['color'],
            (int)$row['anio'],
            $row['cabina'],
            (float)$row['capacidad']
        );
    }
}