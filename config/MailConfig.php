<?php

namespace Config;

class MailConfig
{
    public static function getHost()
    {
        return getenv('MAIL_HOST');
    }

    public static function getPort()
    {
        return (int)getenv('MAIL_PORT');
    }

    public static function getUser()
    {
        return getenv('MAIL_USER');
    }

    public static function getPass()
    {
        return getenv('MAIL_PASSWORD');
    }

    public static function getFromName()
    {
        return getenv('MAIL_FROM_NAME');
    }

    public static function getEncryption()
    {
        // Returns 'tls' or 'ssl' (PHPMailer uses these for SMTPSecure)
        return getenv('MAIL_ENCRYPTION') ?: 'tls';
    }
}
