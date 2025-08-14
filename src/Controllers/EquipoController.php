<?php declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Equipo;
use App\Repositories\EquipoRepository;

class EquipoController
{
    private EquipoRepository $repository;

    public function __construct()
    {
        $this->repository = new EquipoRepository();
    }

    public function handle(): void
    {
        header('Content-Type: application/json');
        $method = $_SERVER['REQUEST_METHOD'];
        $payload = json_decode(file_get_contents('php://input'), true);

        if ($method === 'GET') {
            if (isset($_GET['id'])) {
                $equipo = $this->repository->findById((int)$_GET['id']);
                echo json_encode($equipo ? $this->equipoToArray($equipo) : null);
                return;
            } else {
                $list = array_map([$this, 'equipoToArray'], $this->repository->findAll());
                echo json_encode($list);
                return;
            }
        }

        if ($method === 'POST') {
            $equipo = new Equipo(
                0,
                $payload['nombre'] ?? '',
                $payload['hackathonId'] ?? '',
                $payload['participantes'] ?? [],
                $payload['retosAsignados'] ?? []
            );
            echo json_encode(['success' => $this->repository->create($equipo)]);
            return;
        }

        if ($method === 'PUT') {
            $id = (int)($payload['id'] ?? 0);
            $existing = $this->repository->findById($id);
            if (!$existing) {
                http_response_code(404);
                echo json_encode(['error' => 'Equipo not found']);
                return;
            }

            if (isset($payload['nombre'])) $existing->setNombre($payload['nombre']);
            if (isset($payload['hackathonId'])) $existing->setHackathonId($payload['hackathonId']);
            if (isset($payload['participantes'])) $existing->setParticipantes($payload['participantes']);
            if (isset($payload['retosAsignados'])) $existing->setRetosAsignados($payload['retosAsignados']);

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

    private function equipoToArray(Equipo $equipo): array
    {
        return [
            'id' => $equipo->getId(),
            'nombre' => $equipo->getNombre(),
            'hackathonId' => $equipo->getHackathonId(),
            'participantes' => $equipo->getParticipantes(),
            'retosAsignados' => $equipo->getRetosAsignados()
        ];
    }
}
