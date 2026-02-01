<?php
session_start();

// Initialize variables
$category = $name = $email = $phone = $password = '';
$events = [];

// Check if edit mode is active and session exists
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_SESSION['registrations'])) {
    $record = $_SESSION['registrations'];
    // Populate variables from session
    $category = $record['category'];
    $name = $record['name'];
    $email = $record['email'];
    $phone = $record['phone'];
    // Note: We usually don't repopulate passwords for security, but we will leave it empty or fill if needed based on lab req.
    // $password = $record['password']; 
    $events = $record['events'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Campus Event Management System (CEMS)</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<header class="hero">
    <div class="overlay"></div>
    <div class="hero-content">
      <img src="img/logo.jpg" alt="CEMS Logo" class="logo">
      <h1>Campus Event Management System</h1>
      <p>Organize, manage, and participate in campus events seamlessly</p>
    </div>
  </header>

<?php include 'include/topNav.php'; ?>

    <main> 
    <section> 
      <h3>Register</h3>
      
      <p id="output" style="color:green;">
        <?php
        if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_SESSION['registrations'])) {
            echo "Editing: " . htmlspecialchars($_SESSION['registrations']['name']);
        }
        ?>
      </p>

      <form action="register_action.php" method="post" name="registerForm"> 
        <fieldset> 
          <legend>Category</legend> 
          <label> 
            <input type="radio" name="category" value="staff" <?= ($category === 'staff') ? 'checked' : '' ?> required>Staff
          </label> 
          <label> 
            <input type="radio" name="category" value="student" <?= ($category === 'student') ? 'checked' : '' ?>> Student 
          </label> 
          <label> 
            <input type="radio" name="category" value="public" <?= ($category === 'public') ? 'checked' : '' ?>>Public
          </label> 
        </fieldset> 
 
        <div> 
          <label for="name">Full Name</label><br> 
          <input type="text" id="name" name="name" required autocomplete="name" placeholder="Jane Doe" value="<?= htmlspecialchars($name) ?>"> 
        </div> 
 
        <div> 
          <label for="email">Email</label><br> 
          <input type="email" id="email" name="email" required autocomplete="email" placeholder="you@example.edu" value="<?= htmlspecialchars($email) ?>"> 
        </div> 
 
        <div> 
          <label for="phone">Phone</label><br> 
          <input type="tel" id="phone" name="phone" autocomplete="tel" placeholder="+60 12345 6789" value="<?= htmlspecialchars($phone) ?>"> 
        </div>         
 
        <div> 
          <label for="password">Password</label><br> 
          <input type="password" id="password" name="password" required minlength="6" autocomplete="new-password" placeholder="Choose a password"> 
        </div> 
 
        <div> 
            <label>Recommend event about:</label><br> 
            <input type="checkbox" name="event[]" value="workshop" <?= in_array('workshop', $events) ? 'checked' : '' ?>> Workshop<br> 
            <input type="checkbox" name="event[]" value="seminar" <?= in_array('seminar', $events) ? 'checked' : '' ?>> Seminar<br> 
            <input type="checkbox" name="event[]" value="competition" <?= in_array('competition', $events) ? 'checked' : '' ?>> Competition<br> 
            <input type="checkbox" name="event[]" value="festival" <?= in_array('festival', $events) ? 'checked' : '' ?>> Festival<br> 
            <input type="checkbox" name="event[]" value="sport" <?= in_array('sport', $events) ? 'checked' : '' ?>> Sport<br> 
            <input type="checkbox" name="event[]" value="course" <?= in_array('course', $events) ? 'checked' : '' ?>> Course<br> 
        </div> 
 
        <div> 
          <button type="submit">Register</button> 
          <button type="reset">Reset</button> 
        </div> 
      </form> 
      <p id="output"></p>
    </section> 
  </main> 

<footer id="myfooter">
    <hr>
    <p>&copy;  <span id="y"></span>( WAN AHMAD NURULLAH | BI23110062 ) 2025 CEMS. All rights reserved.</p>
</footer>

<script>
    document.getElementById('y').textContent = new Date().getFullYear();
    // Toggle mobile menu
    const menuIcon = document.getElementById('menu-icon');
    const navLinks = document.getElementById('nav-links');
    menuIcon.onclick = () => navLinks.classList.toggle('active');

    document.addEventListener("DOMContentLoaded", () => { 
    const form = document.querySelector("form");     
 
      form.addEventListener("submit", function (e) { 
        const checkboxes = document.querySelectorAll('input[name="event[]"]'); 
        let checked = false; 
 
        // Check if at least one checkbox is selected 
        for (const box of checkboxes) { 
          if (box.checked) { 
            checked = true; 
            break; 
          } 
        } 
 
        if (!checked) { 
          e.preventDefault(); // Stop form submission 
          alert("Please select at least one recommended event."); 
          const output = document.getElementById("output"); 
          output.style.color = "red"; 
          output.textContent = `Please select at least one recommended event.`; 
          return; 
        }  
      }); 
    });
</script>
</body>
</html>