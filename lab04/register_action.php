<?php 
session_start(); // Start the session first

// Get the data safely 
$category = $_POST['category'] ?? 'N/A'; 
$name = htmlspecialchars($_POST['name'] ?? 'N/A'); 
$email = htmlspecialchars($_POST['email'] ?? 'N/A'); 
$phone = htmlspecialchars($_POST['phone'] ?? 'N/A'); 
$password = htmlspecialchars($_POST['password'] ?? ''); 
$events = $_POST['event'] ?? []; // array of selected checkboxes 

// Store registration information to session (Task 3)
if (!isset($_SESSION['registrations'])) {
    $_SESSION['registrations'] = [];
}

// Save current data to session
$_SESSION['registrations'] = [
    'category' => $category,
    'name'     => $name,
    'email'    => $email,
    'phone'    => $phone,
    'password' => $password,
    'events'   => $events
];
?> 
<!DOCTYPE html> 
<html lang="en"> 
<head> 
  <meta charset="UTF-8"> 
  <title>Registration Confirmation</title>   
</head> 
<body> 
  <h2>Thank you for registering!</h2> 
  <p><strong>Name:</strong> <?= $name ?></p> 
  <p><strong>Email:</strong> <?= $email ?></p> 
  <p><strong>Phone:</strong> <?= $phone ?></p> 
  <p><strong>Category:</strong> <?= ucfirst($category) ?></p> 
 
  <?php if (!empty($events)): ?> 
    <p><strong>Interested Events:</strong></p> 
    <ul> 
      <?php foreach ($events as $event): ?> 
        <li><?= htmlspecialchars($event) ?></li> 
      <?php endforeach; ?> 
    </ul> 
  <?php else: ?> 
    <p><em>No event selected.</em></p> 
  <?php endif; ?> 
 
  <a href="index.php">Back to Home</a> | <a href="register.php?action=edit">Edit?</a>
</body> 
</html>