<?php

namespace App\Controllers;

use App\Models\Coordinator;
use App\Models\User;
use App\Helpers\Response;
use App\Services\PasswordGeneratorService;
use Config\Database;
use App\Services\EmailService;

class CoordinatorController
{
    private $coordinatorModel;
    private $userModel;
    private $passwordGenerator;

    private $emailService;

    public function __construct()
    {
        $this->coordinatorModel = new Coordinator();
        $this->userModel = new User();
        $this->passwordGenerator = new PasswordGeneratorService();
        $this->emailService = new EmailService();
    }

    public function index()
    {
        Response::json($this->coordinatorModel->findAll());
    }

    public function count()
    {
        Response::json($this->coordinatorModel->countAll());
    }

    public function show($id)
    {
        $item = $this->coordinatorModel->findById($id);
        $item ? Response::json(data: $item) : Response::error("Coordinator not found", 404);
    }

    public function getByEmail()
    {
        $email = $_GET['emailAddress'] ?? $_GET['email'] ?? null;

        if (!$email) {
            Response::error("Email address is required", 400);
        }

        $coordinator = $this->coordinatorModel->findByEmailAddress($email);

        if ($coordinator) {
            Response::json($coordinator);
        } else {
            Response::error("Coordinator not found", 404);
        }
    }


    public function create()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['fullName']) || empty($data['emailAddress'])) {
            Response::error("Full name and email are required", 400);
        }

        if ($this->coordinatorModel->findByEmailAddress($data['emailAddress'])) {
            Response::error("Email address already registered", 400);
        }

        $db = Database::getConnection();
        try {
            $db->beginTransaction();

            $this->coordinatorModel->create($data);
            $coordinatorId = $this->coordinatorModel->lastInsertId();

            $plainPassword = $this->passwordGenerator->generateSecurePassword();

            $this->userModel->create([
                'username' => $data['emailAddress'],
                'password' => $plainPassword,
                'role'     => 'Coordinator'
            ]);

            // $emailSent = $this->emailService->sendWelcomeEmail(
            //     $data['emailAddress'],
            //     $data['fullName'],
            //     $data['emailAddress'],
            //     $plainPassword
            // );

            // if (!$emailSent) {
            //     throw new \Exception("Failed to send welcome email");
            // }

            $db->commit();
            Response::json($data, "Coordinator registered successfully. Credentials created.", 201);
        } catch (\Exception $e) {
            $db->rollBack();
            Response::error("Error creating coordinator: " . $e->getMessage(), 500);
        }
    }

    public function updateStatus($id)
    {
        $status = $_GET['coordinatorStatus'] ?? null;
        if ($this->coordinatorModel->updateStatus($id, $status)) {
            Response::json(null, "Status updated to $status");
        } else {
            Response::error("Update failed", 404);
        }
    }

    public function update($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$id) {
            Response::error("Coordinator ID is required", 400);
        }

        if (
            empty($data['fullName']) ||
            empty($data['phoneNumber']) ||
            empty($data['gender'])
        ) {
            Response::error("Full name, phone number and gender are required", 400);
        }

        $updated = $this->coordinatorModel->update($id, [
            'full_name'          => $data['fullName'],
            'phone_number'       => $data['phoneNumber'],
            'gender'             => $data['gender'],
            'email_address'      => $data['emailAddress'] ?? null,
            'coordinator_status' => $data['coordinatorStatus'] ?? null,
        ]);

        if (!$updated) {
            Response::error("Failed to update coordinator", 500);
        }

        Response::json(null, "Coordinator updated successfully");
    }


    public function delete($id)
    {
        if ($this->coordinatorModel->delete($id)) {
            http_response_code(204);
            exit;
        }
        Response::error("Delete failed", 404);
    }
}
