<?php
// VetCare Pro - Veterinary Practice Management System

// Redirect to login page// VetCare Pro - Veterinary Practice Management System// VetCare Pro - Veterinary Practice Management System

header("Location: login/index.php");

exit();// Redirect to login page// Redirect to login page

?>
header("Location: login/index.php");header("Location: login/login.php");

exit();exit();

?>?>
        </div>
      </div>

      <nav id="navbar" class="nav-menu navbar">
        <ul>
          <li><a href="#hero" class="nav-link scrollto active"><i class="bx bx-home"></i> <span>Home</span></a></li>
          <li><a href="#about" class="nav-link scrollto"><i class="bx bx-user"></i> <span>About</span></a></li>
          <li><a href="#contact" class="nav-link scrollto"><i class="bx bx-envelope"></i> <span>Contact</span></a></li>
          <li><a href="./login/index.php"><i class="bx bx-right-arrow"></i> <span>Admin Login</span></a></li>
        </ul>
      </nav><!-- .nav-menu -->
    </div>
  </header><!-- End Header -->

  <!-- ======= Hero Section ======= -->
  <section id="hero" class="d-flex flex-column justify-content-center align-items-center">
    <div class="hero-container" data-aos="fade-in">
      <h1>Animaux <Br></h1>
      <p>It offers <span class="typed" data-typed-items="patient management, basic accounting, back office administration"></span></p>
    </div>
  </section><!-- End Hero -->

  <main id="main">

    <!-- ======= About Section ======= -->
    <section id="about" class="about">
      <div class="container">

        <div class="section-title">
          <h2>About</h2>
          <p>Animaux aims at easing your Vet practice by handling your administrative work and most of your accounting.</p>
        </div>

        <div class="row">
          <div class="col-lg-4" data-aos="fade-right">
            <img src="assets/img/animaux.jpg" class="img-fluid" alt="">
          </div>
          <div class="col-lg-8 pt-4 pt-lg-0 content" data-aos="fade-left">
            <h3>Some More Details About Us</h3>
            <p class="fst-italic">
              "Throughout history, it has been the inaction of those who could have acted; the indifference of those who should have known better; the silence of the voice of justice when it mattered most; that has made it possible for evil to triumph."- Haile S.
            </p>
            <div class="row">
              <div class="col-lg-6">
                <ul>
                  <!-- <li><i class="bi bi-chevron-right"></i> <strong>Birthday:</strong> <span>1 May 1995</span></li> -->
                  <li><i class="bi bi-chevron-right"></i> <strong>Website:</strong> <span>www.animaux.co.zw</span></li>
                  <li><i class="bi bi-chevron-right"></i> <strong>Phone:</strong> <span>+263 772 119 106</span></li>
                  <li><i class="bi bi-chevron-right"></i> <strong>Town:</strong> <span>Mutare, Zimbabwe</span></li>
                </ul>
              </div>
              <div class="col-lg-6">
                <ul>
                  <!-- <li><i class="bi bi-chevron-right"></i> <strong>Age:</strong> <span>30</span></li> -->
                  <li><i class="bi bi-chevron-right"></i> <strong>WhatsApp No:</strong> <span>+263 771 222 039</span></li>
                  <li><i class="bi bi-chevron-right"></i> <strong>Email:</strong> <span>info@animaux.co.zw</span></li>
                  <li><i class="bi bi-chevron-right"></i> <strong>Address:</strong> <span>21 Montgomery Ave. </span></li>
                </ul>
              </div>
            </div>
           
          </div>
        </div>

      </div>
    </section><!-- End About Section -->

    <!-- ======= Facts Section ======= -->
    


    
    <!-- ======= Contact Section ======= -->
    <section id="contact" class="contact">
      <div class="container">

        <div class="section-title">
          <h2>Book for consultation:</h2>
          <p></p>
        </div>

        <div class="row" data-aos="fade-in">

          <div class="col-lg-5 d-flex align-items-stretch">
            <div class="info">
              <div class="address">
                <i class="bi bi-geo-alt"></i>
                <h4>Location:</h4>
                <p>21 Montgomery Ave. Bordervale Mutare</p>
              </div>

              <div class="email">
                <i class="bi bi-envelope"></i>
                <h4>Email:</h4>
                <p>helpdesk@animaux.co.zw</p>
              </div>

              <div class="phone">
                <i class="bi bi-phone"></i>
                <h4>Call:</h4>
                <p>+263 77 211 9106</p>
              </div>
              <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3798.170365723749!2d31.04686391488265!3d-17.830646787814473!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1931a4e5b43c8e79%3A0x855e6cc505590ead!2sThree%20Anchor%20House%2C%20Jason%20Moyo%20Ave%2C%20Harare!5e0!3m2!1sen!2szw!4v1617338885648!5m2!1sen!2szw" width="100%" height="290" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>

          </div>

          <div class="col-lg-7 mt-5 mt-lg-0 d-flex align-items-stretch">
            <form action="sendemail.php" method="POST" role="form" class="php-email-form">
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="name">Your Name</label>
                  <input type="text" name="name" class="form-control" id="name" required>
                </div>
                <div class="form-group col-md-6">
                  <label for="name">Your Email</label>
                  <input type="email" class="form-control" name="email" id="email" required>
                </div>
              </div>
              <div class="form-group">
                <label for="name">Subject</label>
                <input type="text" class="form-control" name="subject" id="subject" required>
              </div>
              <div class="form-group">
                <label for="name">Message</label>
                <textarea class="form-control" name="message" rows="10" required></textarea>
              </div>
              <div class="my-3">
                <div class="loading">Loading</div>
                <div class="error-message"></div>
                <div class="sent-message">Your message has been sent. Thank you!</div>
              </div>
              <div class="text-center"><button type="submit">Send Message</button></div>
            </form>
          </div>

        </div>

      </div>
    </section><!-- End Contact Section -->

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer">
    <div class="container">
      <div class="copyright">
        &copy; Copyright <strong><span>Infinity Lines of Code</span></strong>
      </div>
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/iportfolio-bootstrap-portfolio-websites-template/ -->
        Designed by <a href="https://infinitylinesofcode.com/">iL.O.C</a>
      </div>
    </div>
  </footer><!-- End  Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/purecounter/purecounter.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/typed.js/typed.min.js"></script>
  <script src="assets/vendor/waypoints/noframework.waypoints.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>




</html>