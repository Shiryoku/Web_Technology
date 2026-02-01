<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Campus Event Management System (CEMS)</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <!-- ======= Hero Section ======= -->
  <header class="hero">
    <div class="overlay"></div>
    <div class="hero-content">
      <img src="assets/img/logo.jpg" alt="CEMS Logo" class="logo">
      <h1>Campus Event Management System</h1>
      <p>Organize, manage, and participate in campus events seamlessly</p>
    </div>
  </header>

  <!-- ======= Top Navigation ======= -->
  <?php
     include("include/topNav.php");
  ?>  

  <main>
    <section class="intro">
      <h2>Welcome to CEMS</h2>
      <p>
        This system helps you manage and explore upcoming campus events efficiently.
        Register or log in to get started.
      </p>
    </section>
    <section class="listing">
      <h2>Event Listing</h2>
        <div>
		      <table border="1" align="center">
		        <tr>
		          <td>All</td>
		          <td>Filter 1</td>
		          <td>Filter 2</td>
		          <td>Filter 3</td>
		        </tr>
		      </table>
		    </div>
		    <div>
		      <table width="100%" border="1" id="event_table">
		        <tr>
		          <td>Event 1<img src="assets/img/event1.jpg" style="width:100%;"></td>
		          <td>Event 2<img src="assets/img/event2.jpg" style="width:100%;"></td>
		          <td>Event 3<img src="assets/img/event3.jpg" style="width:100%;"></td>
		        </tr>
		        <tr>
		          <td>img</td>
		          <td>img</td>
		          <td>img</td>
		        </tr>
		      </table>
		    </div>
        </section>
  </main>

  <footer>
        <hr>
        <p>&copy; Sam | BI12345678</p>
    </footer>

  <script>
    // Toggle mobile menu
    const menuIcon = document.getElementById('menu-icon');
    const navLinks = document.getElementById('nav-links');
    menuIcon.onclick = () => navLinks.classList.toggle('active');
  </script>
</body>
</html>
