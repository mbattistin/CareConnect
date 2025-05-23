<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=">
  <title>Register</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="scripts/register-form-validation.js" defer></script>
</head>

<body>
  <!-- navbar section -->
  <?php include 'navbar.php'; ?>
  <!-- Register section -->
  <main class="container my-2">
    <div class="card mx-auto" style="max-width: 500px;">
      <div class="card-header bg-dark text-white text-center">
        <h5 class="mb-0">Register</h5>
      </div>
      <form class="card-body" id="registerForm" name="registerForm" method="POST"
        action="databaseConnection.php?action=insertUserFromRegisterForm" onsubmit="return validateForm(event)">
        <div class="mb-1">
          <?php if (isset($_SESSION['registration_error_message'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['registration_error_message'];
            unset($_SESSION['registration_error_message']); ?>
            </div>
          <?php endif; ?>
          <label for="name" class="form-label">Full Name:</label>
          <input type="text" class="form-control" id="name" name="name" maxlength="200">
          <label class="text-danger" id="nameError"></label>
        </div>
        <div class="mb-1">
          <label for="email" class="form-label">Email:</label>
          <input type="email" class="form-control" id="email" name="email" maxlength="100">
          <label class="text-danger" id="emailError"></label>
        </div>
        <div class="mb-1">
          <label for="phone" class="form-label">Phone Number:</label>
          <input type="tel" class="form-control" id="phone" name="phone" maxlength="10">
          <label class="text-danger" id="phoneError"></label>
        </div>
        <div class="mb-1">
          <label for="password" class="form-label">Password:</label>
          <input type="password" class="form-control" id="password" name="password" maxlength="15">
          <label class="text-danger" id="passwordError"></label>
        </div>
        <div class="mb-1">
          <label for="confirmPassword" class="form-label">Confirm Password:</label>
          <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" maxlength="15">
          <label class="text-danger" id="confirmPasswordError"></label>
        </div>
        <div class="row">
          <div class="col-6">
            <button type="submit" class="btn btn-dark w-100"><i class="fa fa-sign-in mr-2"
                aria-hidden="true"></i>Register</button>
          </div>
          <div class="col-6">
            <a href="index.php" class="btn btn-danger w-100"><i class="fa fa-times mr-2"
                aria-hidden="true"></i>Cancel</a>
          </div>
      </form>
    </div>

  </main>
  <!-- footer section -->
  <?php include 'footer.php'; ?>
</body>

</html>