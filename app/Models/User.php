<?php

namespace App\Models;

class User extends BaseModel
{
    protected $table = 'user_role';
    protected $primaryKey = 'user_id';

    public function findByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (username, password, role, created_at, updated_at) 
                VALUES (:username, :password, :role, NOW(), NOW())";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role'     => $data['role'] ?? 'ROLE_STUDENT'
        ]);
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function updatePassword($id, $hashedPassword)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET password = :password,
                updated_at = NOW()
            WHERE {$this->primaryKey} = :id
        ");

        return $stmt->execute([
            'password' => $hashedPassword,
            'id'       => $id
        ]);
    }
}
