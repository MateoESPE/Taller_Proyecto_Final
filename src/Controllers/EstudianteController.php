<?php declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Estudiante;
use App\Repositories\EstudianteRepository;

class EstudianteController
{
    private EstudianteRepository $repository;

    public function __construct()
    {
        $this->repository = new EstudianteRepository();
    }

    public function handle(): void
    {
        header('Content-Type: application/json');
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'GET') {
            if (isset($_GET['id'])) {
                $estudiante = $this->repository->findById((int)$_GET['id']);
                echo json_encode($estudiante ? $this->estudianteToArray($estudiante) : null);
                return;
            } else {
                $list = array_map([$this, 'estudianteToArray'], $this->repository->findAll());
                echo json_encode($list);
                return;
            }
        }

        $payload = json_decode(file_get_contents('php://input'), true);

        if ($method === 'POST') {
            $estudiante = new Estudiante(
                null,
                $payload['nombre'],
                $payload['email'],
                $payload['nivelHabilidad'],
                $payload['grado'],
                $payload['institucion'],
                (int)$payload['tiempoDisponibleSemanal']
            );
            echo json_encode(['success' => $this->repository->create($estudiante)]);
            return;
        }

        if ($method === 'PUT') {
            $id = (int)($payload['id'] ?? 0);
            $existing = $this->repository->findById($id);
            if (!$existing) {
                http_response_code(404);
                echo json_encode(['error' => 'Estudiante not found']);
                return;
            }

            if (isset($payload['nombre'])) $existing->setNombre($payload['nombre']);
            if (isset($payload['email'])) $existing->setEmail($payload['email']);
            if (isset($payload['nivelHabilidad'])) $existing->setNivelHabilidad($payload['nivelHabilidad']);
            if (isset($payload['grado'])) $existing->setGrado($payload['grado']);
            if (isset($payload['institucion'])) $existing->setInstitucion($payload['institucion']);
            if (isset($payload['tiempoDisponibleSemanal'])) $existing->setTiempoDisponibleSemanal((int)$payload['tiempoDisponibleSemanal']);

            echo json_encode(['success' => $this->repository->update($existing)]);
            return;
        }

        if ($method === 'DELETE') {
            echo json_encode(['success' => $this->repository->delete((int)($payload['id'] ?? 0))]);
            return;
        }

        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
    }

    private function estudianteToArray(Estudiante $e): array
    {
        return [
            'id' => $e->getId(),
            'nombre' => $e->getNombre(),
            'email' => $e->getEmail(),
            'nivelHabilidad' => $e->getNivelHabilidad(),
            'grado' => $e->getGrado(),
            'institucion' => $e->getInstitucion(),
            'tiempoDisponibleSemanal' => $e->getTiempoDisponibleSemanal()
        ];
    }
}
