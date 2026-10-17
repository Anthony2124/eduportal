<?php
/*
 * EduPortal settings template. Copy this file to api/config.php and fill it in.
 * api/config.php is not committed to git because it holds passwords.
 *
 * Sign-up and faculty sign-in email a 6-digit code, so fill in the smtp Gmail login below
 * (a Gmail App Password - see the steps above 'smtp'). Without it those steps show an error,
 * unless demo_mode is turned on for testing.
 * Leaving google_client_id empty is fine: users type their Gmail address instead of choosing a Google account.
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

    // true = show sign-in codes on screen instead of emailing them, when no Gmail login is set above.
    // ONLY for testing on your own computer: anyone could then sign up with someone else's Gmail.
    'demo_mode' => false,

    // School term shown in the top bar of every page (set to '' to hide it)
    'term_label' => 'AY 2026-2027 • 1st Sem',

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
