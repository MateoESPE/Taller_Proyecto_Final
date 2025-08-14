<?php declare(strict_types=1);

namespace App\Controllers;

use App\Entities\MentorTecnico;
use App\Repositories\MentorTecnicoRepository;

class MentorTecnicoController
{
    private MentorTecnicoRepository $repository;

    public function __construct()
    {
        $this->repository = new MentorTecnicoRepository();
    }

    public function handle(): void
    {
        header('Content-Type: application/json');
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'GET') {
            if (isset($_GET['id'])) {
                $mentor = $this->repository->findById((int)$_GET['id']);
                echo json_encode($mentor ? $this->mentorToArray($mentor) : null);
                return;
            } else {
                $list = array_map([$this, 'mentorToArray'], $this->repository->findAll());
                echo json_encode($list);
                return;
            }
        }

        $payload = json_decode(file_get_contents('php://input'), true);

        if ($method === 'POST') {
            $mentor = new MentorTecnico(
                null,
                $payload['nombre'],
                $payload['email'],
                $payload['nivelHabilidad'],
                $payload['especialidad'],
                (int)$payload['experiencia'],
                $payload['disponibilidadHoraria']
            );
            echo json_encode(['success' => $this->repository->create($mentor)]);
            return;
        }

        if ($method === 'PUT') {
            $id = (int)($payload['id'] ?? 0);
            $existing = $this->repository->findById($id);
            if (!$existing) {
                http_response_code(404);
                echo json_encode(['error' => 'Mentor not found']);
                return;
            }

            if (isset($payload['nombre'])) $existing->setNombre($payload['nombre']);
            if (isset($payload['email'])) $existing->setEmail($payload['email']);
            if (isset($payload['nivelHabilidad'])) $existing->setNivelHabilidad($payload['nivelHabilidad']);
            if (isset($payload['especialidad'])) $existing->setEspecialidad($payload['especialidad']);
            if (isset($payload['experiencia'])) $existing->setExperiencia((int)$payload['experiencia']);
            if (isset($payload['disponibilidadHoraria'])) $existing->setDisponibilidadHoraria($payload['disponibilidadHoraria']);

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

    private function mentorToArray(MentorTecnico $m): array
    {
        return [
            'id' => $m->getId(),
            'nombre' => $m->getNombre(),
            'email' => $m->getEmail(),
            'nivelHabilidad' => $m->getNivelHabilidad(),
            'especialidad' => $m->getEspecialidad(),
            'experiencia' => $m->getExperiencia(),
            'disponibilidadHoraria' => $m->getDisponibilidadHoraria()
        ];
    }
}
