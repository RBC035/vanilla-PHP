<?php

namespace App\Models;

class Comment extends BaseModel
{
    protected $table = 'comment';
    protected $primaryKey = 'comment_id';

    public function findAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE coordinator_email IS NOT NULL AND commented_date IS NOT NULL ORDER BY commented_date DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByCoordinatorEmail($email)
    {
        if (empty($email)) {
            return [];
        }

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE coordinator_email = ? AND coordinator_email IS NOT NULL ORDER BY commented_date DESC");
        $stmt->execute([$email]);
        return $stmt->fetchAll();
    }

    public function findByProgressId($progressId)
    {
        if (empty($progressId)) {
            return [];
        }

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE progress_id = ? AND coordinator_email IS NOT NULL AND commented_date IS NOT NULL ORDER BY commented_date DESC");
        $stmt->execute([$progressId]);
        return $stmt->fetchAll();
    }

    public function findByDate($date)
    {
        if (empty($date)) {
            return [];
        }

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE DATE(commented_date) = ? AND coordinator_email IS NOT NULL ORDER BY commented_date DESC");
        $stmt->execute([$date]);
        return $stmt->fetchAll();
    }

    public function create($data)
    {
        if (empty($data['comment']) || empty($data['coordinatorEmail']) || empty($data['progressId'])) {
            return false;
        }

        $sql = "INSERT INTO {$this->table} (comment, commented_date, coordinator_email, progress_id) 
                VALUES (:comment, :commentedDate, :coordinatorEmail, :progressId)";

        $stmt = $this->db->prepare($sql);

        $commentedDate = !empty($data['commentedDate']) ? $data['commentedDate'] : date('Y-m-d H:i:s');

        return $stmt->execute([
            'comment'          => trim($data['comment']),
            'commentedDate'    => $commentedDate,
            'coordinatorEmail' => strtolower(trim($data['coordinatorEmail'])),
            'progressId'       => (int)$data['progressId']
        ]);
    } 

    public function update($id, $data)
    {
        if (empty($id)) {
            return false;
        }

        $fields = [];
        $params = ['id' => $id];

        if (!empty($data['comment'])) {
            $fields[] = 'comment = :comment';
            $params['comment'] = trim($data['comment']);
        }

        if (!empty($data['coordinatorEmail'])) {
            $fields[] = 'coordinator_email = :coordinatorEmail';
            $params['coordinatorEmail'] = strtolower(trim($data['coordinatorEmail']));
        }

        if (!empty($data['commentedDate'])) {
            $fields[] = 'commented_date = :commentedDate';
            $params['commentedDate'] = $data['commentedDate'];
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id)
    {
        if (empty($id)) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([(int)$id]);
    }

    public function countAll()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE coordinator_email IS NOT NULL AND commented_date IS NOT NULL");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? (int)$result['total'] : 0;
    }

    public function countByProgressId($progressId)
    {
        if (empty($progressId)) {
            return 0;
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE progress_id = ? AND coordinator_email IS NOT NULL AND commented_date IS NOT NULL");
        $stmt->execute([$progressId]);
        $result = $stmt->fetch();
        return $result ? (int)$result['total'] : 0;
    }
}
