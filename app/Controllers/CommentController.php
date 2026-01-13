<?php

namespace App\Controllers;

use App\Models\Comment;

class CommentController
{
    private $commentModel;

    public function __construct()
    {
        $this->commentModel = new Comment();
    }

    public function store()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid JSON data']);
                return;
            }

            if ($this->commentModel->create($data)) {
                http_response_code(201);
                echo json_encode(['message' => 'Comment created successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Failed to create comment - missing required fields']);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function index()
    {
        try {
            $comments = $this->commentModel->findAll();
            echo json_encode($comments);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            if (empty($id)) {
                http_response_code(400);
                echo json_encode(['error' => 'Comment ID is required']);
                return;
            }

            $comment = $this->commentModel->findById($id);

            if ($comment) {
                echo json_encode($comment);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Comment not found']);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getByCoordinatorEmail()
    {
        try {
            $email = $_GET['coordinatorEmail'] ?? '';

            if (empty($email)) {
                http_response_code(400);
                echo json_encode(['error' => 'Coordinator email is required']);
                return;
            }

            $comments = $this->commentModel->findByCoordinatorEmail($email);
            echo json_encode($comments);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getByProgressId()
    {
        try {
            $progressId = $_GET['progressId'] ?? null;

            if (!$progressId) {
                http_response_code(400);
                echo json_encode(['error' => 'Progress ID is required']);
                return;
            }

            $comments = $this->commentModel->findByProgressId($progressId);
            echo json_encode($comments);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getByDate()
    {
        try {
            $date = $_GET['commentedDate'] ?? '';

            if (empty($date)) {
                http_response_code(400);
                echo json_encode(['error' => 'Date is required']);
                return;
            }

            $comments = $this->commentModel->findByDate($date);
            echo json_encode($comments);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function update($id)
    {
        try {
            if (empty($id)) {
                http_response_code(400);
                echo json_encode(['error' => 'Comment ID is required']);
                return;
            }

            $data = json_decode(file_get_contents("php://input"), true);

            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid JSON data']);
                return;
            }

            if ($this->commentModel->update($id, $data)) {
                echo json_encode(['message' => 'Comment updated successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Failed to update comment']);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            if (empty($id)) {
                http_response_code(400);
                echo json_encode(['error' => 'Comment ID is required']);
                return;
            }

            if ($this->commentModel->delete($id)) {
                http_response_code(204);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Comment not found']);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function count()
    {
        try {
            $total = $this->commentModel->countAll();
            echo json_encode(['total' => $total]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function countByProgressId()
    {
        try {
            $progressId = $_GET['progressId'] ?? null;

            if (!$progressId) {
                http_response_code(400);
                echo json_encode(['error' => 'Progress ID is required']);
                return;
            }

            $total = $this->commentModel->countByProgressId($progressId);
            echo json_encode(['total' => $total]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
