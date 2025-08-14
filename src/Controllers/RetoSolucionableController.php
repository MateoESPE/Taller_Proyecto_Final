<?php declare(strict_types=1);

namespace App\Controllers;

use App\Entities\RetoSolucionable;
use App\Repositories\RetoSolucionableRepository;

class RetoSolucionableController
{
    private RetoSolucionableRepository $retoRepo;

    public function __construct()
    {
        $this->retoRepo = new RetoSolucionableRepository();
    }

    public function handle(): void
    {
        header('Content-Type: application/json');
        $method = $_SERVER['REQUEST_METHOD'];
        $payload = json_decode(file_get_contents('php://input'), true);

        if ($method === 'GET') {
            if (isset($_GET['id'])) {
                $reto = $this->retoRepo->findById((int)$_GET['id']);
                echo json_encode($reto ? $this->retoToArray($reto) : null);
            } else {
                $list = array_map([$this, 'retoToArray'], $this->retoRepo->findAll());
                echo json_encode($list);
            }
            return;
        }

        if ($method === 'POST') {
            $reto = new RetoSolucionable(
                null,
                $payload['titulo'] ?? '',
                $payload['descripcion'] ?? '',
                $payload['complejidad'] ?? '',
                $payload['areasConocimiento'] ?? []
            );
            echo json_encode(['success' => $this->retoRepo->create($reto)]);
            return;
        }

        if ($method === 'PUT') {
            $id = (int)($payload['id'] ?? 0);
            $existing = $this->retoRepo->findById($id);
            if (!$existing) {
                http_response_code(404);
                echo json_encode(['error' => 'RetoSolucionable not found']);
                return;
            }

            if (isset($payload['titulo'])) $existing->setTitulo($payload['titulo']);
            if (isset($payload['descripcion'])) $existing->setDescripcion($payload['descripcion']);
            if (isset($payload['complejidad'])) $existing->setComplejidad($payload['complejidad']);
            if (isset($payload['areasConocimiento'])) $existing->setAreasConocimiento($payload['areasConocimiento']);

            echo json_encode(['success' => $this->retoRepo->update($existing)]);
            return;
        }

        if ($method === 'DELETE') {
            echo json_encode(['success' => $this->retoRepo->delete((int)($payload['id'] ?? 0))]);
            return;
        }

        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
    }

    private function retoToArray(RetoSolucionable $reto): array
    {
        return [
            'id' => $reto->getId(),
            'titulo' => $reto->getTitulo(),
            'descripcion' => $reto->getDescripcion(),
            'complejidad' => $reto->getComplejidad(),
            'areasConocimiento' => $reto->getAreasConocimiento()
        ];
    }
}
