<?php

namespace App\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Helpers\Response;
use App\Services\PasswordGeneratorService;

class StudentController
{

    private $studentModel;
    private $userModel;
    private $passwordGenerator;


    public function __construct()
    {
        $this->studentModel = new Student();
        $this->userModel = new User();
        $this->passwordGenerator = new PasswordGeneratorService();
    }

    public function index()
    {
        $students = $this->studentModel->findAll();
        Response::json(data: $students);
    } 

    public function count()
    {
        $count = $this->studentModel->countAll();
        Response::json(data: $count);
    }

    public function show($id)
    {
        $student = $this->studentModel->findById($id);
        if ($student) {
            Response::json($student);
        } else {
            Response::error("Student not found", 404);
        }
    }

    public function create()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['fullName']) || empty($data['emailAddress'])) {
            Response::error("Full name and email address are required", 400);
        }

        if ($this->studentModel->findByEmailAddress($data['emailAddress'])) {
            Response::error("Email address already registered", 400);
        }

        if ($this->studentModel->create($data)) {
            $studentId = $this->studentModel->lastInsertId();

            $plainPassword = $this->passwordGenerator->generateSecurePassword();

            $userData = [
                'username' => $data['emailAddress'],
                'password' => $plainPassword,
                'role'     => 'Student'
            ];

            if ($this->userModel->create($userData)) {
                Response::json($data, "Student registered and user credentials created successfully", 201);
            } else {
                Response::error("Student created but failed to create login credentials", 500);
            }
        } else {
            Response::error("Error creating student", 500);
        }
    }

    public function update($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if ($this->studentModel->update($id, $data)) {
            Response::json($data, "Student updated successfully");
        } else {
            Response::error("Update failed", 404);
        }
    } 

    public function updateStatus($id)
    {
        $status = $_GET['studentStatus'] ?? null;
        if ($this->studentModel->updateStatus($id, $status)) {
            Response::json(null, "Status updated to $status");
        } else {
            Response::error("Failed to update status", 404);
        }
    }

    public function delete($id)
    {
        if ($this->studentModel->delete($id)) {
            http_response_code(204);
            exit;
        }
        Response::error("Delete failed", 404);
    }

    public function searchByName()
    {
        $name = $_GET['fullName'] ?? '';
        Response::json($this->studentModel->findByFullName($name));
    }

    public function searchByEmail()
    {
        $email = $_GET['emailAddress'] ?? '';
        $student = $this->studentModel->findByEmailAddress($email);
        $student ? Response::json($student) : Response::error("Student not found", 404);
    }

    public function searchByGender()
    {
        $gender = $_GET['gender'] ?? '';
        Response::json($this->studentModel->findByGender($gender));
    }

    public function searchByPhone()
    {
        $phone = $_GET['phoneNumber'] ?? '';
        Response::json($this->studentModel->findByPhoneNumber($phone));
    }

    // public function uploadImage($id)
    // {
    //     if (!isset($_FILES['profileImage'])) {
    //         Response::error("No file uploaded", 400);
    //     }

    //     $dir = "uploads/profile/";
    //     if (!is_dir($dir)) mkdir($dir, 0777, true);

    //     $fileName = time() . "_" . $_FILES['profileImage']['name'];
    //     $filePath = $dir . $fileName;

    //     if (move_uploaded_file($_FILES['profileImage']['tmp_name'], $filePath)) {
    //         $this->studentModel->updateProfileImage($id, $filePath);
    //         Response::json(["path" => $filePath], "Image uploaded successfully");
    //     } else {
    //         Response::error("Upload failed", 500);
    //     }
    // }

public function uploadImage($id)
{
    if (!isset($_FILES['profileImage'])) {
        Response::error("No file uploaded", 400);
        return;
    }

    $uploadDir = "uploads/profile/";
    $absoluteDir = $_SERVER['DOCUMENT_ROOT'] . '/' . $uploadDir;
    
    if (!is_dir($absoluteDir)) {
        mkdir($absoluteDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES['profileImage']['name']);
    $absolutePath = $absoluteDir . $fileName;
    
    $storedPath = "profile/" . $fileName;

    if (move_uploaded_file($_FILES['profileImage']['tmp_name'], $absolutePath)) {
        if ($this->studentModel->updateProfileImage($id, $storedPath)) {
            Response::json(["path" => $storedPath], "Image uploaded successfully", 200);
        } else {
            Response::error("Failed to save image path to database", 500);
        }
    } else {
        Response::error("Upload failed", 500);
    }
}

    // public function getProfileImage($id)
    // {
    //     $student = $this->studentModel->findById($id);
        
    //     if (!$student) {
    //         Response::error("Student not found", 404);
    //         return;
    //     }

    //     if (!isset($student['profile_image']) || empty($student['profile_image'])) {
    //         Response::error("No profile image found", 404);
    //         return;
    //     }

    //     $filePath = $student['profile_image'];

    //     if (!file_exists($filePath)) {
    //         Response::error("File not found on server", 404);
    //         return;
    //     }

    //     $mimeType = mime_content_type($filePath);
        
    //     if (ob_get_level()) {
    //         ob_end_clean();
    //     }

    //     header('Content-Type: ' . $mimeType);
    //     header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
    //     header('Content-Length: ' . filesize($filePath));
    //     header('Cache-Control: public, max-age=3600');
        
    //     readfile($filePath);
    //     exit;
    // }

//     public function getProfileImage($id)
// {
//     $student = $this->studentModel->findById($id);
    
//     if (!$student) {
//         Response::error("Student not found", 404);
//         return;
//     }

//     if (!isset($student['profile_image']) || empty($student['profile_image'])) {
//         Response::error("No profile image found", 404);
//         return;
//     }

//     $storedPath = trim($student['profile_image']);
    
//     $documentRoot = $_SERVER['DOCUMENT_ROOT'];
//     $filePath = $documentRoot . '/uploads/' . $storedPath;
    
//     $filePath = realpath($filePath);
    
//     if (!$filePath || !file_exists($filePath)) {
//         Response::error("Profile image file not found", 404);
//         return;
//     }
    
//     $allowedDir = realpath($documentRoot . '/uploads/profile');
//     if ($allowedDir && strpos($filePath, $allowedDir) !== 0) {
//         Response::error("Access denied", 403);
//         return;
//     }

//     $mimeType = mime_content_type($filePath);
    
//     if (ob_get_level()) {
//         ob_end_clean();
//     }

//     header('Content-Type: ' . $mimeType);
//     header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
//     header('Content-Length: ' . filesize($filePath));
//     header('Cache-Control: public, max-age=3600');
//     header('Access-Control-Allow-Origin: *');
    
//     readfile($filePath);
//     exit;
// }
public function getProfileImage($id)
{
    $student = $this->studentModel->findById($id);
    
    if (!$student) {
        http_response_code(404);
        echo json_encode(["error" => "Student not found"]);
        exit;
    }

    $profileImage = isset($student['profile_image']) ? trim($student['profile_image']) : null;
    
    if (!$profileImage || empty($profileImage)) {
        http_response_code(404);
        echo json_encode(["error" => "No profile image found"]);
        exit;
    }

    $documentRoot = $_SERVER['DOCUMENT_ROOT'];
    $fullPath = $documentRoot . '/uploads/' . $profileImage;
    
    $fullPath = realpath($fullPath);
    
    if (!$fullPath || !file_exists($fullPath)) {
        http_response_code(404);
        echo json_encode(["error" => "File not found: " . $profileImage]);
        exit;
    }

    $allowedDir = realpath($documentRoot . '/uploads/profile');
    if ($allowedDir && strpos($fullPath, $allowedDir) !== 0) {
        http_response_code(403);
        echo json_encode(["error" => "Access denied"]);
        exit;
    }

    $fileExtension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
    $mimeTypes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp'
    ];
    
    $mimeType = isset($mimeTypes[$fileExtension]) ? $mimeTypes[$fileExtension] : 'application/octet-stream';
    
    if (ob_get_level()) {
        ob_end_clean();
    }

    header('Content-Type: ' . $mimeType);
    header('Content-Disposition: inline; filename="' . basename($fullPath) . '"');
    header('Content-Length: ' . filesize($fullPath));
    header('Cache-Control: public, max-age=86400');
    header('Access-Control-Allow-Origin: *');
    header('Expires: ' . gmdate('D, d M Y H:i:s T', time() + 86400));
    
    readfile($fullPath);
    exit;
}
}
