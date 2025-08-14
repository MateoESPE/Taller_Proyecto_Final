<?php
namespace App\Controllers;

use App\Models\Book;

class BookController {

    public function handle() {
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                $this->list();
                break;
            case 'POST':
                $this->createOrUpdate();
                break;
            case 'DELETE':
                $this->delete();
                break;
        }
    }

    private function delete() {
        // parseamos el id enviado por ExtJS
        parse_str(file_get_contents("php://input"), $data);
        $id = $data['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID missing']);
            return;
        }

        $book = new Book();
        $deleted = $book->delete($id); // implementa delete en tu modelo

        if ($deleted) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Delete failed']);
        }
    }

    // ... list(), createOrUpdate() etc
}
