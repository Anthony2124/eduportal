<?php
/*
 * Sends the OTP email through Gmail SMTP (SSL, port 465) - no extra libraries needed.
 */
declare(strict_types=1);

function smtp_configured(): bool
{
    $smtp = config()['smtp'];
    return $smtp['username'] !== '' && $smtp['password'] !== '';
}

function send_otp_email(string $to, string $name, string $code, int $ttlMinutes): void
{
    $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $subject = "{$code} is your EduPortal verification code";

    $text = "Hi {$name},\r\n\r\n"
        . "Your EduPortal verification code is: {$code}\r\n\r\n"
        . "It expires in {$ttlMinutes} minutes. If you didn't request this, you can ignore this email.\r\n";

    $html = <<<HTML
        <div style="font-family:Arial,Helvetica,sans-serif;max-width:480px;margin:0 auto;padding:24px;color:#1E293B">
            <h2 style="margin:0 0 4px;color:#4F46E5">EduPortal</h2>
            <p style="margin:0 0 24px;color:#64748B">Academic Management System</p>
            <p>Hi {$safeName},</p>
            <p>Use this code to verify your Gmail:</p>
            <p style="font-size:32px;font-weight:bold;letter-spacing:8px;background:#EEF2FF;color:#3730A3;padding:16px;text-align:center;border-radius:12px">{$code}</p>
            <p style="color:#64748B">It expires in {$ttlMinutes} minutes. If you didn't request this, you can ignore this email.</p>
        </div>
        HTML;

    send_mail($to, $name, $subject, $text, $html);
}

function send_mail(string $to, string $toName, string $subject, string $text, string $html): void
{
    $smtp = config()['smtp'];
    $from = $smtp['username'];
    $password = str_replace(' ', '', $smtp['password']); // App Passwords are shown with spaces

    $socket = @stream_socket_client("ssl://{$smtp['host']}:{$smtp['port']}", $errno, $errstr, 15);
    if (!$socket) {
        throw new RuntimeException("Cannot connect to {$smtp['host']}:{$smtp['port']} ({$errstr})");
    }
    stream_set_timeout($socket, 15);

    $expect = function (array $codes) use ($socket): void {
        $response = '';
        while (($line = fgets($socket, 515)) !== false) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break; // last line of a (possibly multi-line) reply
            }
        }
        if (!in_array((int) substr($response, 0, 3), $codes, true)) {
            throw new RuntimeException('SMTP error: ' . trim($response));
        }
    };
    $send = function (string $command, array $codes) use ($socket, $expect): void {
        fwrite($socket, $command . "\r\n");
        $expect($codes);
    };

    $boundary = 'eduportal_' . bin2hex(random_bytes(8));
    $encodeHeader = fn(string $value) => '=?UTF-8?B?' . base64_encode($value) . '?=';
    $message = implode("\r\n", [
        'Date: ' . date(DATE_RFC2822),
        'From: ' . $encodeHeader($smtp['from_name']) . " <{$from}>",
        'To: ' . $encodeHeader($toName) . " <{$to}>",
        'Subject: ' . $encodeHeader($subject),
        'Message-ID: <' . bin2hex(random_bytes(12)) . '@eduportal.local>',
        'MIME-Version: 1.0',
        "Content-Type: multipart/alternative; boundary=\"{$boundary}\"",
        '',
        "--{$boundary}",
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: base64',
        '',
        rtrim(chunk_split(base64_encode($text))),
        "--{$boundary}",
        'Content-Type: text/html; charset=UTF-8',
        'Content-Transfer-Encoding: base64',
        '',
        rtrim(chunk_split(base64_encode($html))),
        "--{$boundary}--",
    ]);

    try {
        $expect([220]);
        $send('EHLO localhost', [250]);
        $send('AUTH LOGIN', [334]);
        $send(base64_encode($from), [334]);
        $send(base64_encode($password), [235]);
        $send("MAIL FROM:<{$from}>", [250]);
        $send("RCPT TO:<{$to}>", [250, 251]);
        $send('DATA', [354]);
        $send($message . "\r\n.", [250]);
        $send('QUIT', [221]);
    } finally {
        fclose($socket);
    }
}
