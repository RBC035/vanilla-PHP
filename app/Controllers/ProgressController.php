<?php

namespace App\Controllers;

use App\Helpers\Response;
use App\Models\Progress;

class ProgressController
{
    private $progressModel;
    private $uploadDir;

    public function __construct()
    {
        $this->progressModel = new Progress();
        $this->uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/";
    }

    public function index()
    {
        echo json_encode(value: ['data' => $this->progressModel->findAll()]);
    }

    public function show($id)
    {
        $result = $this->progressModel->findById($id);
        echo $result ? json_encode($result) : json_encode(['message' => 'Not Found']);
    }

    public function getCount()
    {
        $count = $this->progressModel->countAll();
        echo json_encode(['data' => $count]);
    }

    public function getByStudent($studentId)
    {
        echo json_encode(value: $this->progressModel->findByStudentId($studentId));
    }

    public function getByStudy($studyId)
    {
        echo json_encode($this->progressModel->findByStudyId($studyId));
    }

    public function getLatestFive($studentId)
    {
        echo json_encode(value: $this->progressModel->findLatestByStudent($studentId, 5));
    }

    public function getByDate()
    {
        $date = $_GET['progressDate'] ?? null;
        if (!$date) {
            http_response_code(400);
            echo json_encode(['message' => 'progressDate parameter is required']);
            return;
        }
        echo json_encode($this->progressModel->findByProgressDate($date));
    }

    public function getByCompletion()
    {
        $completion = $_GET['studyComplition'] ?? null;
        if (!$completion) {
            http_response_code(400);
            echo json_encode(['message' => 'studyComplition parameter is required']);
            return;
        }
        echo json_encode($this->progressModel->findByStudyCompletion($completion));
    }

    public function getByThesis()
    {
        $submitThesis = $_GET['submitThesis'] ?? null;
        if (!$submitThesis) {
            http_response_code(400);
            echo json_encode(['message' => 'submitThesis parameter is required']);
            return;
        }
        echo json_encode($this->progressModel->findBySubmitThesis($submitThesis));
    }

    public function getBySupervisorMeeting()
    {
        $meetSupervisor = $_GET['meetSupervisor'] ?? null;
        if (!$meetSupervisor) {
            http_response_code(400);
            echo json_encode(['message' => 'meetSupervisor parameter is required']);
            return;
        }
        echo json_encode($this->progressModel->findByMeetSupervisor($meetSupervisor));
    }

    public function getByResponse()
    {
        $supervisorResponse = $_GET['supervisorResponse'] ?? null;
        if (!$supervisorResponse) {
            http_response_code(400);
            echo json_encode(['message' => 'supervisorResponse parameter is required']);
            return;
        }
        echo json_encode($this->progressModel->findBySupervisorResponse($supervisorResponse));
    }

    public function getCountByStudent($studentId)
    {
        echo json_encode(['data' => $this->progressModel->countByStudent($studentId)]);
    }

    public function getLatestByStudent($studentId)
    {
        echo json_encode($this->progressModel->findLatestByStudent($studentId));
    }

    public function store()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $newProgressId = $this->progressModel->create($data);

        if ($newProgressId) {
            http_response_code(201);
            echo json_encode([
                'message' => 'Progress saved',
                'progressId' => $newProgressId
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to save progress']);
        }
    }

    public function updateStatus($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $status = $data['progressStatus'] ?? null;
        $comments = $data['comments'] ?? 'No additional comments provided.';

        if ($this->progressModel->update($id, ['progress_status' => $status])) {
            if (strtolower($status) === 'rejected') {
                $this->handleRejectionEmail($id, $comments);
            }
            echo json_encode(['message' => 'Status updated successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Progress record not found']);
        }
    }

    private function handleRejectionEmail($progressId, $comments)
    {
        $details = $this->progressModel->getStudentDetailsByProgressId($progressId);

        if ($details) {
            $emailService = new \App\Services\EmailService();
            $emailService->sendRejectedProgressEmail(
                $details['email'],
                $details['full_name'],
                date('F Y'),
                'Rejected',
                $comments
            );
        }
    }

    public function uploadFile($id)
    {
        if (!isset($_FILES['file'])) {
            http_response_code(400);
            echo json_encode(['message' => 'No file uploaded']);
            return;
        }

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }

        $uniqueFileName = time() . "_" . basename($_FILES['file']['name']);
        $targetPath = $this->uploadDir . $uniqueFileName;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
            $this->progressModel->update($id, ['document_path' => $uniqueFileName]);

            echo json_encode([
                'message' => 'File uploaded',
                'filename' => $uniqueFileName
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Upload failed']);
        }
    }

    public function update($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if ($this->progressModel->update($id, $data)) {
            Response::json($data, "Progress data updated successfully");
        } else {
            Response::error("Update error", 404);
        }
    }

    public function download($filename)
    {
        // Clean the filename to prevent directory traversal attacks
        $filename = basename($filename);
        $filePath = $this->uploadDir . $filename;

        if (file_exists($filePath)) {
            // Clear any previous output (avoids corrupted PDF data)
            if (ob_get_level()) ob_end_clean();

            header('Content-Type: application/pdf');
            // 'inline' opens in browser, 'attachment' forces download
            header('Content-Disposition: inline; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($filePath));

            readfile($filePath);
            exit; // Important: Stop execution here
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'File not found', 'path' => $filePath]);
            exit;
        }
    }

    public function destroy($id)
    {
        if ($this->progressModel->delete($id)) {
            http_response_code(204);
        }
    }
}
