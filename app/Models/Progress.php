<?php

namespace App\Models;

class Progress extends BaseModel
{
    protected $table = 'study_progress';
    protected $primaryKey = 'progress_id';

    public function findAll()
    {
        $sql = "SELECT p.*, 
                s.study_id, s.finish_date, s.student_id, s.level, s.univeristy_name
                FROM {$this->table} p
                LEFT JOIN study s ON p.study_id = s.study_id
                ORDER BY p.progress_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        return array_map(function ($row) {
            return $this->formatProgressWithStudy($row);
        }, $results);
    }

    public function findById($id)
    {
        $sql = "SELECT p.*, 
                s.study_id, s.finish_date, s.student_id, s.level, s.univeristy_name
                FROM {$this->table} p
                LEFT JOIN study s ON p.study_id = s.study_id
                WHERE p.progress_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) return null;

        return $this->formatProgressWithStudy($row);
    }

    public function findByStudyId($studyId)
    {
        $sql = "SELECT p.*, 
                s.study_id, s.finish_date, s.student_id, s.level, s.univeristy_name
                FROM {$this->table} p
                LEFT JOIN study s ON p.study_id = s.study_id
                WHERE p.study_id = ?
                ORDER BY p.progress_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studyId]);
        $results = $stmt->fetchAll();

        return array_map(function ($row) {
            return $this->formatProgressWithStudy($row);
        }, $results);
    }

    public function findByStudentId($studentId)
    {
        $sql = "SELECT p.*, 
                s.study_id, s.finish_date, s.student_id, s.level, s.univeristy_name
                FROM {$this->table} p 
                LEFT JOIN study s ON p.study_id = s.study_id 
                WHERE s.student_id = ?
                ORDER BY p.progress_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId]);
        $results = $stmt->fetchAll();

        return array_map(function ($row) {
            return $this->formatProgressWithStudy($row);
        }, $results);
    }

    public function findLatestByStudent($studentId, $limit = 5)
    {
        $sql = "SELECT p.*, 
                s.study_id, s.finish_date, s.student_id, s.level, s.univeristy_name
                FROM {$this->table} p 
                LEFT JOIN study s ON p.study_id = s.study_id 
                WHERE s.student_id = ? 
                ORDER BY p.progress_date DESC LIMIT $limit";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId]);
        $results = $stmt->fetchAll();

        return array_map(function ($row) {
            return $this->formatProgressWithStudy($row);
        }, $results);
    }

    private function formatProgressWithStudy($row)
    {
        return [
            'progress_id' => $row['progress_id'],
            'previous' => $row['previous'],
            'currently' => $row['currently'],
            'next' => $row['next'],
            'document_path' => $row['document_path'],
            'meet_supervisor' => $row['meet_supervisor'],
            'supervisor_response' => $row['supervisor_response'],
            'meet_progress_plan' => $row['meet_progress_plan'],
            'submit_thesis' => $row['submit_thesis'],
            'study_complition' => $row['study_complition'],
            'challenges' => $row['challenges'],
            'suggestion' => $row['suggestion'],
            'progress_date' => $row['progress_date'],
            'progress_status' => $row['progress_status'],
            'study_id' => $row['study_id'],
            'studyId' => [
                'studyId' => $row['study_id'],
                'finish_date' => $row['finish_date'],
                'student_id' => $row['student_id'],
                'level' => $row['level'],
                'univeristy_name' => $row['univeristy_name']
            ]
        ];
    }

    public function countAll()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table}");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? (int)$result['total'] : 0;
    }

    public function countByStudent($studentId)
    {
        $sql = "SELECT COUNT(p.progress_id) as total 
            FROM {$this->table} p
            LEFT JOIN study s ON p.study_id = s.study_id
            WHERE s.student_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId]);
        $result = $stmt->fetch();
        return $result ? (int)$result['total'] : 0;
    }

    public function getStudentDetailsByProgressId($progressId)
    {
        $sql = "SELECT s.email_address, s.full_name 
                FROM study_progress p
                JOIN study st ON p.study_id = st.study_id
                JOIN student s ON st.student_id = s.student_id
                WHERE p.progress_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$progressId]);
        return $stmt->fetch();
    }

    public function findByProgressDate($date)
    {
        $sql = "SELECT p.*, 
                s.study_id, s.finish_date, s.student_id, s.level, s.univeristy_name
                FROM {$this->table} p
                LEFT JOIN study s ON p.study_id = s.study_id
                WHERE p.progress_date = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$date]);
        $results = $stmt->fetchAll();

        return array_map(function ($row) {
            return $this->formatProgressWithStudy($row);
        }, $results);
    }

    public function findByStudyCompletion($completion)
    {
        $sql = "SELECT p.*, 
                s.study_id, s.finish_date, s.student_id, s.level, s.univeristy_name
                FROM {$this->table} p
                LEFT JOIN study s ON p.study_id = s.study_id
                WHERE p.study_complition = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$completion]);
        $results = $stmt->fetchAll();

        return array_map(function ($row) {
            return $this->formatProgressWithStudy($row);
        }, $results);
    }

    public function findBySubmitThesis($submitThesis)
    {
        $sql = "SELECT p.*, 
                s.study_id, s.finish_date, s.student_id, s.level, s.univeristy_name
                FROM {$this->table} p
                LEFT JOIN study s ON p.study_id = s.study_id
                WHERE p.submit_thesis = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$submitThesis]);
        $results = $stmt->fetchAll();

        return array_map(function ($row) {
            return $this->formatProgressWithStudy($row);
        }, $results);
    }

    public function findByMeetSupervisor($meetSupervisor)
    {
        $sql = "SELECT p.*, 
                s.study_id, s.finish_date, s.student_id, s.level, s.univeristy_name
                FROM {$this->table} p
                LEFT JOIN study s ON p.study_id = s.study_id
                WHERE p.meet_supervisor = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$meetSupervisor]);
        $results = $stmt->fetchAll();

        return array_map(function ($row) {
            return $this->formatProgressWithStudy($row);
        }, $results);
    }

    public function findBySupervisorResponse($response)
    {
        $sql = "SELECT p.*, 
                s.study_id, s.finish_date, s.student_id, s.level, s.univeristy_name
                FROM {$this->table} p
                LEFT JOIN study s ON p.study_id = s.study_id
                WHERE p.supervisor_response = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$response]);
        $results = $stmt->fetchAll();

        return array_map(function ($row) {
            return $this->formatProgressWithStudy($row);
        }, $results);
    }

    public function create($data)
    {
        $studyId = is_array($data['studyId']) ? $data['studyId']['studyId'] : $data['studyId'];

        $sql = "INSERT INTO {$this->table} (
                previous, currently, next, document_path, meet_supervisor, 
                supervisor_response, meet_progress_plan, submit_thesis, 
                study_complition, challenges, suggestion, progress_date, 
                progress_status, study_id
            ) VALUES (
                :previous, :currently, :next, :documentPath, :meetSupervisor, 
                :supervisorResponse, :meetProgressPlan, :submitThesis, 
                :studyComplition, :challenges, :suggestion, :progressDate, 
                :progressStatus, :studyId
            )";

        $stmt = $this->db->prepare($sql);

        $success = $stmt->execute([
            'previous'           => $data['previous'],
            'currently'          => $data['currently'],
            'next'               => $data['next'],
            'documentPath'       => null,
            'meetSupervisor'     => $data['meetSupervisor'],
            'supervisorResponse' => $data['supervisorResponse'],
            'meetProgressPlan'   => $data['meetProgressPlan'],
            'submitThesis'       => $data['submitThesis'],
            'studyComplition'    => $data['studyComplition'],
            'challenges'         => $data['challenges'],
            'suggestion'         => $data['suggestion'],
            'progressDate'       => $data['progressDate'],
            'progressStatus'     => $data['progressStatus'],
            'studyId'            => $studyId
        ]);

        return $success ? $this->db->lastInsertId() : false;
    }

    public function update($id, $data)
    {
        $fields = "";
        foreach ($data as $key => $value) {
            $fields .= "$key = :$key, ";
        }
        $fields = rtrim($fields, ", ");

        $sql = "UPDATE {$this->table} SET $fields WHERE {$this->primaryKey} = :id";
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }
}
