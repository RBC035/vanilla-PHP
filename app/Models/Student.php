<?php

namespace App\Models;

class Student extends BaseModel
{
    protected $table = 'student';
    protected $primaryKey = 'student_id';

    public function findAll()
    { 
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY full_name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByFullName($fullName)
    {
        if (empty($fullName)) {
            return [];
        }

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE full_name LIKE ? ORDER BY full_name ASC");
        $stmt->execute(["%$fullName%"]);
        return $stmt->fetchAll();
    }

    public function findByEmailAddress($emailAddress)
    {
        if (empty($emailAddress)) {
            return null;
        }

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email_address = ?");
        $stmt->execute([$emailAddress]);
        return $stmt->fetch();
    }

    public function findByGender($gender)
    {
        if (empty($gender)) {
            return [];
        }

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE gender = ? ORDER BY full_name ASC");
        $stmt->execute([$gender]);
        return $stmt->fetchAll();
    }

    public function findByPhoneNumber($phoneNumber)
    {
        if (empty($phoneNumber)) {
            return [];
        }

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE phone_number = ? ORDER BY full_name ASC");
        $stmt->execute([$phoneNumber]);
        return $stmt->fetchAll();
    }

    public function countAll()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table}");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? (int)$result['total'] : 0;
    }

    public function countByStatus($status)
    {
        if (empty($status)) {
            return 0;
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE student_status = ?");
        $stmt->execute([$status]);
        $result = $stmt->fetch();
        return $result ? (int)$result['total'] : 0;
    }

    public function create($data)
    {
        if (empty($data['fullName']) || empty($data['emailAddress'])) {
            return false;
        }

        $sql = "INSERT INTO {$this->table} (full_name, email_address, gender, phone_number, age_group, student_status) 
                VALUES (:fullName, :emailAddress, :gender, :phoneNumber, :ageGroup, :studentStatus)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'fullName'      => trim($data['fullName']),
            'emailAddress'  => strtolower(trim($data['emailAddress'])),
            'gender'        => $data['gender'] ?? null,
            'phoneNumber'   => $data['phoneNumber'] ?? null,
            'ageGroup'      => $data['ageGroup'] ?? null,
            'studentStatus' => $data['studentStatus'] ?? 'enable'
        ]);
    }

    public function update($id, $data)
    {
        if (empty($id)) {
            return false;
        }

        $fields = [];
        $params = ['id' => $id];

        if (!empty($data['fullName'])) {
            $fields[] = 'full_name = :fullName';
            $params['fullName'] = trim($data['fullName']);
        }

        if (!empty($data['emailAddress'])) {
            $fields[] = 'email_address = :emailAddress';
            $params['emailAddress'] = strtolower(trim($data['emailAddress']));
        }

        if (!empty($data['phoneNumber'])) {
            $fields[] = 'phone_number = :phoneNumber';
            $params['phoneNumber'] = $data['phoneNumber'];
        }

        if (!empty($data['ageGroup'])) {
            $fields[] = 'age_group = :ageGroup';
            $params['ageGroup'] = $data['ageGroup'];
        }

        if (!empty($data['gender'])) {
            $fields[] = 'gender = :gender';
            $params['gender'] = $data['gender'];
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function updateStatus($id, $status)
    {
        if (empty($id) || empty($status)) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE {$this->table} SET student_status = ? WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$status, $id]); 
    }

    public function updateProfileImage($id, $imagePath)
    {
        if (empty($id) || empty($imagePath)) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE {$this->table} SET profile_image = ? WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$imagePath, $id]);
    }

    public function delete($id)
    {
        if (empty($id)) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    public function lastInsertId()
    {
        return $this->db->lastInsertId();
    }
}
