<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>MMM | Home</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' href='/assets/css/index.css'>
</head>
<body>

<?php include 'PHP/header.php'; ?>

<?php if (isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin: 1%">
        <?= $_SESSION['flash_error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin: 1%">
        <?= $_SESSION['flash_success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<div class="main">
    <div>
        <h1 style="font-size: 3em">Welcome to MMM Events</h1>
        <h3 style="font-weight: normal; margin-top: 20px">Elegance. Joy. Unforgettable Moments.</h3>
        <br><br>
        <p class="parag">
            At MMM, we believe your event should be nothing short of extraordinary. Whether you dream of a fairytale celebration under the stars or an intimate rustic weekend retreat, we bring your vision to life. With years of experience, a network of elite vendors, and a passion for perfection, our team will orchestrate every detail with love and care. <br><br>
            Discover how we turn dreams into reality — one beautiful moment at a time.
        </p>
    </div>
</div>

<div class="second">
    <h1>About Our Company</h1>
    <p style="max-width: 800px; margin: 0 auto; line-height: 28px">
        MMM was born from a desire to elevate life-s most cherished events. From weddings and birthday party weekends to unique private gatherings, we craft unforgettable experiences. Our founders — passionate planners and designers — envisioned a brand that blends timeless elegance with modern creativity.
    </p>
</div>

<div class="third">
    <?php include 'PHP/slideshow.php'; ?>
</div>

<?php include 'PHP/footer.php'; ?>

</body>
</html>