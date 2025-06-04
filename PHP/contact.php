<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact | MMM</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/index.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="main">
    <h1>Get in Touch</h1>
    <h3>We'd love to hear from you!</h3>

    <p class="parag" style="max-width: 60%">
        Whether you're planning the wedding of your dreams or just have a question about our services, we're here to help. Fill out the form below or reach out to us via email or phone. We aim to respond within 24 hours.
    </p>

    <form style="max-width: 500px; margin: 0 auto; text-align: left; color: white;">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" style="width: 100%; padding: 10px;"><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" style="width: 100%; padding: 10px;"><br><br>

        <label for="message">Message:</label><br>
        <textarea id="message" name="message" rows="5" style="width: 100%; padding: 10px;"></textarea><br><br>

        <button type="submit" style="padding: 10px 20px; background-color: #f34213; color: white; border: none; cursor: pointer; margin-bottom: 10px;">
            Send Message
        </button>
    </form>
</div>

<div class="second" style="margin-top: 1%">
    <h2>Our Contact Info</h2>
    <p>Email: support@mmmweddings.com</p>
    <p>Phone: +36 1 234 5678</p>
    <p>Address: 1011 Budapest, Romantic Lane 5.</p>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
