<?php
// session_start();

// Check if the session variables are set
if (!isset($_SESSION['user_id']) || !isset($_SESSION['name']) || !isset($_SESSION['class'])) {
  header("Location: login.php");
  exit();
}

// Store user ID in a variable
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <script src="https://unpkg.com/feather-icons"></script>
  <style>
    body {
      background-image: url('mianbg.svg');
      background-size: cover;
      background-repeat: no-repeat;
      background-attachment: fixed;
      background-position: center;
    }

    .navbar {
      position: fixed;
      top: 1rem;
      left: 1rem;
      background: #fff;
      border-radius: 10px;
      padding: 1rem 0;
      box-shadow: 0 0 40px rgba(10, 10, 10, 0.03);
      height: calc(100vh - 2rem);
      display: flex;
      flex-direction: column;
    }

    .navbar__menu {
      list-style: none;
      padding: 0;
      margin: 0;
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    .navbar__link {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 3.5rem;
      width: 5.5rem;
      color: #6a778e;
      transition: 250ms ease all;
      text-decoration: none;
    }

    .navbar__link span {
      position: absolute;
      left: 100%;
      transform: translate(-3rem);
      margin-left: 1rem;
      opacity: 0;
      pointer-events: none;
      color: #406ff3;
      background: #fff;
      padding: 0.75rem;
      transition: 250ms ease all;
      border-radius: 17.5px;
    }

    .navbar__link:hover {
      color: #000;
    }

    .navbar:not(:hover) .navbar__link:focus,
    .navbar__link:hover span {
      opacity: 1;
      transform: translate(0);
    }

    .main-menu {
      flex-grow: 1;
    }

    .navbar__item.logout {
      margin-top: auto;
      border-top: 1px solid #eaeef6;
      padding-top: 0.5rem;
    }

    .form-container {
      margin-left: 8rem;
      padding: 1rem;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      max-width: 500px;
    }

    .form-container h2 {
      margin-bottom: 1rem;
    }

    .form-container input,
    .form-container textarea {
      width: 100%;
      padding: 0.75rem;
      margin: 0.5rem 0;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .form-container button {
      background: #406ff3;
      color: #fff;
      padding: 0.75rem 1.5rem;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    /* Custom Scrollbar for the entire website */
    ::-webkit-scrollbar {
      width: 8px;
    }

    ::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
      background: #9e80df;
      border-radius: 4px;
      transition: background-color 0.2s ease;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: #7a5ec0;
      /* Darker version of #9e80df */
    }

    /* For Firefox */
    * {
      scrollbar-width: thin;
      scrollbar-color: #9e80df #f1f1f1;
    }

    /* For Edge and other browsers */
    ::-ms-scrollbar {
      width: 8px;
    }

    ::-ms-scrollbar-track {
      background: #f1f1f1;
      border-radius: 4px;
    }

    ::-ms-scrollbar-thumb {
      background: #9e80df;
      border-radius: 4px;
    }

    ::-ms-scrollbar-thumb:hover {
      background: #7a5ec0;
    }
  </style>
</head>

<body>
  <nav class="navbar">
    <ul class="navbar__menu">
      <div class="main-menu">
        <!-- Default Menu Items -->
        <li class="navbar__item">
          <a href="student_dashboard.php" class="navbar__link"><i data-feather="home"></i><span>Home</span></a>
        </li>
        <?php if (
          !in_array($user_id, [
            320,
            390,
            389
          ])
        ): ?>
          <li class="navbar__item">
            <a href="Leave_form.php" class="navbar__link"><i data-feather="message-square"></i><span>Form</span></a>
          </li>
        <?php endif; ?>

        <!-- Conditional Menu Item for Specific Users -->
        <?php if (
          in_array($user_id, [
            320,
            390,
            389
          ])
        ): ?>
          <li class="navbar__item">
            <a href="cc_status.php" class="navbar__link"><i data-feather="alert-circle"></i><span>Status</span></a>
          </li>
        <?php endif; ?>
      </div>
      <!-- Logout Menu Item -->
      <li class="navbar__item logout">
        <a href="Logout.php" class="navbar__link"><i data-feather="log-out"></i><span>Logout</span></a>
      </li>
    </ul>
  </nav>


  </div>

  <script>
    // Replace feather icons
    feather.replace();
  </script>
</body>

</html>