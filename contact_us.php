<?php 


session_start(); 
include 'Database/db.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Contact Us - iTech Store</title>
    <link rel="stylesheet"
          href="Style/style.css">

</head>

<?php
include 'Includes/header.php';
?>

<body>

<!-- Main page container -->
<main class="main-container contact-box-layout">

    <div class="contact-content">

        <h1>Contact Us</h1>

        <!-- Subtitle -->
        <p class="subtitle">
            We'd love to hear from you. Reach out anytime!
        </p>

        <!-- Contact information cards -->
        <div class="contact-cards-grid">

        <div class="c-card">
                <strong>Address:</strong>
                Dammam, Saudi Arabia
            </div>

            <div class="c-card">
                <strong>Phone:</strong>
                +966 500000000
            </div>

            <div class="c-card">
                <strong>Email:</strong>
                support@itechstore.com
            </div>

            <div class="c-card">
                <strong>Hours:</strong>
                Sat – Thu: 9AM – 10PM
            </div>

        </div>

        <hr class="divider">

        <div class="contact-form-section">

            <h2>Send Us a Message</h2>

            <form id="contactForm">
                <div class="input-group">

                    <input type="text"
                           id="name"
                           placeholder="Your Name">

                    <span id="nameError"
                          class="error-msg"></span>

                </div>

                <div class="input-group">

                    <input type="email"
                           id="email"
                           placeholder="Your Email">

                    <span id="emailError"
                          class="error-msg"></span>

                </div>

                <div class="input-group">

                    <textarea id="message"
                              placeholder="Write your message..."></textarea>

                    <span id="messageError"
                          class="error-msg"></span>

                </div>

                <button type="submit"
                        class="btn-cart">
                    Send Message
                </button>

            </form>

        </div>

        <div class="map-section">

            <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d114389.2612739174!2d50.04616238865618!3d26.37125345753063!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e49fb067bc89d7b%3A0x6001711681a81d45!2sDammam%20Saudi%20Arabia!5e0!3m2!1sen!2ssa!4v1700000000000!5m2!1sen!2ssa"
            class="map-frame"
            loading="lazy"></iframe>

        </div>

        <a href="index.php"
           class="back-link">
           ← Back to Shopping
        </a>

    </div>

</main>

<!-- Website footer -->
<footer class="footer">
    <div class="footer-container">
        <div class="footer-section">

            <h3>About iTech Store</h3>

            <p>
                Your premium destination for the latest
                technology and genuine Apple products
                in Saudi Arabia.
            </p>

        </div>

        <div class="footer-section">

            <h3>Quick Links</h3>

            <a href="index.php">Home</a>

            <a href="contact_us.php">Contact Us</a>

        </div>

        <!-- Contact information -->
        <div class="footer-section">

            <h3>Contact Info</h3>

            <p>Dammam, Saudi Arabia</p>

            <p>support@itechstore.com</p>

        </div>

    </div>

    <div class="footer-bottom">

        <p>
            &copy; 2026 iTech Store | All Rights Reserved
        </p>

    </div>

</footer>

<script>

function validateContactForm() {

    var valid = true;

    var name =
    document.getElementById("name").value.trim();

    var email =
    document.getElementById("email").value.trim();

    var message =
    document.getElementById("message").value.trim();

    document.getElementById("nameError").textContent = "";
    document.getElementById("emailError").textContent = "";
    document.getElementById("messageError").textContent = "";

    if (name === "") {

        document.getElementById("nameError").textContent =
        "Name is required.";

        valid = false;

    } else if (/^\d/.test(name)) {

        document.getElementById("nameError").textContent =
        "Name must not start with a number.";

        valid = false;

    } else if (!/^[A-Za-z\s]+$/.test(name)) {

        document.getElementById("nameError").textContent =
        "Name must contain letters only.";

        valid = false;

    } else if (name.length < 3) {

        document.getElementById("nameError").textContent =
        "Name must be at least 3 characters.";

        valid = false;
    }

    // Email format pattern
    var emailPattern =
    /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;

    if (email === "") {

        document.getElementById("emailError").textContent =
        "Email is required.";

        valid = false;

    } else if (!email.match(emailPattern)) {

        document.getElementById("emailError").textContent =
        "Enter a valid email.";

        valid = false;
    }

    // Validate message
    if (message === "") {

        document.getElementById("messageError").textContent =
        "Message is required.";

        valid = false;

    } else if (message.length < 10) {

        document.getElementById("messageError").textContent =
        "Message must be at least 10 characters.";

        valid = false;
    }

    // Show success message
    if (valid) {

        alert("Message sent successfully!");

        document.getElementById("contactForm").reset();
    }

    return false;
}

// Run validation when form submitted
document.getElementById("contactForm")
.addEventListener("submit", function(e) {

    // Stop page refresh
    e.preventDefault();

    // Run validation
    validateContactForm();

});

</script>

</body>
</html>
