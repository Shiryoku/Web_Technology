<?php
/**
 * Mock mail helper to simulate sending emails by logging them to a local file.
 */

function sendResetEmail($to, $link)
{
    $log_file = dirname(__DIR__) . '/logs/mail_log.txt';
    $log_dir = dirname($log_file);

    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0777, true);
    }

    $message = "[" . date('Y-m-d H:i:s') . "] Password reset link for $to: $link\n";
    file_put_contents($log_file, $message, FILE_APPEND);

    return true;
}
