<?php
require_once("config.php");
require_once("functions.php");

tokenVerify($dbHost, $dbName, $dbUser, $dbPass);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Plan your event</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/event.css'>
</head>
<body>
    <?php
    
    include "header.php"; 
    ?>
    <div class="form">
    <h1 class="mainTitle">Plan your event!</h1>
    <form action="handler.php" method="post" enctype="multipart/form-data">
        <label for="photo" class="image">Image for your event!</label>
        <br>
        <input type="file" name="photo" id="img" accept="image/png, image/jpeg" onchange="previewImage(event)">
        <img id="imgPreview" alt="Image Preview">
        <?php
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            unset($_SESSION['message']); 
        }
    ?>
        <br><br>

        <label for="title" class="title">Name of your event:</label>
        <br>
        <input type="text" name="title" id="tit">
        <?php
        if (isset($_SESSION['message2'])) {
            echo $_SESSION['message2'];
            unset($_SESSION['message2']); 
        }
    ?>
        <br><br>

        <label for="number" class="number">Number of attendees:</label>
        <br>
        <input type="number" name="number" id="num" max="1000" min="0">
        <?php
        if (isset($_SESSION['message3'])) {
            echo $_SESSION['message3'];
            unset($_SESSION['message3']);
        }
    ?>
        <br><br>

        <div class="dates">
        <label for="startDate">Event starts: </label>
        <input type="date" name="startDate" id="sDate">
        <br>
        <label for="endDate">Event starts: </label>
        <input type="date" name="endDate" id="eDate">
        <br>
        <?php
        if (isset($_SESSION['message7'])) {
            echo $_SESSION['message7'];
            unset($_SESSION['message7']);
        }elseif (isset($_SESSION['message8'])) {
            echo $_SESSION['message8'];
            unset($_SESSION['message8']);
        }
        ?>
        </div>
        <br><br>

        <label for="type" class="type">Type of event:</label>
        <br>
        <select name="type" id="typ" onchange="showInput()">
            <option value="" disabled selected hidden>-- Select an option --</option>
            <option value="concert">Concert</option>
            <option value="wedding">Wedding</option>
            <option value="birthday">Birthday</option>
            <option value="other">Other...</option>
        </select>
        <?php
        if (isset($_SESSION['message4'])) {
            echo $_SESSION['message4'];
            unset($_SESSION['message4']); 
        }
    ?>
        <br><br>

        <label for="other" id="other_lbl" class="other">Please, enter the type of event:</label>
        <br>
        <input type="text" name="other" id="other_txt">
        <br><br>

        <label for="eventCity">Where is the event held: </label>
        <input type="text" name="eventCity" id="city" placeholder="City name">
        <input type="text" name="eventStreet" id="street" placeholder="Street name">
        <input type="text" name="eventHouse" id="house" placeholder="House Number">
        <br><br>

        <label for="eventDesc">Write about the event: </label>
        <br>
        <textarea name="eventDesc" id="desc" cols="95" rows="8"></textarea>

        <br>
        <div class="chckboxDiv">
        <label for="public" class="chckboxLabel">Public event?</label>
        <input type="checkbox" name="public" id="pub" class="chckbox">
        </div>
        <br><br>

        <input type="submit" value="Send">
        <input type="reset" value="Cancel">
    </form>

    <script>
        function showInput() {
            const dropdown = document.getElementById('typ');
            const otherLabel = document.getElementById('other_lbl');
            const textInput = document.getElementById('other_txt');

            if (dropdown.value === 'other') {
                otherLabel.style.display = 'inline';
                textInput.style.display = 'inline';
            } else {
                otherLabel.style.display = 'none';
                textInput.style.display = 'none';
            }
        }

        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('imgPreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    </div>
    <?php
        include 'footer.php';
    ?>
</body>
</html>
