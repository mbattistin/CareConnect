<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="index.php">Home</a>
      </li>
      <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'user' || $_SESSION['role'] === 'doctor')) : ?>
        <li class="nav-item">
          <a class="nav-link" href="appointments.php">Appointments</a>
        </li>
      <?php endif; ?>
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Management
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="#">Appointments</a>
            <a class="dropdown-item" href="#">Doctors</a>
            <a class="dropdown-item" href="usersManagement.php">Users</a>
          </div>
        </li>
      <?php endif; ?>
    </ul>
    <ul class="navbar-nav ml-auto">
      <?php if (isset($_SESSION['username'])) : ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
            <?php echo htmlspecialchars($_SESSION['username']); ?>
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
            <a class="dropdown-item" href="databaseConnection.php?action=userLogOut">Sign out</a>
          </div>
        </li>
      <?php else : ?>
        <li class="nav-item">
          <a class="nav-link" href="signIn.php">Sign in</a>
        </li>
      <?php endif; ?>
    </ul>
  </div>
</nav>