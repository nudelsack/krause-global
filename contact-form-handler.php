<?php
// Simple PHP mail handler for contact form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $company = htmlspecialchars(trim($_POST['company'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $interest = htmlspecialchars(trim($_POST['interest'] ?? ''));
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));

    $to = 'info@krause-global.com';
    $subject = 'Neue Kontaktanfrage von der Website';
    $body = "Name: $name\nFirma: $company\nE-Mail: $email\nTelefon: $phone\nInteresse: $interest\nNachricht:\n$message";
    $headers = "From: noreply@krause-global.com\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8";

    if (mail($to, $subject, $body, $headers)) {
        http_response_code(200);
        echo 'success';
    } else {
        http_response_code(500);
        echo 'error';
    }
    exit;
}
http_response_code(405);
echo 'Method Not Allowed';
