<?php

namespace App\Models;

class Study extends BaseModel
{
    protected $table = 'study';
    protected $primaryKey = 'study_id';

    public function findAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByLevel($level)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE level = ?");
        $stmt->execute([$level]);
        return $stmt->fetchAll();
    }

    public function findByUniversity($universityName)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE univeristy_name = ?");
        $stmt->execute([$universityName]);
        return $stmt->fetchAll();
    }

    public function findByCountry($country)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE country = ?");
        $stmt->execute([$country]);
        return $stmt->fetchAll();
    }

    public function findByStudentId($studentId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE student_id = ?");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public function create($data)
    {
        if (empty($data['level']) || empty($data['univeristyName']) || empty($data['studentId'])) {
            throw new \Exception('Level, university name, and student ID are required');
        }

        $sql = "INSERT INTO {$this->table} 
                (level, univeristy_name, country, finish_date, start_date, student_id) 
                VALUES 
                (:level, :univeristy_name, :country, :finish_date, :start_date, :student_id)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'level'           => $data['level'],
            'univeristy_name' => $data['univeristyName'],
            'country'         => $data['country'] ?? null,
            'finish_date'     => $data['finishDate'] ?? null,
            'start_date'      => $data['startDate'] ?? null,
            'student_id'      => $data['studentId']
        ]);
    }

    public function update($id, $data)
    {
        if (empty($id)) {
            throw new \Exception('Study ID is required');
        }

        $sql = "UPDATE {$this->table} SET 
                level = :level, 
                univeristy_name = :univeristy_name, 
                country = :country, 
                finish_date = :finish_date, 
                start_date = :start_date, 
                student_id = :student_id 
                WHERE {$this->primaryKey} = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'level'           => $data['level'] ?? null,
            'univeristy_name' => $data['univeristyName'] ?? null,
            'country'         => $data['country'] ?? null,
            'finish_date'     => $data['finishDate'] ?? null,
            'start_date'      => $data['startDate'] ?? null,
            'student_id'      => $data['studentId'] ?? null,
            'id'              => $id
        ]);
    }

    public function delete($id)
    {
        if (empty($id)) {
            throw new \Exception('Study ID is required');
        }

        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    public function countAll()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table}");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? (int)$result['total'] : 0;
    }

    public function countByStudentId($studentId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE student_id = ?");
        $stmt->execute([$studentId]);
        $result = $stmt->fetch();
        return $result ? (int)$result['total'] : 0;
    }
}
