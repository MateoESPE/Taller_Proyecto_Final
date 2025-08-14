<?php declare(strict_types=1);

namespace App\Controllers;

use App\Entities\RetoReal;
use App\Repositories\RetoRealRepository;

class RetoRealController
{
    private RetoRealRepository $retoRealRepository;

    public function __construct()
    {
        $this->retoRealRepository = new RetoRealRepository();
    }

    public function handle(): void
    {
        header('Content-Type: application/json');
        $method = $_SERVER['REQUEST_METHOD'];
        $payload = json_decode(file_get_contents('php://input'), true);

        if ($method === 'GET') {
            if (isset($_GET['id'])) {
                $reto = $this->retoRealRepository->findById((int)$_GET['id']);
                echo json_encode($reto ? $this->retoToArray($reto) : null);
            } else {
                $list = array_map([$this, 'retoToArray'], $this->retoRealRepository->findAll());
                echo json_encode($list);
            }
            return;
        }

        if ($method === 'POST') {
            $reto = new RetoReal(
                null,
                $payload['titulo'] ?? '',
                $payload['descripcion'] ?? '',
                $payload['complejidad'] ?? '',
                $payload['areasConocimiento'] ?? [],
                $payload['entidadColaboradora'] ?? ''
            );
            echo json_encode(['success' => $this->retoRealRepository->create($reto)]);
            return;
        }

        if ($method === 'PUT') {
            $id = (int)($payload['id'] ?? 0);
            $existing = $this->retoRealRepository->findById($id);
            if (!$existing) {
                http_response_code(404);
                echo json_encode(['error' => 'RetoReal not found']);
                return;
            }

            if (isset($payload['titulo'])) $existing->setTitulo($payload['titulo']);
            if (isset($payload['descripcion'])) $existing->setDescripcion($payload['descripcion']);
            if (isset($payload['complejidad'])) $existing->setComplejidad($payload['complejidad']);
            if (isset($payload['areasConocimiento'])) $existing->setAreasConocimiento($payload['areasConocimiento']);
            if (isset($payload['entidadColaboradora'])) $existing->setEntidadColaboradora($payload['entidadColaboradora']);

            echo json_encode(['success' => $this->retoRealRepository->update($existing)]);
            return;
        }

        if ($method === 'DELETE') {
            echo json_encode(['success' => $this->retoRealRepository->delete((int)($payload['id'] ?? 0))]);
            return;
        }

        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
    }

    private function retoToArray(RetoReal $reto): array
    {
        return [
            'id' => $reto->getId(),
            'titulo' => $reto->getTitulo(),
            'descripcion' => $reto->getDescripcion(),
            'complejidad' => $reto->getComplejidad(),
            'areasConocimiento' => $reto->getAreasConocimiento(),
            'entidadColaboradora' => $reto->getEntidadColaboradora()
        ];
    }
}
