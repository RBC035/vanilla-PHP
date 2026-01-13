<?php
namespace App\Controllers;

use App\Models\Study;

class StudyController {
    private $studyModel;

    public function __construct() {
        $this->studyModel = new Study();
    }

    public function index() {
        $studies = $this->studyModel->findAll();
        echo json_encode($studies);
    }

    public function getCount() {
        $count = $this->studyModel->countAll();
        echo json_encode(['total' => $count]);
    }

    public function show($id) { 
        $study = $this->studyModel->findById($id);
        if ($study) {
            echo json_encode($study);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Study not found']);
        }
    }

    public function store() {
        $data = json_decode(file_get_contents("php://input"), true);
        if ($this->studyModel->create($data)) {
            http_response_code(201);
            echo json_encode(['message' => 'Study created successfully']);
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        if ($this->studyModel->update($id, $data)) {
            echo json_encode(['message' => 'Study updated successfully']);
        } else {
            http_response_code(404);
        }
    }

    public function destroy($id) {
        if ($this->studyModel->delete($id)) {
            http_response_code(204);
        }
    }

    public function countByStudent($studentId) {
        $count = $this->studyModel->countByStudentId($studentId);
        echo json_encode(['total' => $count]);
    }

    public function searchByStudent($studentId) {
        $studies = $this->studyModel->findByStudentId($studentId);
        echo json_encode($studies);
    }
} 