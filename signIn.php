<?php

session_start();

$currentPage = 'Sign In';

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=">
    <title>CareConnect – Sign In</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="scripts/login-form-validation.js" defer></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  </head>
  <body>
    <!-- navbar section -->
    <?php include 'navbar.php'; ?>
    <main class="container my-2">
      <div class="card mx-auto" style="max-width: 500px;" >
        <div class="card-header bg-dark text-white text-center">
          <h5 class="mb-0">Sign In</h5>
        </div>
        <form class="card-body" id="loginForm"  name="loginForm"  method="POST" action="databaseConnection.php?action=userLogin"
          onsubmit="return validateForm(event)" autocomplete="off">
          <?php if (isset($_GET['registration_success_message'])): ?>
            <div class='alert alert-success'>Registration successful. Please log in.</div>
          <?php endif; ?>
          <?php if (isset($_SESSION['login_error_message'])): ?>
            <div class="alert alert-danger">
              <?= $_SESSION['login_error_message']; unset($_SESSION['login_error_message']); ?>
            </div>
          <?php endif; ?>
          <div class="mb-1">
            <label for="email" class="form-label">Email:</label>
            <input type="email" class="form-control" id="email" name="email"  maxlength="100" autocomplete="username">
            <label class="text-danger" id="emailError"></label>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <input type="password" class="form-control" id="password" name="password"  maxlength="15" autocomplete="current-password">
            <label class="text-danger" id="passwordError"></label>
          </div>
          <div class="mb-3 d-flex flex-column align-items-center text-center">
            <!-- I used the captcha from google based on their documentation: https://cloud.google.com/security/products/recaptcha -->
            <div class="g-recaptcha" data-sitekey="6LeNdT4rAAAAAGk9hEHmpny5DeUYFdiiwA2RU_0j"></div>
            <label class="text-danger" id="captchaError"></label> 
          </div>
          <div class="inline-block text-center mb-3">
            Don’t have an account? <a href="register.php">Register here</a>.
          </div>
          <div class="row">
          <div class="col-6">
            <button type="submit" class="btn btn-dark w-100">Sign In</button>
          </div>
          <div class="col-6">
            <a href="index.php" class="btn btn-danger w-100">Cancel</a>
          </div>
      </form>
      </div>

    </main>
    <!-- footer section -->
    <?php include 'footer.php'; ?>
  </body>
</html>
