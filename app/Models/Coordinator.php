<?php

namespace App\Models;

class Coordinator extends BaseModel
{
    protected $table = 'coordinator';
    protected $primaryKey = 'coordinator_id';

    public function findByEmailAddress($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email_address = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (full_name, phone_number, gender, coordinator_status, email_address) 
                VALUES (:fullName, :phoneNumber, :gender, :coordinatorStatus, :emailAddress)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'fullName'          => $data['fullName'],
            'phoneNumber'       => $data['phoneNumber'] ?? null,
            'gender'            => $data['gender'] ?? null,
            'coordinatorStatus' => $data['coordinatorStatus'] ?? 'enable',
            'emailAddress'      => $data['emailAddress']
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table}
            SET 
                full_name = :full_name,
                phone_number = :phone_number,
                gender = :gender,
                email_address = :email_address,
                coordinator_status = :coordinator_status
            WHERE {$this->primaryKey} = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'full_name'          => $data['full_name'],
            'phone_number'       => $data['phone_number'],
            'gender'             => $data['gender'],
            'email_address'      => $data['email_address'],
            'coordinator_status' => $data['coordinator_status'],
            'id'                 => $id
        ]);
    }
}
