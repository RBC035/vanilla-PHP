<?php

namespace App\Models;

class PasswordResetOTP extends BaseModel
{
    protected $table = 'resetPassword';
    protected $primaryKey = 'otp_id';

    public function createOTP($email, $otp, $minutes = 10)
    {
        $expiry = date('Y-m-d H:i:s', strtotime("+$minutes minutes"));
        $sql = "INSERT INTO {$this->table} (email, otp, expires_at) VALUES (?, ?, ?)";
        return $this->db->prepare($sql)->execute([$email, $otp, $expiry]);
    }

    public function verifyOTP($email, $otp)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE email = ? AND otp = ? AND expires_at > NOW() 
                ORDER BY otp_id DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email, $otp]);
        return $stmt->fetch();
    }
}
