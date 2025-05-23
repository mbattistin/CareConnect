<?php

session_start();

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Home</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
    integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
    crossorigin="anonymous"></script> <!-- <link rel="stylesheet" href="styles/custom-navbar.css"> -->
</head>

<body>
  <!-- navbar section -->
  <?php include 'navbar.php'; ?>

  <main class="container my-5">
    <!-- Home Section -->
    <section id="home" class="mb-3">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h1 class="display-4">Welcome to CareConnect</h1>
          <p class="lead">
            At CareConnect, our mission is to make quality healthcare accessible for everyone. We believe
            that cost should never be a barrier to getting the support you need. Our social-pricing model
            ensures that all consultations remain affordable, regardless of background.
          </p>
          <p>
            Whether you need a routine check‑up, a specialist referral, or just someone to talk to, our
            network of caring professionals is here to help. CareConnect is by your side.
          </p>
        </div>
        <div class="col-md-6 text-center">
          <img src="images/graphic-of-female-doctor.jpg" class="img-fluid rounded">
          <a href="https://www.vecteezy.com/free-vector/health-care">Health Care Vectors by Vecteezy</a>
        </div>
      </div>
    </section>

    <!-- About Us Section -->
    <section id="about">
      <div class="row">
        <div class="col-12 text-center mb-7">
          <h2>About Us</h2>
        </div>
        <div class="col-md-6">
          <p>
            CareConnect was born out of a simple idea: healthcare is a human right. Frustrated by rising medical
            costs and long wait times, our founders came together to create a platform that cuts through red tape and
            brings care directly to those who need it most.
          </p>
        </div>
        <div class="col-md-6">
          <ul class="list-group">
            <li class="list-group-item">Built on principles of transparency, compassion, and community.</li>
            <li class="list-group-item">Connect partners with clinics who agree to offer their services at a
              sliding‑scale rate.</li>
          </ul>
        </div>
      </div>
    </section>
  </main>

  <!-- footer section -->
  <?php include 'footer.php'; ?>
</body>

</html>