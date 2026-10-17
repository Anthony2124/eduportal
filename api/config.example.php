<?php
/*
 * EduPortal settings template. Copy this file to api/config.php and fill it in.
 * api/config.php is not committed to git because it holds passwords.
 *
 * Leaving google_client_id or the smtp username/password empty runs that part in demo mode:
 *   - no Google Client ID -> the user types their Gmail address instead of choosing a Google account
 *   - no SMTP login       -> the OTP is shown on screen instead of being emailed
 */
return [
    // XAMPP MySQL (MariaDB). XAMPP's default root password is empty.
    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'eduportal',
        'user' => 'root',
        'pass' => '',
    ],

    // OAuth "Web application" Client ID from Google Cloud Console
    // (looks like 1234567890-xxxxxxxx.apps.googleusercontent.com).
    // Its "Authorized JavaScript origins" must include http://localhost
    'google_client_id' => '',

    // Gmail account that sends the OTP emails, through Gmail SMTP.
    // 'password' is a Gmail App Password, NOT the normal Gmail password:
    //   Google Account > Security > 2-Step Verification (turn on) > App passwords > create one
    'smtp' => [
        'host' => 'smtp.gmail.com',
        'port' => 465,
        'username' => '',          // e.g. eduportal.mailer@gmail.com
        'password' => '',          // 16-character App Password, e.g. 'abcd efgh ijkl mnop'
        'from_name' => 'EduPortal',
    ],

    'otp' => [
        'ttl_minutes' => 5,        // how long a code stays valid
        'max_attempts' => 5,       // wrong tries before the code is cancelled
        'resend_seconds' => 45,    // wait time before "Resend code" works
    ],

    // Time zone for greetings and dates shown on the dashboards
    'timezone' => 'Asia/Manila',

    // "Remember me" keeps the user signed in for this many days
    'remember_days' => 30,
];
