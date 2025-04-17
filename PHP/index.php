<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>MMM</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' href='../assets/css/index.css'>
    </head>
<body>

<!-- Ajándék választási visszajelzés a főoldalon -->

<?php include 'header.php'; ?>
<?php
if (isset($_SESSION['flash_error'])): ?>
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
        <h1>Welcome to the website!</h1>
        <h3>Where your dream wedding or fun weekend can come to life!</h3>
        <br><br>
        <p class="parag">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus tincidunt ultricies turpis vitae vestibulum. Aenean feugiat, augue faucibus eleifend euismod, tellus nisl vestibulum ex, a ullamcorper nulla magna a mi. Vestibulum dapibus et turpis sit amet pretium. Cras suscipit arcu eget nisl pretium varius. Nulla elit dui, pulvinar eget auctor maximus, consectetur pulvinar erat. Quisque rhoncus non sapien vitae dignissim. Donec id malesuada risus, sed volutpat nulla. Integer volutpat magna in mi imperdiet faucibus. Etiam eu vehicula felis. Integer ornare ex id erat lobortis rutrum. Nullam consectetur tristique placerat. Fusce iaculis, felis vitae fermentum efficitur, turpis elit facilisis augue, et dignissim arcu est sed risus.
            Quisque elementum arcu eget imperdiet vulputate. Nam tempor consequat metus, sed cursus turpis hendrerit ac. Quisque vitae viverra dolor. Etiam pellentesque fermentum nisl sed consequat. Phasellus tincidunt nibh fermentum euismod sodales. Duis cursus elit et dictum aliquet. Maecenas sed odio ac erat rhoncus faucibus in in nibh. Nulla risus nibh, lacinia a auctor ut, sodales id nisl. Donec lacinia justo urna, vel dignissim tellus rutrum vel.
        </p>
    </div>
</div>
<div class="second">
  <h1>About our company</h1>
  <p>This is gonna be the example text hwre text is gonna go.</p>
  <p>Testing testing one two three</p>
</div>
<div class="third">
    <?php include 'slideshow.php'; ?>
</div>
<?php
include 'footer.php';
?>
</body>
</html>