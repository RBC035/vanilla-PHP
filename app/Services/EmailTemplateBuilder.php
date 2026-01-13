<?php

namespace App\Services;

class EmailTemplateBuilder
{
    private function getCommonStyles()
    {
        return "
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f5f5f5; margin: 0; padding: 0; }
            .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden; }
            .header { background: linear-gradient(135deg, #19b4de 0%, #d3c20d 100%); color: #ffffff; padding: 30px 20px; text-align: center; }
            .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
            .content { padding: 30px 20px; }
            .greeting { font-size: 16px; margin-bottom: 20px; color: #555; }
            .otp-section { background-color: #f9f9f9; border-left: 4px solid #19b4de; padding: 20px; margin: 25px 0; border-radius: 4px; }
            .otp-label { font-size: 12px; color: #999; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
            .otp-code { font-size: 32px; font-weight: bold; color: #19b4de; letter-spacing: 4px; text-align: center; font-family: 'Courier New', monospace; }
            .security-notice { background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; padding: 15px; margin: 20px 0; font-size: 14px; color: #856404; }
            .info-text { font-size: 14px; color: #666; margin: 15px 0; }
            .divider { border-top: 1px solid #e0e0e0; margin: 20px 0; }
            .rejection-badge { background-color: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 20px 0; border-radius: 4px; color: #721c24; }
            .action-box { background-color: #e8f4f8; border-left: 4px solid #19b4de; padding: 15px; margin: 15px 0; border-radius: 4px; }
            .reminder-badge { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px; color: #856404; }
            .action-button { display: inline-block; background-color: #19b4de; color: white !important; padding: 12px 30px; border-radius: 4px; text-decoration: none; font-weight: bold; margin: 15px 0; }
            .footer { padding: 20px; text-align: center; font-size: 12px; color: #999; background: #f9f9f9; }";
    }

    private function getFooter()
    {
        $year = date("Y");
        return "
            <div class='footer'>
                <p>This is an automated message. Please do not reply to this email.</p>
                <p>&copy; $year The State University of Zanzibar (SUZA). All rights reserved.</p>
            </div>";
    }

    public function buildOtpEmailTemplate($otp)
    {
        $styles = $this->getCommonStyles();
        $footer = $this->getFooter();
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head><style>$styles</style></head>
        <body>
            <div class="container">
                <div class="header"><h1>🔒 Secure Verification</h1></div>
                <div class="content">
                    <p class="greeting">Hello,</p>
                    <p class="info-text">You have requested to verify your identity. Please use the One-Time Password (OTP) below:</p>
                    <div class="otp-section">
                        <div class="otp-label">Your One-Time Password:</div>
                        <div class="otp-code">$otp</div>
                    </div>
                    <div class="security-notice">
                        <strong>⏳ Important Security Notice:</strong>
                        <div>• This code will expire in 10 minutes</div>
                        <div>• Never share this code with anyone</div>
                    </div>
                </div>
                $footer
            </div>
        </body>
        </html>
HTML;
    }

    public function buildWelcomeEmailTemplate($fullName, $username, $password)
    {
        $styles = $this->getCommonStyles();
        $footer = $this->getFooter();
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head><style>$styles</style></head>
        <body>
            <div class="container">
                <div class="header"><h1>🎉 Welcome to HETRAS</h1></div>
                <div class="content">
                    <p class="greeting">Hello $fullName,</p>
                    <p class="info-text">Your account has been created successfully. Use the credentials below to log in:</p>
                    <div class="otp-section">
                        <div class="otp-label">Login Credentials:</div>
                        <p class="info-text"><strong>Username:</strong> $username</p>
                        <p class="info-text"><strong>Password:</strong> $password</p>
                    </div>
                    <div class="security-notice">
                        <strong>⚠️ Important:</strong>
                        <div>• Please change your password after your first login</div>
                    </div>
                </div>
                $footer
            </div>
        </body>
        </html>
HTML;
    }

    public function buildRejectedProgressEmailTemplate($studentName, $progressMonth, $status, $comments)
    {
        $styles = $this->getCommonStyles();
        $footer = $this->getFooter();
        $feedback = !empty($comments) ? $comments : "No additional comments provided.";
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head><style>$styles</style></head>
        <body>
            <div class="container">
                <div class="header"><h1>Progress Report Update</h1></div>
                <div class="content">
                    <p class="greeting">Hello $studentName,</p>
                    <p class="info-text">Your progress report has been reviewed.</p>
                    <div class="rejection-badge">
                        <p><strong>Status:</strong> $status</p>
                        <p><strong>Period:</strong> $progressMonth</p>
                    </div>
                    <div class="action-box">
                        <p><strong>Supervisor Feedback:</strong></p>
                        <p style="white-space: pre-wrap;">$feedback</p>
                    </div>
                    <p class="info-text">Please address the feedback and resubmit your report as soon as possible.</p>
                </div>
                $footer
            </div>
        </body>
        </html>
HTML;
    }

    public function buildProgressReminderEmailTemplate($studentName, $progressMonth, $studyLevel)
    {
        $styles = $this->getCommonStyles();
        $footer = $this->getFooter();
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head><style>$styles</style></head>
        <body>
            <div class="container">
                <div class="header"><h1>📅 Progress Report Reminder</h1></div>
                <div class="content">
                    <p class="greeting">Hello $studentName,</p>
                    <p class="info-text">This is a reminder that your progress report is due.</p>
                    <div class="reminder-badge">
                        <p><strong>📅 Period:</strong> $progressMonth</p>
                        <p><strong>📚 Level:</strong> $studyLevel</p>
                    </div>
                    <div style="text-align: center;">
                        <a href="#" class="action-button">Submit Progress Report</a>
                    </div>
                </div>
                $footer
            </div>
        </body>
        </html>
HTML;
    }
}
