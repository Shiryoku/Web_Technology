<?php
header('Content-Type: application/json');


$data = json_decode(file_get_contents('php://input'), true);
$message = isset($data['message']) ? strtolower(trim($data['message'])) : '';

if (empty($message)) {
    echo json_encode(['response' => 'Please ask a question.']);
    exit;
}


$response = "I'm not sure about that. Could you please clarify?";

// Keywords matching
if (strpos($message, 'hello') !== false || strpos($message, 'hi') !== false) {
    $response = "Hello! I'm your SkillShare assistant. How can I help you today?";
} elseif (strpos($message, 'register') !== false || strpos($message, 'sign up') !== false || strpos($message, 'create account') !== false) {
    $response = "To register, click the 'Register' button in the top right corner. You'll need to provide your name, email, and password. You can choose to register as a 'User' (to join events) or an 'Organizer' (to create events).";
} elseif (strpos($message, 'login') !== false || strpos($message, 'sign in') !== false) {
    $response = "You can log in by clicking the 'Login' button in the navigation bar. Use your registered email and password.";
} elseif (strpos($message, 'create event') !== false || strpos($message, 'host event') !== false) {
    $response = "To create an event, you must be logged in as an 'Organizer'. Once logged in, go to your dashboard and click 'Create New Event'. Fill in the details like title, date, location, and price.";
} elseif (strpos($message, 'join') !== false || strpos($message, 'book') !== false || strpos($message, 'attend') !== false) {
    $response = "To join an event, browse the events list and click on an event you're interested in. On the event details page, click 'Register Now'. If it's a paid event, you may need to complete payment.";
} elseif (strpos($message, 'payment') !== false || strpos($message, 'pay') !== false) {
    $response = "We support secure payments for paid events. After registering, you'll be guided to the payment process if applicable.";
} elseif (strpos($message, 'cancel') !== false || strpos($message, 'refund') !== false) {
    $response = "To cancel a registration, please go to your dashboard and view your registered events. Cancellation policies depend on the specific event organizer.";
} elseif (strpos($message, 'contact') !== false || strpos($message, 'support') !== false) {
    $response = "You can contact our support team at support@skillshare.com for further assistance.";
}


usleep(500000); // 0.5 seconds

echo json_encode(['response' => $response]);
