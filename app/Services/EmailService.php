<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Config\MailConfig;

class EmailService
{
    private $templateBuilder;

    public function __construct()
    {
        $this->templateBuilder = new EmailTemplateBuilder();
    }

    private function sendEmail($to, $subject, $body)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = MailConfig::getHost();
            $mail->SMTPAuth   = true;
            $mail->Username   = MailConfig::getUser();
            $mail->Password   = MailConfig::getPass();

            $fromEmail = MailConfig::getUser();
            $fromName = MailConfig::getFromName();

            // Map 'tls' or 'ssl' to PHPMailer constants
            $encryption = strtolower(MailConfig::getEncryption());
            $mail->SMTPSecure = ($encryption === 'ssl') ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;

            $mail->Port       = MailConfig::getPort();

            error_log("DEBUG: From Email = '$fromEmail', From Name = '$fromName'");

            $mail->setFrom(MailConfig::getUser(), MailConfig::getFromName());
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            return $mail->send();
        } catch (Exception $e) {
            error_log("HETRAS Email Error: " . $mail->ErrorInfo);
            return false;
        }
    }

    public function sendOtpEmail($to, $otp)
    {
        $html = $this->templateBuilder->buildOtpEmailTemplate($otp);
        return $this->sendEmail($to, "Your One-Time Password (OTP) - Action Required", $html);
    }

    public function sendWelcomeEmail($to, $fullName, $username, $password)
    {
        $html = $this->templateBuilder->buildWelcomeEmailTemplate($fullName, $username, $password);
        return $this->sendEmail($to, "Welcome to HETRAS System - Your Login Credentials", $html);
    }

    public function sendRejectedProgressEmail($to, $studentName, $progressMonth, $status, $comments)
    {
        $html = $this->templateBuilder->buildRejectedProgressEmailTemplate($studentName, $progressMonth, $status, $comments);
        return $this->sendEmail($to, "HETRAS | Your Progress Report - $status", $html);
    }

    public function sendProgressReminderEmail($to, $studentName, $progressMonth, $studyLevel)
    {
        $html = $this->templateBuilder->buildProgressReminderEmailTemplate($studentName, $progressMonth, $studyLevel);
        return $this->sendEmail($to, "HETRAS | Monthly Progress Report Reminder", $html);
    }
}
