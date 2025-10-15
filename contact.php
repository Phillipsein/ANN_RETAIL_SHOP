<?php
// contact.php — PHPMailer + Titan SMTP (production-ready)
declare(strict_types=1);

// ---- SETTINGS ----
$SITE_NAME  = 'ANN RETAIL SHOP';
$TO_EMAILS  = ['phillipsein6@gmail.com', 'nabukeeraannet2@gmail.com'];
$FROM_EMAIL = 'sales@annretailshop.philltechs.com';  // Titan mailbox to send from
$SMTP_HOST  = 'smtp.titan.email';
$SMTP_USER  = 'sales@annretailshop.philltechs.com';
$SMTP_PASS  = 'lilAnn@78930_salesEmail';             // consider storing outside web root
$SMTP_PORT  = 587;

// ---- BASIC ANTISPAM/RATE LIMIT ----
session_start();
if (!empty($_POST['company'])) {
    http_response_code(400);
    exit('Bad request');
} // honeypot
if (isset($_SESSION['last_submit']) && time() - $_SESSION['last_submit'] < 15) {
    http_response_code(429);
    exit('Too many requests, try again in a moment.');
}
$_SESSION['last_submit'] = time();

// ---- VALIDATE ----
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');
if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
    http_response_code(400);
    exit('Invalid input');
}

// ---- COMPOSE ----
$subject = "New inquiry from $SITE_NAME website";
$body    = "Name: $name\nEmail: $email\nPhone: $phone\n\nMessage:\n$message\n\n--\n$SITE_NAME";

// ---- SEND (PHPMailer over Titan SMTP) ----
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = $SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = $SMTP_USER;
    $mail->Password   = $SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $SMTP_PORT;

    $mail->setFrom($FROM_EMAIL, $SITE_NAME);
    foreach ($TO_EMAILS as $rcpt) {
        $mail->addAddress($rcpt);
    }
    $mail->addReplyTo($email, $name);

    $mail->Subject = $subject;
    $mail->Body    = $body;

    $ok = $mail->send();
} catch (Exception $e) {
    $ok = false;
}

// ---- REDIRECT BACK ----
header('Location: /?' . ($ok ? 'ok=1' : 'ok=0') . '#contact');
exit;
